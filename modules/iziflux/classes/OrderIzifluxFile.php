<?php
/**
 *  2016 Profileo
 *
 *  @author    Profileo <contact@profileo.com>
 *  @copyright 2016 Profileo
 *  @license   Profileo
 *  @link  http://www.profileo.com
 */

if (! defined('_PS_VERSION_')) {
    exit();
}

/**
 * Class to load prestashop object model for order_iziflux_file table
 */
class OrderIzifluxFile extends ObjectModel
{

    public $file;

    public $date_add;

    /**
     *
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'order_iziflux_file',
        'primary' => 'id',
        'fields' => array(
            'file' => array(
                'type' => self::TYPE_STRING
            ),
            'date_add' => array(
                'type' => self::TYPE_DATE
            )
        )
    );

    /**
     * Function to check if file exists
     *
     * @param string $file
     */
    public static function iziFluxOrderFileExist($file)
    {
        $sql = 'SELECT a.file FROM `' . _DB_PREFIX_ . 'order_iziflux_file` a WHERE a.file = "' . pSQL($file) . '"';

        $res = Db::getInstance()->executeS($sql);
        return (isset($res[0]['file']) && $res[0]['file']) ? $res[0]['file'] : 0;
    }
}
