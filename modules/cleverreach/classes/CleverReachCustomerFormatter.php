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

include_once _PS_MODULE_DIR_ . 'cleverreach/classes/DataModel.php';

class CleverReachCustomerFormatter
{
    /**
     * @var ConfigModel
     */
    private $model;

    public function __construct()
    {
        $this->model = new ConfigModel();
    }

    /**
     * Format customer's data from Customer's object
     *
     * @param Customer $customer
     * @returns array
     */
    public function formatFromCustomerObject($customer)
    {
        $result = array();
        $map = array('email', 'active', 'date_add', 'newsletter', 'id_gender', 'firstname', 'lastname');

        foreach ($customer as $key => $customerField) {
            if (in_array($key, $map)) {
                $result[$key] = $customerField;
            } elseif ($key == 'id') {
                $result['id_customer'] = $customerField;
            }
        }

        return $result;
    }

    /**
     * Returns a formatted data for customer
     *
     * @param array $customer
     * @param bool $import
     * @return array
     */
    public function getFormattedCustomerData($customer, $import = false)
    {

        if ($this->model->isEnabledDebugMode()) {
            Logger::addLog('Email: ' . $customer['email']);
            Logger::addLog('Registered: ' . strtotime($customer['date_add']));
        }

        $activated = strtotime($customer['date_add']);

        if (!$customer['active'] || !$customer['newsletter']) {
            $activated = 0;
        }

        if (!$import && $this->model->getDOIStatus()) {
            $activated = 0;
        }

        return array(
            'email' => $customer['email'],
            'registered' => strtotime($customer['date_add']),
            'activated' => $activated,
            'deactivated' => $activated == 0 ? time() : 0,
            'source' => Context::getContext()->shop->name . ' PrestaShop export',
            'attributes' => $this->formatAttributes($customer),
            'orders' => $this->formatOrdersData($customer),
        );
    }

    /**
     * Returns formatted attributes data for given customer
     *
     * @param array $customer
     * @return array
     */
    private function formatAttributes($customer)
    {
        $gender = null;

        // if gender is 1, the customer is male, if 2 than female
        if ($customer['id_gender'] == 1) {
            $gender = 'Mr';
        } elseif ($customer['id_gender'] == 2) {
            $gender = 'Mrs';
        }

        $attributesData = array(
            'store' => Context::getContext()->shop->name,
            'salutation' => $gender,
            'firstname' => $customer['firstname'],
            'lastname' => $customer['lastname'],
            'street' => isset($customer['address']) ? $customer['address'] : null,
            'postal_number' => isset($customer['postcode']) ? $customer['postcode'] : null,
            'city' => isset($customer['city']) ? $customer['city'] : null,
            'country' => isset($customer['country']) ? $customer['country'] : null,
            'prestashop_shops' => isset($customer['shops']) ? $customer['shops'] : null,
            'prestashop_groups' => isset($customer['groups']) ? $customer['groups'] : null,
        );

        return $attributesData;
    }

    /**
     * Returns formatted orders data for given customer
     *
     * @param
     * @return array
     */
    private function formatOrdersData($customer)
    {
        $ordersData = array();
        $orders = Order::getCustomerOrders((int)$customer['id_customer']); //get orders for customer

        foreach ($orders as $order) {
            $orderObj = new Order((int)$order['id_order']);
            $products = $orderObj->getProducts();
            foreach ($products as $item) {
                $ordersData[] = array(
                    'order_id' => $order['id_order'],
                    'product' => $item['product_name'],
                    'product_id' => $item['product_id'],
                    'price' => $item['product_price'],
                    'currency' => Context::getContext()->currency->iso_code,
                    'amount' => $item['product_quantity'],
                );
            }
        }

        return $ordersData;
    }

    /**
     * Returns a formatted data for guest subscriber
     *
     * @param array $subscriber
     * @param bool $import
     * @return array
     */
    public function getFormattedSubscriberData($subscriber, $import = false)
    {
        $activated = strtotime($subscriber['newsletter_date_add']);

        if (!$import && (!$subscriber['active'] || $this->model->getDOIStatus())) {
            $activated = 0;
        }

        // On initial import of subscribers don't send DOI email foreach customer, they already did it
        if ($import && !$subscriber['active']) {
            $activated = 0;
        }

        return array(
            'email' => $subscriber['email'],
            'registered' => strtotime($subscriber['newsletter_date_add']),
            'activated' => $activated,
            'deactivated' => $activated == 0 ? time() : 0,
            'source' => Context::getContext()->shop->name . ' PrestaShop export',
            'attributes' => array(
                'prestashop_shops' => $subscriber['shop'],
                'prestashop_groups' => 'Guest subscriber',
            ),
            'orders' => array(),
        );
    }
}
