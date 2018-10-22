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

class DataModel
{
    /**
     * Gets shop ids for shop group id
     *
     * @param int $shopGroupId
     * @return array
     */
    public function getShopIdsForGroup($shopGroupId)
    {
        $result = array();

        $shopObj = new Shop();
        $shops = $shopObj->getShopsCollection(true, $shopGroupId)->getAll();
        foreach ($shops as $shop) {
            $result[] = (int)$shop->id;
        }

        return $result;
    }

    /**
     * Count all customers
     *
     * @param array $shopIds List of shop IDs
     * @return int
     */
    public function getCustomerCount($shopIds = null)
    {
        $query = 'SELECT COUNT(*) as total FROM `' . _DB_PREFIX_ . 'customer`';

        if (!empty($shopIds)) {
            $query .= ' WHERE `id_shop` IN (' . $this->escape($shopIds) . ') ';
        }

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);
    }

    /**
     *  Count all guest subscribers
     *
     * @param array $shopIds
     * @return int
     */
    public function getGuestSubscriberCount($shopIds = null)
    {
        $table = _PS_VERSION_ >= '1.7.0.0' ? 'emailsubscription' : 'newsletter';

        $query = 'SELECT COUNT(*) as total FROM `' . _DB_PREFIX_ . pSQL($table) . '`';

        if (!empty($shopIds)) {
            $query .= ' WHERE `id_shop` IN (' . $this->escape($shopIds) . ') ';
        }

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);
    }

    /**
     * @param int $limit
     * @param int $offset
     * @param array $shopIds
     * @return array
     * @throws PrestaShopDatabaseException
     */
    public function getSubscribers($limit, $offset, $shopIds)
    {
        $table = _PS_VERSION_ >= '1.7.0.0' ? 'emailsubscription' : 'newsletter';

        $query = 'SELECT sub.`id`, sub.`id_shop`, sub.`email`, sub.`newsletter_date_add`, sub.`active`, s.`name` as shop
                  FROM `' . _DB_PREFIX_ . pSQL($table) . '` as sub 
                  LEFT JOIN `' . _DB_PREFIX_ . 'shop` as s ON s.`id_shop` = sub.`id_shop` ';

        if (!empty($shopIds)) {
            $query .= 'WHERE sub.`id_shop` IN (' . $this->escape($shopIds) . ') ';
        }

        $query .= 'LIMIT ' . (int)$limit . ' OFFSET ' . (int)$offset;

        $subscribers = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);

        return $subscribers;
    }

    /**
     * Ges customers with additional data (address information, shops and groups)
     *
     * @param int $limit
     * @param int $offset
     * @param array $shopIds
     * @return array
     */
    public function getCustomers($limit, $offset, $shopIds)
    {
        $customersFormatted = array();
        $additional = array();
        $customerIds = array();

        $query = 'SELECT c.`id_customer`, c.`id_shop_group`, c.`id_shop`, c.`id_gender`, c.`firstname`, c.`lastname`, 
                    c.`email`, c.`newsletter`, c.`date_add`, c.`active`  
                  FROM `' . _DB_PREFIX_ . 'customer` AS c ';

        if (!empty($shopIds)) {
            $query .= 'WHERE c.`id_shop` IN (' . $this->escape($shopIds) . ') ';
        }

        $query .= 'LIMIT ' . (int)$limit . ' OFFSET ' . (int)$offset;

        $customers = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);

        foreach ($customers as $customer) {
            array_push($customerIds, (int)$customer['id_customer']);
            $customersFormatted[$customer['id_customer']] = $customer;
        }

        // if there are customers, get additional data for them
        if (!empty($customerIds)) {
            // get address information for customer ids
            $additional = $this->getAdditionalDataForCustomers($customerIds);

            // get shop information for customer ids
            $shops = $this->getShops($customerIds);

            // get group information for $customer ids
            $groups = $this->getGroups($customerIds);
        }

        // merge additional data with customers array
        foreach ($customersFormatted as $key => $customer) {
            if (isset($additional[$key])) {
                $customersFormatted[$key] = array_merge($customer, $additional[$key]);
            }

            if (isset($shops[$key])) {
                $customersFormatted[$key]['shops'] = $shops[$key];
            }

            if (isset($groups[$key])) {
                $customersFormatted[$key]['groups'] = $groups[$key];
            }
        }

        return $customersFormatted;
    }

    /**
     * Get address information for customer ids
     *
     * @param array $customerIds
     * @return array
     * @throws PrestaShopDatabaseException
     */
    public function getAdditionalDataForCustomers($customerIds)
    {
        $query = 'SELECT a.`id_customer`, a.`address1` AS address, a.`postcode`, a.`city`, cl.`name` AS `country` 
                  FROM `' . _DB_PREFIX_ . 'address` AS a 
                  LEFT JOIN `' . _DB_PREFIX_ . 'country_lang` AS cl ON a.`id_country` = cl.`id_country`
                  WHERE a.`id_customer` IN (' . $this->escape($customerIds) . ') 
                  AND a.`active` = 1 AND cl.`id_lang` = ' . (int)Context::getContext()->language->id . '
                  GROUP BY a.`id_customer`';

        $addresses = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);

        return $this->formatById($addresses);
    }

    /**
     * Get shops for customer ids
     *
     * @param array $customerIds
     * @return array
     * @throws PrestaShopDatabaseException
     */
    public function getShops($customerIds)
    {
        $query = 'SELECT c.`id_customer`, c.`id_shop`, s.`name` as `value`, sg.`share_customer`, sg.`id_shop_group` 
                  FROM `' . _DB_PREFIX_ . 'customer` AS c 
                  LEFT JOIN `' . _DB_PREFIX_ . 'shop` as s ON s.`id_shop` = c.`id_shop` 
                  LEFT JOIN `' . _DB_PREFIX_ . 'shop_group` as sg ON sg.`id_shop_group` = s.`id_shop_group`
                  WHERE c.`id_customer` IN (' . $this->escape($customerIds) . ') AND s.`active` = 1';

        $shops = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);

        $shops = $this->addSharedShops($shops);

        return $this->formatById($shops, true);
    }

    /**
     * Get customer groups for customer ids.
     *
     * @param array $customerIds
     * @return array
     * @throws PrestaShopDatabaseException
     */
    public function getGroups($customerIds)
    {
        $query = 'SELECT cg.`id_customer`, gl.`name` as `value` 
                  FROM `' . _DB_PREFIX_ . 'customer_group` AS cg 
                  LEFT JOIN `' . _DB_PREFIX_ . 'group_lang` as gl ON gl.`id_group` = cg.`id_group`
                  WHERE cg.`id_customer` IN (' . $this->escape($customerIds) . ') 
                  AND gl.`id_lang` = ' . (int)Context::getContext()->language->id;

        $groups = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);

        return $this->formatById($groups, true);
    }

    /**
     * Add Shared Shops
     *
     * @param array $shops
     * @return array $result
     */
    private function addSharedShops($shops = array())
    {
        $result = $shops;

        foreach ($shops as $shop) {
            if ($shop['share_customer'] === '1') {
                // if shop is in the shared group, get other shops from same group
                $shopObj = new Shop();
                $sharedShops = $shopObj->getShopsCollection(true, $shop['id_shop_group'])->getAll();
                foreach ($sharedShops as $sharedShop) {
                    if ($sharedShop->id != $shop['id_shop']) {
                        $shop['value'] = $sharedShop->name;
                        $result[] = $shop;
                    }
                }
            }
        }

        return $result;
    }

    /**
     * Returns array with customer id as array key
     *
     * @param array $data
     * @param bool $implode
     * @return array
     */
    private function formatById($data, $implode = false)
    {
        $result = array();

        foreach ($data as $d) {
            $id = $d['id_customer'];
            if ($implode) {
                $result[$id] = isset($result[$id]) ? $result[$id] . ',' . $d['value'] : $d['value'];
            } else {
                $result[$id] = $d;
            }
        }

        return $result;
    }

    private function escape(array $ids)
    {
        return implode(', ', array_map('intval', $ids));
    }
}
