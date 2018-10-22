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
 * Class to load prestashop object model for order_iziflux table
 */
class OrderIziflux extends ObjectModel
{

    public $id_order;

    public $id_iziflux_order;

    public $id_product;

    public $reference_product_market;

    public $transaction;

    public $market_place;

    public $carrier_name;

    public $quantity;

    public $status;

    public $date_add;

    /**
     *
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'order_iziflux',
        'primary' => 'id_order_iziflux',
        'fields' => array(
            'id_order' => array(
                'type' => self::TYPE_INT
            ),
            'id_iziflux_order' => array(
                'type' => self::TYPE_STRING
            ),
            'id_product' => array(
                'type' => self::TYPE_INT
            ),
            'reference_product_market' => array(
                'type' => self::TYPE_STRING
            ),
            'transaction' => array(
                'type' => self::TYPE_STRING
            ),
            'market_place' => array(
                'type' => self::TYPE_STRING
            ),
            'carrier_name' => array(
                'type' => self::TYPE_STRING
            ),
            'quantity' => array(
                'type' => self::TYPE_INT
            ),
            'status' => array(
                'type' => self::TYPE_INT
            ),
            'date_add' => array(
                'type' => self::TYPE_DATE
            )
        )
    );

    /**
     * Function to check if iziflux order exists
     *
     * @param int $id_iziflux_order
     * @param string $reference_product_market
     */
    public static function iziFluxOrderExist($id_iziflux_order, $reference_product_market)
    {
        $sql = 'SELECT a.id_order FROM `' . _DB_PREFIX_ . 'order_iziflux` a
            WHERE a.id_iziflux_order = \'' . pSQL($id_iziflux_order) . "'
            AND reference_product_market = '" . pSQL($reference_product_market) . "'";

        $res = Db::getInstance()->executeS($sql);
        return (isset($res[0]['id_order']) && $res[0]['id_order']) ? $res[0]['id_order'] : 0;
    }

    public static function iziFluxNewOrder(
        $id_order,
        $id_izif_order,
        $id_product,
        $reference_product_mp,
        $transaction,
        $market_place,
        $carrier_name,
        $qty
    ) {
        $newOrderIziflux = new OrderIziflux();
        $newOrderIziflux->id_order = $id_order;
        $newOrderIziflux->id_iziflux_order = $id_izif_order;
        $newOrderIziflux->id_product = $id_product;
        $newOrderIziflux->reference_product_market = $reference_product_mp;
        $newOrderIziflux->transaction = $transaction;
        $newOrderIziflux->market_place = $market_place;
        $newOrderIziflux->carrier_name = $carrier_name;
        $newOrderIziflux->quantity = $qty;
        $newOrderIziflux->date_add = date('Y-m-d H:i:s');
        if (REGISTERDB == 'ON' && $id_order>"1") {
            $newOrderIziflux->save();
        }
    }
}
