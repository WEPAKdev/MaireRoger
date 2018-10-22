<?php
/**
 * 2017 CleverReach
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    CleverReach <partner@cleverreach.com>
 * @copyright 2017 CleverReach GmbH
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

include_once _PS_MODULE_DIR_ . 'cleverreach/classes/CleverReachApiClient.php';
include_once _PS_MODULE_DIR_ . 'cleverreach/classes/ConfigModel.php';
include_once _PS_MODULE_DIR_ . 'cleverreach/classes/DataModel.php';
include_once _PS_MODULE_DIR_ . 'cleverreach/classes/Data.php';
include_once _PS_MODULE_DIR_ . 'cleverreach/classes/BackgroundProcess.php';
include_once _PS_MODULE_DIR_ . 'cleverreach/classes/CleverReachCustomerFormatter.php';
include_once _PS_MODULE_DIR_ . 'cleverreach/classes/CleverReachUtility.php';

ignore_user_abort(true);
set_time_limit(3600);

class CleverReachImportModuleFrontController extends ModuleFrontController
{
    /**
     * @var int
     */
    private $currentProgress;

    /**
     * @var ConfigModel
     */
    private $model;

    /**
     * @var BackgroundProcess
     */
    private $backgroundProcessHelper;

    /**
     * @var CleverReachApiClient
     */
    private $apiClient;

    /**
     * @var CleverReachCustomerFormatter
     */
    private $formatter;

    /**
     * @var dataModel
     */
    private $dataModel;

    /**
     * @var Data
     */
    private $data;

    public function __construct()
    {
        $this->currentProgress = 0;
        $this->apiClient = new CleverReachApiClient();
        $this->model = new ConfigModel();
        $this->backgroundProcessHelper = new BackgroundProcess();
        $this->formatter = new CleverReachCustomerFormatter();
        $this->dataModel = new DataModel();
        $this->data = new Data();

        parent::__construct();
    }

    public function initContent()
    {
        // check if we have password in request
        // BackgroundProcess sends encrypted password
        if (Tools::getValue('password') !== md5($this->model->getProductEndpointPassword())) {
            header($_SERVER['SERVER_PROTOCOL'] . ' 401 Unauthorized');
            header('WWW-Authenticate: Password"');
            die('401 Unauthorized');
        }

        $offset = Tools::getValue('startFrom');
        $limit = Tools::getValue('limit');
        $groupId = Tools::getValue('groupId');
        $shopGroupId = Tools::getValue('shopGroupId');
        $groupMappings = $this->model->getGroupMappings();
        $listId = 0;
        $shopIds = array();

        if (isset($groupMappings[$groupId])) {
            $listId = $groupMappings[$groupId]['crGroup'];
        }

        if (!empty($shopGroupId)) {
            $shopIds = $this->dataModel->getShopIdsForGroup($shopGroupId);
        }

        $customers = array_merge(
            $this->prepareSubscribers($limit, $offset, $shopIds),
            $this->prepareCustomers($limit, $offset, $shopIds)
        );

        $this->currentProgress = is_null($this->model->getImportProgress()) ? 0 : $this->model->getImportProgress();

        $this->apiClient->setAuthMode('bearer', $this->model->getAccessToken());

        // adding attributes to the CleverReach list
        if ($offset == 0) {
            $this->addAttributes($this->apiClient, $listId);
        }

        // sending customers
        $this->importCustomers($listId, $customers);

        $this->currentProgress += count($customers);
        $this->model->setImportProgress($this->currentProgress);

        // if number of customers is not equal to total customers continue process
        if ($this->currentProgress != $this->model->getTotalCustomersForImport()) {
            if ($this->model->isEnabledDebugMode()) {
                Logger::addLog("continueProcess: Limit: $limit");
            }

            $this->continueProcess($offset + $limit, $limit, $groupId);
        } else {
            if ($this->model->isEnabledDebugMode()) {
                Logger::addLog("Import process finished");
            }

            $this->model->setImportEndTime(time());
            $this->model->setImportLocked(0);
        }

        CleverReachUtility::dieJson(
            array(
                'success' => true,
                'groupId' => $groupId,
                'limit' => $limit,
                'offset' => $offset,
            )
        );
    }

    /**
     * Sets parameters for next piece of customers, and starts background process for them.
     *
     * @param $offset
     * @param $limit
     * @param $groupId
     */
    private function continueProcess($offset, $limit, $groupId)
    {
        $this->backgroundProcessHelper->setOffset($offset)
            ->setContinue(true)
            ->setPassword($this->model->getProductEndpointPassword())
            ->setUrl($this->context->link->getModuleLink('cleverreach', 'import'))
            ->setLimit($limit)
            ->setGroupId($groupId)
            ->startBackgroundProcess();
    }

    /**
     * Returns properly formatted unregistered guest subscribers for sending to CleverReach
     *
     * @param int $limit
     * @param int $offset
     * @param array $shopIds
     * @return array
     */
    private function prepareSubscribers($limit, $offset, $shopIds)
    {
        $result = array();
        $subscribers = $this->dataModel->getSubscribers($limit, $offset, $shopIds);

        foreach ($subscribers as $subscriber) {
            $result[] = $this->formatter->getFormattedSubscriberData($subscriber, true);
        }

        return $result;
    }

    /**
     * Returns properly formatted customers for sending to CleverReach
     *
     * @param int $limit
     * @param int $offset
     * @param array $shopIds
     * @return array
     */
    private function prepareCustomers($limit, $offset, $shopIds)
    {
        $result = array();
        $customers = $this->dataModel->getCustomers($limit, $offset, $shopIds);

        foreach ($customers as $customer) {
            $result[] = $this->formatter->getFormattedCustomerData($customer, true);
        }

        return $result;
    }

    /**
     * Import/update users
     *
     * @param $listId
     * @param $customers
     *
     * @throws Exception
     */
    private function importCustomers($listId, $customers)
    {
        foreach ($customers as $customer) {
            $this->apiClient->setThrowExceptions(false);
            $recipient = $this->apiClient->get(
                '/groups.json/' . $listId . '/receivers/' .
                urlencode($customer['email'])
            );
            $this->apiClient->setThrowExceptions(true);
            try {
                if (!empty($recipient)) {
                    if (!$this->data->updateCustomerStatus($recipient['events'])) {
                        unset($customer['activated'], $customer['deactivated']);
                    }
                    $this->apiClient->put(
                        '/groups.json/' . $listId . '/receivers/' . urlencode($customer['email']),
                        $customer
                    );
                } else {
                    $this->apiClient->post('/groups.json/' . $listId . '/receivers', $customer);
                }
            } catch (\Exception $e) {
                Logger::addLog('Import of customer ' . $customer['email'] . ' failed: ' . $e->getMessage());
            }
        }
    }

    /**
     * Registers attributes for group
     *
     * @param CleverReachApiClient $apiClient
     * @param $listID
     *
     * @throws \Exception
     */
    private function addAttributes($apiClient, $listID)
    {
        $apiClient->setThrowExceptions(false);

        $apiClient->post("/groups.json/$listID/attributes", array('name' => 'store', 'type' => 'text'));
        $apiClient->post("/groups.json/$listID/attributes", array('name' => 'salutation', 'type' => 'text'));
        $apiClient->post("/groups.json/$listID/attributes", array('name' => 'firstname', 'type' => 'text'));
        $apiClient->post("/groups.json/$listID/attributes", array('name' => 'lastname', 'type' => 'text'));
        $apiClient->post("/groups.json/$listID/attributes", array('name' => 'street', 'type' => 'text'));
        $apiClient->post("/groups.json/$listID/attributes", array('name' => 'postal_number', 'type' => 'text'));
        $apiClient->post("/groups.json/$listID/attributes", array('name' => 'city', 'type' => 'text'));
        $apiClient->post("/groups.json/$listID/attributes", array('name' => 'country', 'type' => 'text'));
        $apiClient->post("/groups.json/$listID/attributes", array('name' => 'prestashop_shops', 'type' => 'text'));
        $apiClient->post("/groups.json/$listID/attributes", array('name' => 'prestashop_groups', 'type' => 'text'));

        $apiClient->setThrowExceptions(true);
    }
}
