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

include_once _PS_MODULE_DIR_ . 'cleverreach/classes/Abstract/ConfigAbstract.php';

class ConfigModel extends ConfigAbstract
{

    /**
     * Gets status for double opt in from system configuration
     *
     * @return bool
     */
    public function getDOIStatus()
    {
        $result = false;

        $groupMapping = $this->getGroupMappings();
        if ($groupMapping[1]['optInForm'] != 0) {
            $result = true;
        }

        return $result;
    }

    /**
     * Saves value to system
     *
     * @param string $name
     * @param mixed $value
     * @param bool $jsonEncode
     *
     * @return \PDOStatement|string
     */
    public function setConfigValue($name, $value, $jsonEncode = false)
    {
        $status = $this->getConfigValue($name);
        if ($status === null) {
            $result = Db::getInstance()->execute(
                'INSERT INTO ' . _DB_PREFIX_ . 'cleverreach (`name`, `value`, `created_at`, `modified_at`) 
                VALUES (\'' . pSQL($name) . '\',\'' . pSQL($value) . '\', NOW(), NOW())'
            );
        } else {
            $result = Db::getInstance()->execute(
                'UPDATE ' . _DB_PREFIX_ . 'cleverreach SET `value` = \'' . pSQL($value) . '\', `modified_at` = NOW() 
                WHERE `name` = \'' . pSQL($name) . '\''
            );
        }

        if ($jsonEncode) {
            $result = json_encode($result);
        }

        return $result;
    }

    /**
     * Returns a value from system database for given name
     *
     * @param string $name
     * @param null $default
     * @param bool $jsonDecode
     *
     * @return mixed
     */
    public function getConfigValue($name, $default = null, $jsonDecode = false)
    {
        $row = Db::getInstance()->getRow(
            'SELECT `value` FROM ' . _DB_PREFIX_ . 'cleverreach WHERE `name`=\'' . pSQL($name) . '\'',
            false
        );

        $result = isset($row['value']) ? $row['value'] : $default;

        if ($jsonDecode) {
            $result = json_decode($result, true);
        }

        return $result;
    }
}
