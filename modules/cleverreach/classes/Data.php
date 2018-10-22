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
include_once _PS_MODULE_DIR_ . 'cleverreach/classes/CleverReachCustomerFormatter.php';

class Data
{
    /**
     * Status for nonexistent product
     */
    const NO_PRODUCT = 8;

    /**
     * @var CleverReachApiClient
     */
    private $apiClient;

    /**
     * @var ConfigModel
     */
    private $model;

    /**
     * @var CleverReachCustomerFormatter
     */
    private $formatter;

    public function __construct()
    {
        $this->apiClient = new CleverReachApiClient();
        $this->model = new ConfigModel();
        $this->formatter = new CleverReachCustomerFormatter();
    }

    /**
     * Sends Double-Opt-In email
     *
     * @param $formId
     * @param $email
     * @param $groupsId
     *
     * @throws \Exception
     */
    public function sendDOIEmail($formId, $email, $groupsId)
    {
        $data = array(
            "email" => $email,
            "groups_id" => $groupsId,
            "doidata" => array(
                'user_ip' => $_SERVER['REMOTE_ADDR'],
                'referer' => $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'],
                'user_agent' => $_SERVER['HTTP_USER_AGENT'],
            ),
        );

        $this->apiClient->setAuthMode('bearer', $this->model->getAccessToken());
        $this->apiClient->post('/forms.json/' . $formId . '/send/activate', $data);
    }

    /**
     * Handle new user subscription to CleverReach
     *
     * @param $subscriberEmail
     * @param bool $unSubscribe
     */
    public function subscribeUser($subscriberEmail, $unSubscribe = false)
    {
        // get group mappings and list id
        $groupMappings = $this->model->getGroupMappings();
        $listId = $this->model->getMappedListId();

        if (empty($listId)) {
            return;
        }

        // format subscriber's data for sending to CleverReach
        $customerData = $this->formatter->getFormattedSubscriberData(
            array(
                'email' => $subscriberEmail,
                'active' => 0,
                'newsletter_date_add' => date('Y-m-d H:i:s'),
                'shop' => Context::getContext()->shop->name,
            )
        );

        try {
            $recipientsURL = '/groups.json/' . $listId . '/receivers';
            $this->apiClient->setAuthMode('bearer', $this->model->getAccessToken());

            $this->apiClient->setThrowExceptions(false);
            $this->apiClient->post($recipientsURL, $customerData);
            $this->apiClient->setThrowExceptions(true);
            $recipient = $this->apiClient->get($recipientsURL . '/' . urlencode($customerData['email']));

            $shouldUpdateStatus = $this->updateCustomerStatus($recipient['events']);
            $this->setCustomerStatusAndSendDOI(
                $listId,
                $groupMappings,
                $subscriberEmail,
                $shouldUpdateStatus,
                $unSubscribe
            );
        } catch (\Exception $e) {
            Logger::addLog('Exception on CustomerSubscribeObserver: ' . $e->getMessage());
        }
    }

    /**
     * @param string $listId
     * @param array $groupMappings
     * @param string $subscriberEmail
     * @param bool $shouldUpdateStatus
     * @param bool $unSubscribe
     */
    private function setCustomerStatusAndSendDOI(
        $listId,
        $groupMappings,
        $subscriberEmail,
        $shouldUpdateStatus,
        $unSubscribe = false
    ) {
        if ($shouldUpdateStatus) {
            $baseUrl = '/groups.json/' . $listId . '/receivers/' . urlencode($subscriberEmail);

            if ($unSubscribe) {
                $this->apiClient->put($baseUrl . '/setinactive');
            } else {
                if ($this->model->getDOIStatus()) {
                    $this->apiClient->put($baseUrl . '/setinactive');
                    $formId = $groupMappings[1]['optInForm'];

                    // if form id is not 0, then mail must be send only via CleverReach,
                    // otherwise it needs to be send via magento
                    if ($formId != 0) {
                        $this->sendDOIEmail($formId, $subscriberEmail, $listId);
                    }
                } else {
                    $this->apiClient->put($baseUrl . '/setactive');
                }
            }
        }
    }

    /**
     * Update customer in CleverReach
     *
     * @param Customer $data
     * @param DataModel $dataModel
     */
    public function updateCustomer($data, $dataModel)
    {
        // get group mappings and list id
        $groupMappings = $this->model->getGroupMappings();
        $listId = $this->model->getMappedListId();

        // get additional information needed for customer
        $additional = $dataModel->getAdditionalDataForCustomers(array($data->id));
        $shops = $dataModel->getShops(array($data->id));
        $groups = $dataModel->getGroups(array($data->id));

        // prepare customer's data
        $customer = $this->formatter->formatFromCustomerObject($data);
        $customer['shops'] = isset($shops[$customer['id_customer']]) ? $shops[$customer['id_customer']] : null;
        $customer['groups'] = isset($groups[$customer['id_customer']]) ? $groups[$customer['id_customer']] : null;

        if (isset($additional[$customer['id_customer']])) {
            $customer = array_merge($customer, $additional[$customer['id_customer']]);
        }

        // format customer's data for sending to CleverReach
        $customerData = $this->formatter->getFormattedCustomerData($customer);

        // set auth mode
        $this->apiClient->setAuthMode('bearer', $this->model->getAccessToken());

        $recipientsURL = '/groups.json/' . $listId . '/receivers';
        // send data to CleverReach
        $this->apiClient->setThrowExceptions(false);
        $recipient = $this->apiClient->get($recipientsURL . '/' . urlencode($customerData['email']));

        $this->apiClient->setThrowExceptions(true);

        try {
            $shouldUpdateStatus = true;
            if (empty($recipient)) {
                $this->apiClient->post($recipientsURL, $customerData);
            } else {
                $shouldUpdateStatus = $this->updateCustomerStatus($recipient['events']);
                if (!$shouldUpdateStatus) {
                    unset($customerData['activated'], $customerData['deactivated']);
                }

                $this->apiClient->put($recipientsURL . '/' . urlencode($customerData['email']), $customerData);
            }

            $this->setCustomerStatusAndSendDOI(
                $listId,
                $groupMappings,
                $customerData['email'],
                $shouldUpdateStatus,
                !(bool)$data->newsletter
            );
        } catch (\Exception $e) {
            Logger::addLog('Exception on UpdateCustomer: ' . $e->getMessage());
        }
    }

    /**
     * Send order to CleverReach
     *
     * @param Order $order
     * @param Cookie $cookie
     */
    public function sendOrder($order, $cookie)
    {
        $customer = $order->getCustomer();

        // get list id
        $listId = $this->model->getMappedListId();

        //get products from order
        $products = $order->getProducts();

        $productsViewedFromCleverReach = json_decode($cookie->campaignData, true);

        //send each product to CleverReach
        foreach ($products as $item) {
            $data = array(
                'group_id' => $listId,
                'order_id' => (string)$order->id,
                'product' => $item['product_name'],
                'product_id' => $item['product_id'],
                'quantity' => $item['product_quantity'],
                'price' => $item['product_price'],
                'stamp' => strtotime($order->date_add),
                'source' => Context::getContext()->shop->name . ' PrestaShop',
            );

            //check if product getting from CleverReach email campaign
            $products = $productsViewedFromCleverReach === null ? array() : $productsViewedFromCleverReach;
            if (array_key_exists($item['product_id'], $products)) {
                $data['mailings_id'] = $products[$item['product_id']];
            }

            try {
                if ($this->model->isEnabledDebugMode()) {
                    Logger::addLog('Sending order data: ' . print_r($data, true));
                }

                $this->apiClient->setAuthMode('bearer', $this->model->getAccessToken());
                $this->apiClient->post('/receivers.json/' . $customer->email . '/orders', $data);
            } catch (\Exception $e) {
                Logger::addLog('Exception on order: ' . $e->getMessage());
            }
        }
    }

    /**
     * Check if there's event with type 'user_unsubscribe'
     *
     * @param $events
     *
     * @return bool
     */
    public function updateCustomerStatus($events)
    {
        $result = true;

        foreach ($events as $event) {
            if ($event['type'] === 'user_unsubscribe') {
                $result = false;
                break;
            }
        }

        return $result;
    }
}
