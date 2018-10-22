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
include_once _PS_MODULE_DIR_ . 'cleverreach/classes/BackgroundProcess.php';
include_once _PS_MODULE_DIR_ . 'cleverreach/classes/CleverReachUtility.php';

class CleverReachImportController extends AdminController
{

    /**
     * @var ConfigModel
     */
    private $model;
    
    /**
     * @var ConfigModel
     */
    private $dataModel;

    /**
     * @var CleverReachApiClient
     */
    private $apiClient;

    /**
     * @var BackgroundProcess
     */
    private $backgroundProcessHelper;

    public function __construct()
    {
        $this->bootstrap = true;
        $this->model = new ConfigModel();
        $this->dataModel = new DataModel();
        $this->apiClient = new CleverReachApiClient();
        $this->backgroundProcessHelper = new BackgroundProcess();

        parent::__construct();
    }

    /**
     * Initialize content
     */
    public function initContent()
    {
        parent::initContent();
    }

    /**
     * Start import action
     *
     */
    public function displayAjaxImportStart()
    {
        // if not connected import cannot be started
        if (!$this->model->isConnected()) {
            CleverReachUtility::dieJson(
                array(
                    'status' => BackgroundProcess::IMPORT_ERROR,
                    'message' => $this->l('Process cannot be started'),
                )
            );
        }

        $importStartTime = $this->model->getImportStartTime();

        // check if import is locked
        if ($this->model->isImportLocked()
            && !is_null($importStartTime)
            && (($importStartTime + 24 * 60 * 60) > time())
        ) {
            CleverReachUtility::dieJson(
                array(
                    'status' => BackgroundProcess::IMPORT_LOCKED,
                    'message' => $this->l('Process already started'),
                )
            );
        }

        // get customer groups
        $customerGroups = $this->getCustomerGroups();

        //if empty there's nothing to be imported
        if (empty($customerGroups)) {
            CleverReachUtility::dieJson(
                array(
                    'status' => BackgroundProcess::NOTHING_TO_IMPORT,
                    'message' => $this->l('Nothing to import'),
                )
            );
        }

        // set configurations and start process
        $this->setImportConfigurations($customerGroups);
        $this->startProcess($customerGroups);

        CleverReachUtility::dieJson(
            array(
                'status' => BackgroundProcess::IMPORT_STARTED,
                'message' => $this->l('Process successfully started and will continue in background'),
            )
        );
    }

    /**
     * Check import progress action
     *
     */
    public function displayAjaxImportProgress()
    {
        CleverReachUtility::dieJson(
            array(
                'status' => $this->backgroundProcessHelper->setCount($this->model->getTotalCustomersForImport())
                    ->setCurrent($this->model->getImportProgress())
                    ->getCurrentState(),
            )
        );
    }

    /**
     * Returns sorted ids of customer groups
     *
     * @return array
     */
    private function getCustomerGroups()
    {
        $result = array();
        $mapping = $this->model->getGroupMappings();
        if ($mapping) {
            foreach ($mapping as $key => $value) {
                if ($value['crGroup'] != 0) {
                    $result[] = $key;
                }
            }
        }

        sort($result);

        return $result;
    }

    /**
     * Saves import configurations to database
     *
     * @param $customerGroups
     */
    private function setImportConfigurations($customerGroups)
    {
        $count = 0;

        if (in_array(1, $customerGroups)) {
            // check if guest subscribers needs to be imported
            $count += $this->dataModel->getGuestSubscriberCount();
            // check if customers needs to be imported
            $count += $this->dataModel->getCustomerCount();
        }

        $this->model->setImportLocked(1);
        $this->model->setImportProgress(0);
        $this->model->setTotalCustomersForImport($count);
        $this->model->setImportStartTime(time());
    }

    /**
     * Sets parameters and starts background import process
     *
     * @param $customerGroups
     */
    private function startProcess($customerGroups)
    {
        $this->backgroundProcessHelper->setPassword($this->model->getProductEndpointPassword())
            ->setShopGroupId(null)
            ->setUrl($this->context->link->getModuleLink('cleverreach', 'import'))
            ->setOffset(0)
            ->setLimit($this->model->getBatchSize())
            ->setGroupId($customerGroups[0])
            ->startBackgroundProcess();
    }
}
