<?php
/**
 *  2016 Profileo
 *
 *  @author    Profileo <contact@profileo.com>
 *  @copyright 2016 Profileo
 *  @license   Profileo
 *  @link  http://www.profileo.com
 */

include_once(dirname(__FILE__) . '../../lib/SpecificCustomers.php');

/**
 * Class to load utility tools for iziflux module
 */
class IzifluxTools
{
    /**
     * Function load
     */
    public static function load()
    {
        set_time_limit(0);
        ini_set('display_errors', 'on');
        ini_set('memory_limit', '4048M');
        header("Content-Type: text/plain");
        error_reporting(E_ALL & ~ E_NOTICE);
    }

    /**
     * Function getPromo
     *
     * @param array $product
     * @return $promo[]
     */
    public static function getPromo($product, $id_product_attribute = null)
    {
        $discount = SpecificPrice::getSpecificPrice(
            $product['id_product'],
            Context::getContext()->shop->id,
            null,
            null,
            null,
            null
        );
        if ($id_product_attribute != null) {
            $discount = SpecificPrice::getSpecificPrice(
                $product['id_product'],
                Context::getContext()->shop->id,
                null,
                null,
                null,
                null,
                $id_product_attribute
            );
        }

        // Default value is empty promo
        $promo = array(
            'from' => '',
            'to' => '',
            'amount' => '',
            'percent' => '',
            'type' => 0
        );

        // If there is an active promotion (check the dates)
        if (isset($discount) && is_array($discount)) {
            if (! ((date('Y-m-d H:i:s') < $discount['from'] || date('Y-m-d H:i:s') >
                $discount['to'])
                && $discount['from']
                != $discount['to'])) {
                $promo['from'] = $discount['from'];
                $promo['to'] = $discount['to'];
                if ($discount['reduction_type'] == 'amount') {
                    $promo['percent'] = '';
                    $promo['amount'] = round($discount['reduction'], 2);
                } else {
                    $promo['percent'] = $discount['reduction'];
                    $promo['amount'] = '';
                }

                if ($product->on_sale == 1) {
                    $promo['type'] = 2;
                } else {
                    $promo['type'] = 1;
                }
            }
        }
        return $promo;
    }

    /**
     * Function to get id of all carriers
     *
     * @param array $allCarriers
     * @return $allCarriersIds[]
     */
    public static function getAllPrestashopCarriersIds($allCarriers)
    {
        $allCarriersIds = array();
        foreach ($allCarriers as $carriersListeCourant) {
            $allCarriersIds[] = $carriersListeCourant['id_carrier'];
        }
        return $allCarriersIds;
    }

    /**
     * Converts from a product the promotion (type of discount) for IziFlux export
     *
     * @param unknown $product
     */
    public static function computePromotionPriceForIziflux($product)
    {
        $promo = array(
            'from' => '',
            'to' => '',
            'amount' => '',
            'percent' => '',
            'type' => 0
        );

        if ((date('Y-m-d H:i:s') < $product['specific_prices']['from'] ||
            date('Y-m-d H:i:s') > $product['specific_prices']['to']) && $product['specific_prices']['from']
            != $product['specific_prices']['to']) {
            // If promotion is not available by dates, do nothing
        } else {
            if (empty($product['specific_prices'])) {
                return $promo;
            } else {
                // Else compute the promotion
                if ($product['on_sale'] == 1) {
                    $promo['type'] = 2;
                } else {
                    $promo['type'] = 1;
                }

                $promo['from'] = $product['reduction_from'];
                $promo['to'] = $product['reduction_to'];
                $promo['amount'] = round($product['reduction'], 2);
                $promo['percent'] = $product['reduction_percent'];
            }
        }

        return $promo;
    }

    /**
     * Don't load with API to avoid performance leeks
     *
     * @return unknown[] list of categories with name and IDs
     */
    public static function getAllPrestashopCategories()
    {

        // $query = 'SELECT c.id_category, c.id_parent, name
        //             FROM `' . _DB_PREFIX_ . 'category` c ,
        //             `' . _DB_PREFIX_ . 'category_lang`cl
        //                 WHERE c.id_category = cl.id_category
        //                 AND cl.id_lang=' . (int)Configuration::get('PS_LANG_DEFAULT');
        // make it multishop compatible
        $query = '
            SELECT c.id_category, c.id_parent, name
            FROM
                `' . _DB_PREFIX_ . 'category` c ,
                `' . _DB_PREFIX_ . 'category_lang` cl,
                `' . _DB_PREFIX_ . 'category_shop` cs
            WHERE
                c.id_category = cl.id_category AND
                c.id_category = cs.id_category AND
                cs.`id_shop` = '.(int)Context::getContext()->shop->id.' AND
                cs.`id_shop` = cl.`id_shop` AND
                cl.id_lang=' . (int)Configuration::get('PS_LANG_DEFAULT');
        $cats = Db::getInstance()->ExecuteS($query);
        $return = array();

        // Make the key become the category id
        foreach ($cats as $value) {
            $return[$value['id_category']] = $value;
        }

        return $return;
    }

    /**
     * Gets the shipping price depending on weight or price configuration of the carrier
     *
     * @param Carrier $carrier
     *            the carrier loaded
     * @param float $weight
     *            the product weight
     * @param float $price
     *            the product price
     * @param float $additional_shipping_cost
     * @param int $idZone
     *            the id of the zone
     */
    public static function getShippingPrice($carrier, $weight, $price, $additional_shipping_cost, $idZone)
    {
        $price = $additional_shipping_cost;
        switch ($carrier->getShippingMethod()) {
            case 1:
                $price += $carrier->getDeliveryPriceByWeight($weight, $idZone);
                break;
            case 2:
                $price += $carrier->getDeliveryPriceByPrice($price, $idZone);
                break;
        }

        /* special case to symboloshop */
        // $price=round(($price*1.2),2);
        /* special case to symboloshop */
        return $price;
    }

    /**
     * Compute product attributes
     *
     * @param array $product
     *            the product as an array
     * @param unknown $default_lang
     * @return array the attributes computed
     */
    public static function getAttributesLight($product, $default_lang)
    {
        $lang = $default_lang;

        if ($lang) {
        }

        $product_attributes = Db::getInstance()->ExecuteS('SELECT * FROM ' . _DB_PREFIX_ . 'product_attribute
            WHERE id_product = ' . (int)$product['id_product']);

        if (empty($product_attributes) || count($product_attributes) == 0) {
            return false;
        }
        $attributes = array();

        foreach ($product_attributes as $product_attribute) {
            $attributes[$product_attribute['id_product_attribute']] = $product_attribute;
            $attributes[$product_attribute['id_product_attribute']]['disponibilite'] =
                ($attributes[$product_attribute['id_product_attribute']]['quantity'] >= 0 ? 'Y' :
                    (Product::isAvailableWhenOutOfStock($product['out_of_stock']) ? 'Y' : 'N'));
            $attributes[$product_attribute['id_product_attribute']]['statut_disponibilite']
                = ($attributes[$product_attribute['id_product_attribute']]['quantity'] >= 0 ? 'S' :
                    (Product::isAvailableWhenOutOfStock($product['out_of_stock']) ? 'R' : 'V'));
            $attributes[$product_attribute['id_product_attribute']]['price'] =
                Product::getPriceStatic(
                    $product_attribute['id_product'],
                    true,
                    $product_attribute['id_product_attribute'],
                    2
                );
            $attributes[$product_attribute['id_product_attribute']]['price_without_reduction']
                = Product::getPriceStatic(
                    $product_attribute['id_product'],
                    true,
                    $product_attribute['id_product_attribute'],
                    2,
                    null,
                    false,
                    false
                );
        }
        return $attributes;
    }

    /**
     * Function to get all attributes of a product
     *
     * @param array $product
     */
    public static function getAttributesFull($product)
    {
        $link = new Link();
        $product_attributes = Db::getInstance()->ExecuteS('SELECT * FROM ' . _DB_PREFIX_ . 'product_attribute
            WHERE id_product = ' . (int)$product['id_product']);

        if (empty($product_attributes) || count($product_attributes) == 0) {
            return false;
        }
        $attributes = array();
        $product_attribute_array = array();
        foreach ($product_attributes as $product_attr) {
            $product_attribute_array[] = $product_attr['id_product_attribute'];
        }

        $attributesDetails = Db::getInstance()->ExecuteS('SELECT pac.id_product_attribute,
            agl.public_name as group_name,
                    al.name as attribute_name
        			FROM ' . _DB_PREFIX_ . 'product_attribute_combination pac
        			INNER JOIN ' . _DB_PREFIX_ . 'attribute a ON(pac.id_attribute = a.id_attribute)
        			INNER JOIN ' . _DB_PREFIX_ . 'attribute_lang al
                    ON(pac.id_attribute = al.id_attribute
                    AND al.id_lang = ' . Configuration::get('PS_LANG_DEFAULT') . ')
        			INNER JOIN ' . _DB_PREFIX_ . 'attribute_group_lang agl
                    ON(a.id_attribute_group = agl.id_attribute_group
                    AND agl.id_lang = ' . Configuration::get('PS_LANG_DEFAULT') . ')
        			WHERE pac.id_product_attribute IN (' . implode(',', $product_attribute_array).')
                    ORDER by pac.id_product_attribute, group_name');


        $attributeInformation = array();
        foreach ($attributesDetails as $key => $value) {
            $attributeInformation[$value['id_product_attribute']][$value['group_name']] = $value['attribute_name'];
        }


        foreach ($product_attributes as $product_attr) {
            $attributes[$product_attr['id_product_attribute']] = $product_attr;
            $attributes[$product_attr['id_product_attribute']]['imagesMkp'] = "";

            // Retrieve stoc from stock_available table
            $id_product_attribute_stock_available_quantity = Db::getInstance()->ExecuteS('SELECT quantity
                FROM ' . _DB_PREFIX_ . 'stock_available
                WHERE id_product_attribute = ' . $product_attr['id_product_attribute']);
            if (abs($id_product_attribute_stock_available_quantity[0]['quantity']) >= "0") {
                $attributes[$product_attr['id_product_attribute']]['quantity']
                    = $id_product_attribute_stock_available_quantity[0]['quantity'];
            }

            if (trim($attributes[$product_attr['id_product_attribute']]['quantity']) == "") {
                $attributes[$product_attr['id_product_attribute']]['quantity'] = "0";
            }
            $attributes[$product_attr['id_product_attribute']]['disponibilite']
                = ($attributes[$product_attr['id_product_attribute']]['quantity'] >= 0 ? 'Y' :
                    (Product::isAvailableWhenOutOfStock($product['out_of_stock']) ? 'Y' : 'N'));
            $attributes[$product_attr['id_product_attribute']]['statut_disponibilite']
                = ($attributes[$product_attr['id_product_attribute']]['quantity'] >= 0 ? 'S' :
                    (Product::isAvailableWhenOutOfStock($product['out_of_stock']) ? 'R' : 'V'));
            $attributes[$product_attr['id_product_attribute']]['name'] = '';
            $attributes[$product_attr['id_product_attribute']]['iziAttributeizi'] = '';
            $attributes[$product_attr['id_product_attribute']]['name_subjective'] = '';
            $images = Db::getInstance()->ExecuteS(
                'SELECT IMG.`id_image` AS id_image
                FROM ' . _DB_PREFIX_ . 'image IMG WHERE IMG.id_product =' . (int)$product['id_product'] .
                ' GROUP BY IMG.`id_image` ORDER BY `IMG`.`position` ASC'
            );

            $protocol_link = (Configuration::get('PS_SSL_ENABLED')) ? 'https://' : 'http://';
            if (count($images) > 0) {
                $attributes[$product_attr['id_product_attribute']]['images'] = '';
                for ($i = 0; $i <= 4; $i ++) {
                    if ($images[$i]['id_image'] != '') {
                        $ids = $product['id_product'] . '-' . $images[$i]['id_image'];
                        if (method_exists('ImageType', 'getFormatedName')) {        // <= ps.1.6.x
                            $imageType = ImageType::getFormatedName('large');
                        } else {                                                    // >= ps.1.7.x
                            $imageType = ImageType::getFormattedName('large');
                        }

                        $attributes[$product_attr['id_product_attribute']]['images']
                            .= $protocol_link.$link->getImageLink($product['link_rewrite'], $ids, $imageType) . SEP;

                        $account = Configuration::get('IZIFLUX_ACCOUNT');
                        if (method_exists('SpecificCustomers', $account . '_getProductAttributeImage')) {
                            $attributes[$product_attr['id_product_attribute']]['imagesMkp'] = call_user_func(array(
                                'SpecificCustomers',
                                Configuration::get('IZIFLUX_ACCOUNT') . '_getProductAttributeImageMkp'
                            ), array(
                                $ids
                            ));
                        } else {
                            $attributes[$product_attr['id_product_attribute']]['imagesMkp'] = "";
                        }
                    } else {
                        $attributes[$product_attr['id_product_attribute']]['images'] .= SEP;
                    }
                }
            } else {
                $cover_product = Product::getCover($product['id_product']);
                $ids = $product['id_product'] . '-' . $cover_product['id_image'];
                $attributes[$product_attr['id_product_attribute']]['images']
                    = $protocol_link.$link->getImageLink($product['link_rewrite'], $ids, 'large').SEP.SEP.SEP.SEP.SEP;

                $account = Configuration::get('IZIFLUX_ACCOUNT');
                if (method_exists('SpecificCustomers', $account . '_getProductAttributeImage')) {
                    $attributes[$product_attr['id_product_attribute']]['imagesMkp'] = call_user_func(array(
                        'SpecificCustomers',
                        Configuration::get('IZIFLUX_ACCOUNT') . '_getProductAttributeImageMkp'
                    ), array(
                        $ids
                    ));
                } else {
                    $attributes[$product_attr['id_product_attribute']]['imagesMkp'] = "";
                }
            }

            $tax_rate = Tax::getProductTaxRate((int) $product['id_product']);

            $specificPricesCombination = SpecificPrice::getByProductId(
                (int) $product['id_product'],
                (int) $product_attr['id_product_attribute']
            );

            $specific_price_reduction = 0;

            if (sizeof($specificPricesCombination)) {
                // Specific price on combination, use it
                $discount = $specificPricesCombination[0];

                if ($discount['reduction_type'] == 'amount') {
                    $specific_price_reduction = $discount['reduction'] * $discount['price'];
                } else {
                    $specific_price_reduction = $discount['reduction'] * $product['price']
                    * $discount['price'] * (1 + ($tax_rate / 100));
                }
            }

            if ($specific_price_reduction) {
                // not used for now
            }

            $attributes[$product_attr['id_product_attribute']]['price'] =
                Product::getPriceStatic(
                    $product['id_product'],
                    true,
                    $product_attr['id_product_attribute'],
                    2
                );

            $attributes[$product_attr['id_product_attribute']]['price_without_reduction']
                = Product::getPriceStatic(
                    $product['id_product'],
                    true,
                    $product_attr['id_product_attribute'],
                    2,
                    null,
                    false,
                    false
                );

            $name_attr = array();
            $name_attribute_subjective = array();

            foreach ($attributeInformation[$product_attr['id_product_attribute']] as $key => $value) {
                //$name_attribute_subjective[] = $attr_name['group_name'] . ' - ' . $attr_name['attribute_name'];
                $name_attribute_subjective[] = $key. ' - ' .$value;
            }

            foreach ($attributeInformation[$product_attr['id_product_attribute']] as $key => $value) {
                $name_attr[] = trim(
                    preg_replace(
                        "/[^a-z0-9]/i",
                        "",
                        IzifluxTools::winToAscii(
                            trim(
                                Tools::strtolower(
                                    utf8_decode(
                                        $key
                                    )
                                )
                            )
                        )
                    )
                ) . ':' . $value;
            }

            $attributes[$product_attr['id_product_attribute']]['name_subjective']
                = implode(', ', $name_attribute_subjective);
            $attributes[$product_attr['id_product_attribute']]['iziAttributeizi'] = implode('#==#', $name_attr);
            if (preg_match("/#==#$/", $attributes[$product_attr['id_product_attribute']]['iziAttributeizi'])) {
                $attributes[$product_attr['id_product_attribute']]['iziAttributeizi']
                = Tools::substr($attributes[$product_attr['id_product_attribute']]['iziAttributeizi'], 0, - 4);
            }
            if (method_exists('SpecificCustomers', Configuration::get('IZIFLUX_ACCOUNT') . '_getPackaging')) {
                // Condition to force value for specific customers
                $id_product_attribute = $product_attr['id_product_attribute'];
                $id_product = $product['id_product'];
                $iziAttributeizi = $attributes[$product_attr['id_product_attribute']]['iziAttributeizi'];
                $quantity = $attributes[$product_attr['id_product_attribute']]['quantity'];

                $result = call_user_func(array(
                    'SpecificCustomers',
                    Configuration::get('IZIFLUX_ACCOUNT') . '_getPackaging'
                ), array(
                    $iziAttributeizi,
                    $id_product_attribute,
                    $id_product,
                    $quantity
                ));

                $attributes[$product_attr['id_product_attribute']]['name_subjective']
                    .= $result['name_subjective_extra'];
                $attributes[$product_attr['id_product_attribute']]['iziAttributeizi']
                    = $result['iziAttributeizi'];
                $attributes[$product_attr['id_product_attribute']]['quantity']
                    = $result['quantity'];
            }
        }
        return $attributes;
    }

    /**
     * Tests if a remote file exists
     *
     * @param String $url
     * @return boolean if the file exists
     */
    public static function remoteFileExists($url)
    {
        ini_set('allow_url_fopen', '1');

        return @fclose(@fopen($url, 'r'));
    }

    /**
     * log a debug trace into a log file
     *
     * @param string $toLog
     *            the string to log
     * @param number $type
     *            the log type, determines the output
     */
    public static function logDebug($toLog, $type = 0, $auto_detect = true)
    {
        if ($auto_detect) {
            $script_name = basename($_SERVER['PHP_SELF']);
            switch ($script_name) {
                case 'export.php':
                    $type = 0;
                    break;
                case 'exportPriceStock.php':
                    $type = 1;
                    break;
                case 'order_integration.php':
                    $type = 2;
                    break;
                case 'order_sent.php':
                    $type = 3;
                    break;
            }
        }
        switch ($type) {
            case 0:
                // Export
                $outputFile = _PS_MODULE_DIR_ . 'iziflux/log.txt';
                break;
            case 1:
                // Export Price Stock
                $outputFile = _PS_MODULE_DIR_ . 'iziflux/log/logPriceStock.txt';
                break;
            case 2:
                // Export Price Stock : faire un seul fichier par heure.
                $outputFile = _PS_MODULE_DIR_ . 'iziflux/log/logOrderIntegration_'.date('Y-m-d-H').'-00.txt';
                break;
            case 3:
                // Order Sent : faire un seul fichier par heure.
                $outputFile = _PS_MODULE_DIR_ . 'iziflux/log/logOrderSent_'.date('Y-m-d-H').'-00.txt';
                break;
        }

        if (!file_exists($outputFile)) {
            $fp = fopen($outputFile, 'a+');
            // fwrite($fp, chr(10) . date('d/m/Y h:i:s A') . ' - ' . $toLog);
            fclose($fp);
        }

        // Code to check for generation time of log files
        $now = time();
        $dateGeneration = filemtime($outputFile);
        $filePieces = explode('.', $outputFile);
        $extension = '.txt';
        $age = ($now - $dateGeneration);

        if (strpos($filePieces['0'], 'logOrderIntegration_') !== false) {
            //scan folder for all file starting with logOrderIntegration
            $logDirectory = new RecursiveDirectoryIterator(_PS_MODULE_DIR_ . 'iziflux/log');
            $extension = array(
                'txt'
            );

            foreach (new RecursiveIteratorIterator($logDirectory) as $file) {
                $pieces = explode('.', $file);
                $file_ext = array_pop($pieces);
                if (in_array(Tools::strtolower($file_ext), $extension)) {
                    $file_path = trim($file);

                    if (strpos($file_path, 'logOrderIntegration_') !== false) {
                        $dateGeneration = filemtime($file_path);
                        $file_age = ($now - $dateGeneration);

                        // file age
                        // 5184000 seconds = 60 days (60 * 24 * 3600)
                        if ($file_age > 5184000) {
                            unlink($file_path);
                        }
                    }
                }
            }
        } else {
            // file age
            // 1800000 seconds = 500 Hours (500 * 3600)
            if ($age > 1800000) {
                if (file_exists($filePieces['0'].'_500'.$extension)) {
                    unlink($filePieces['0'].'_500'.$extension);
                    // Rename current log file
                    rename($outputFile, $filePieces['0'].'_500'.$extension);
                } else {
                    rename($outputFile, $filePieces['0'].'_500'.$extension);
                }
            }
        }

        $fp = fopen($outputFile, 'a+');
        fwrite($fp, chr(10) . date('d/m/Y h:i:s A') . ' - ' . $toLog);
        fclose($fp);


        // // Write all log to log.txt in module folder
        // $log_file = _PS_MODULE_DIR_ . 'iziflux/log.txt';
        // $fp = fopen($log_file, 'a+');
        // fwrite($fp, chr(10) . date('d/m/Y h:i:s A') . ' - ' . $toLog);
        // fclose($fp);
    }

    public static function clearLogFile($file)
    {
        file_put_contents($file, '');
    }

    /**
     * Converts non standard windows caracters to standard ASCII codes
     *
     * @param Strng $str
     *            the caracters in ASCII
     */
    public static function winToAscii($toHandle)
    {
        $caracters = "\xc0\xc1\xc2\xc3\xc4\xc5\xe0\xe1\xe2\xe3\xe4
            \xe5\xd2\xd3\xd4\xd5\xd6\xd8\xf2\xf3\xf4\xf5\xf6\xf8\xc8\xc9
            \xca\xcb\xe8\xe9\xea\xeb\xc7\xe7\xcc\xcd\xce\xcf\xec\xed\xee
            \xef\xd9\xda\xdb\xdc\xf9\xfa\xfb\xfc\xff\xd1\xf1";
        $replacement = "\x41\x41\x41\x41\x41\x41\x61\x61\x61\x61\x61\x61
            \x4f\x4f\x4f\x4f\x4f\x4f\x6f\x6f\x6f\x6f\x6f\x6f\x45\x45\x45\
            x45\x65\x65\x65\x65\x43\x63\x49\x49\x49\x49\x69\x69\x69\x69\x55
            \x55\x55\x55\x75\x75\x75\x75\x79\x4e\x6e";
        $toHandle = strtr($toHandle, $caracters, $replacement);
        return $toHandle;
    }

    /**
     * Escapes HTML to a string
     */
    public static function htmlEscape($string)
    {
        $string = str_replace(chr(9), ' ', $string);
        $string = str_replace(chr(10), ' ', $string);
        $string = str_replace(chr(13), ' ', $string);
        $string = strtr($string, "\x7C", "\x20");
        return $string;
    }

    /**
     * Function to get anchor link
     *
     * @param int $id_product_attribute
     * @param boolean $with_id
     * @param int $product_id
     * @return string anchor
     */
    public static function getAnchor($id_product_attribute, $with_id = false, $product_id = 0)
    {
        $attributes = IzifluxTools::getAttributesParams($product_id, $id_product_attribute);
        $anchor = '#';
        $sep = "_";
        foreach ($attributes as &$a) {
            foreach ($a as &$b) {
                $b = str_replace($sep, '_', Tools::link_rewrite($b));
            }
            if (version_compare(@constant('_PS_VERSION_'), '1.6.0.0', '>=')) {
                $sep = Configuration::get('PS_ATTRIBUTE_ANCHOR_SEPARATOR');
                $anchor .= '/' . ($with_id && isset($a['id_attribute']) && $a['id_attribute'] ?
                    (int) $a['id_attribute'] . $sep : '') . $a['group'] . $sep . $a['name'];
            } else {
                $sep = '-';
                $anchor .= '/' . $a['group'] . $sep . $a['name'];
            }
        }

        return $anchor;
    }

    /**
     * Function to get product attributes
     *
     * @param int $id_product
     * @param int $id_product_attribute
     */
    public static function getAttributesParams($id_product, $id_product_attribute)
    {
        $result = Db::getInstance()->executeS('SELECT al.`name`, agl.`name` as `group`, al.`id_attribute`
    			FROM `' . _DB_PREFIX_ . 'attribute` a
    			LEFT JOIN `' . _DB_PREFIX_ . 'attribute_lang` al
    				ON (al.`id_attribute` = a.`id_attribute`
                    AND al.`id_lang` = ' . (int) Configuration::get('PS_LANG_DEFAULT') . ')
    			LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute_combination` pac
    				ON (pac.`id_attribute` = a.`id_attribute`)
    			LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute` pa
    				ON (pa.`id_product_attribute` = pac.`id_product_attribute`)
    			' . Shop::addSqlAssociation('product_attribute', 'pa') . '
    			LEFT JOIN `' . _DB_PREFIX_ . 'attribute_group_lang` agl
    				ON (a.`id_attribute_group` = agl.`id_attribute_group`
                    AND agl.`id_lang` = ' . (int) Configuration::get('PS_LANG_DEFAULT') . ')
    			WHERE pa.`id_product` = ' . (int) $id_product . '
    				AND pac.`id_product_attribute` = ' . (int) $id_product_attribute . '
    				AND agl.`id_lang` = ' . (int) Configuration::get('PS_LANG_DEFAULT'));
        return $result;
    }

    /**
     * Function to get a clean string
     *
     * @param string $string
     * @param string $type
     */
    public static function clean($string, $type)
    {
        /**
         * fonction de nettoyage des champs avant enregistrement
         */
        $string = trim(Tools::stripslashes($string));

        if (method_exists('SpecificCustomers', Configuration::get('IZIFLUX_ACCOUNT') . '_getCleanString')) {
            $result = call_user_func(array(
                'SpecificCustomers',
                Configuration::get('IZIFLUX_ACCOUNT') . '_getCleanString'
            ), array(
                $string
            ));
        }

        if ($result == true) {
            return $string;
        } else {
            // Swithch case
            switch ($type) {
                case 'firstname':
                    if (empty($string)) {
                        $string = '.';
                    }
                // No Break
                case 'lastname':
                    $string = preg_replace('/[0-9!<>,;?=+()@#"{}_$%:~]/ui', '', ($string));
                    if (empty($string)) {
                        $string = '';
                    }
                    if (Configuration::get('IZIFLUX_ACCOUNT') == 'chambredressingliterie') {
                        if (empty($string)) {
                            $string = '.';
                        }
                    }
                    break;
                case 'address':
                    $string = preg_replace('/[!<>?=+@{}_$%.;#]/ui', '', ($string));
                    if (empty($string)) {
                        $string = '';
                    }
                    break;
                case 'postcode':
                    $string = Tools::substr(preg_replace('/[^a-zA-Z 0-9-]/ui', '', $string), 0, 12);
                    break;
                case 'city':
                    $string = preg_replace('/[!<>;?=+@#"{}_$%~#]/ui', '', ($string));
                    break;
                case 'email':
                    $string = preg_replace('/[ !#$%&\'*+\/=?^`{}|~]/ui', '', mb_strtolower($string));
                    break;
                case 'phone':
                    $string = preg_replace('/[^+0-9. ()-]*/', '', $string);
                    break;
            }
        }
        $string = trim($string);
        return $string;
    }

    /**
     * Function to create a new cart
     *
     * @param int $customer_id
     * @param int $invoice_address_id
     * @param int $shipping_address_id
     * @param int $id_currency
     * @param int $id_carrier
     */
    public static function manageCreateCartForOrder(
        $customer_id,
        $invoice_address_id,
        $shipping_address_id,
        $id_currency,
        $id_carrier
    ) {
        $cart = new Cart();

        $cart->id_carrier = (int) Configuration::get('IZIFLUX_DEFAULT_CARRIER');
        $cart->id_lang = Configuration::get('PS_LANG_DEFAULT');
        $cart->id_address_delivery = (int) ($shipping_address_id);
        $cart->id_address_invoice = (int) ($invoice_address_id);
        $cart->id_currency = $id_currency;
        $cart->id_customer = (int) ($customer_id);
        $cart->secure_key = md5(uniqid(rand(), true));
        $cart->recyclable = 0;
        $cart->id_carrier = (int) $id_carrier;

        IzifluxTools::testOutput('manageCreateCartForOrder : BEFORE add()');
        IzifluxTools::testOutput(Tools::getValue('default_carrier', Configuration::get('IZIFLUX_DEFAULT_CARRIER')));

        $cart->add();

        IzifluxTools::testOutput('manageCreateCartForOrder : AFTER add()');

        // Retrun new cart id
        return $cart->id;
    }

    /**
     * Function to get id_product and id_product_attribute
     *
     * @param
     *            $file_reference
     */
    public static function getIdProductIdProductAttribute($file_reference)
    {
        $productTableInfos = array();

        // Columns in array
        $referenceProductColumns = array(
            'reference',
            'supplier_reference',
            'ean13',
            'upc',
            'id_product'
        );

        foreach ($referenceProductColumns as $referenceProductColumn) {
            // If file reference in array
            $result_recup_product = Db::getInstance()->executeS('SELECT *
                    FROM `' . _DB_PREFIX_ . 'product`
                    WHERE `' . $referenceProductColumn . '` = "' . pSQL($file_reference) . '" LIMIT 0,1;');
            if ($referenceProductColumn != "id_product_attribute" && $result_recup_product) {
                if ($result_recup_product[0][$referenceProductColumn] == $file_reference) {
                    $productTableInfos['id_product'] = $result_recup_product[0]['id_product'];

                    $msg = "getIdProductIdProductAttribute: req01 :";
                    IzifluxTools::logDebug('IzifluxTools.php '.__LINE__.': '.$msg . 'SELECT *
                        FROM `' . _DB_PREFIX_ . 'product` WHERE `' . $referenceProductColumn . '`
                        = "' . $file_reference . '";', 0);
                }
            }
            // If file refrence is in product_attribute column
            $sqlRef = 'SELECT * FROM `' . _DB_PREFIX_ . 'product_attribute`
                    WHERE `' . $referenceProductColumn . '` = "' . pSQL($file_reference) . '" LIMIT 0,1;';
            if ($result_recup_product_attr = Db::getInstance()->executeS($sqlRef)) {
                if ($result_recup_product_attr[0][$referenceProductColumn] == $file_reference) {
                    $msg = "getIdProductIdProductAttribute: req02 :";
                    IzifluxTools::logDebug(
                        'IzifluxTools.php '.__LINE__.': '.$msg . 'SELECT * FROM `'
                        . _DB_PREFIX_ . 'product_attribute`
                        WHERE `' . $referenceProductColumn . '` = "' . $file_reference . '";',
                        0
                    );

                    $productTableInfos['id_product_attribute'] = $result_recup_product_attr[0]['id_product_attribute'];
                }
            }
        }
        return $productTableInfos;
    }

    /**
     * Get id tax for a product
     *
     * @param int $idProduct
     */
    public static function getTaxInfoDetails($idProduct)
    {
        $tableauTax = array();

        $idTaxRulesGroup = (int) Product::getIdTaxRulesGroupByIdProduct($idProduct);
        // Retriece id_tax
        if (! ($GLOBALS['id_country'])) {
            $GLOBALS['id_country'] = Configuration::get('PS_COUNTRY_DEFAULT');
        }
        $sqlgetTaxInfoDetails = 'SELECT `id_tax` FROM `' . _DB_PREFIX_ . 'tax_rule`
            WHERE `id_tax_rules_group` ="' . $idTaxRulesGroup . '"
            AND `id_country` ="' . (int)$GLOBALS['id_country'] . '";';
        $resSqlgetTaxInfoDetails = Db::getInstance()->executeS($sqlgetTaxInfoDetails);
        $tableauTax['taxId'] = $resSqlgetTaxInfoDetails[0]['id_tax'];
        // Retrieve rate
        $sqlgetTaxInfoDetails = 'SELECT `rate` FROM `' . _DB_PREFIX_ . 'tax`
            WHERE `id_tax` ="' . $tableauTax['taxId'] . '";';
        $resSqlgetTaxInfoDetails = Db::getInstance()->executeS($sqlgetTaxInfoDetails);
        $tableauTax['taxRate'] = $resSqlgetTaxInfoDetails[0]['rate'];

        // Retrieve tax name
        if (! ($GLOBALS['idLang'])) {
            $GLOBALS['idLang'] = Configuration::get('PS_LANG_DEFAULT');
        }
        $sqlgetTaxInfoDetails = 'select `name` FROM `' . _DB_PREFIX_ . 'tax_lang`
            WHERE `id_tax` ="' . $tableauTax['taxId'] . '" and `id_lang` ="' . (int)$GLOBALS['idLang'] . '" ;';
        $resSqlgetTaxInfoDetails = Db::getInstance()->executeS($sqlgetTaxInfoDetails);
        $tableauTax['taxName'] = $resSqlgetTaxInfoDetails[0]['name'];
        return $tableauTax;
    }

    /**
     * Function to check if an address exists
     *
     * @param array $a
     * @return int id_address
     */
    public static function addressExist($a)
    {
        $sql = 'SELECT a.id_address FROM `' . _DB_PREFIX_ . 'address` a
            WHERE a.id_customer = "' . (int)$a['customer_id'] . '"
                AND a.address1 ="' . pSQL($a['address']) . '"
                AND a.postcode = "' . pSQL($a['postcode']) . '" AND a.city = "' . pSQL($a['city']) . '";';
        $res = Db::getInstance()->executeS($sql);
        return (isset($res[0]['id_address']) && $res[0]['id_address']) ? $res[0]['id_address'] : 0;
    }

    /**
     * Function to get the defaut carrier for an order
     *
     * @param string $name
     * @return int $id_carrier
     */
    public static function getCarrierByName($name)
    {
        $sql = 'SELECT * FROM `' . _DB_PREFIX_ . 'carrier` where active="1" and deleted="0" ';
        $carrier_results = Db::getInstance()->executeS($sql);

        $tabIdLeven = array();
        foreach ($carrier_results as $carrier_result) {
            $carrier_result['name'];
            $tabIdLeven[$carrier_result['id_carrier']]
                = levenshtein(Tools::strtolower($name), Tools::strtolower($carrier_result['name']));
        }
        asort($tabIdLeven);

        $tabIdLeven = array_flip($tabIdLeven);

        $id_carrier = array_shift($tabIdLeven);

        if ($id_carrier) {
            return $id_carrier;
        } else {
            $id_carrier = (int) (Configuration::get('IZIFLUX_DEFAULT_CARRIER'));
            return $id_carrier;
        }
    }

    /**
     * Function to register a customer and return the customer id
     *
     * @param int $invoice_email
     * @param string $invoice_lastname
     * @param string $invoice_firstname
     * @param int $id_group
     */
    public static function addCustomer($invoice_email, $invoice_lastname, $invoice_firstname, $id_group)
    {
        // Retrieve customer_id if client already exist (using email address)
        $customer_id = Customer::customerExists($invoice_email, true, false);
        if ($customer_id) {
            // Retrieve customer_id if avaialble
            $customer = new Customer($customer_id);
        } else {
            // If customer_id not available, register as new client

            // Field length
            $invoice_lastname = mb_substr(IzifluxTools::clean(trim($invoice_lastname), 'lastname'), 0, 32);
            $invoice_firstname = mb_substr(IzifluxTools::clean(trim($invoice_firstname), 'lastname'), 0, 32);

            $customer = new Customer();
            $customer->lastname = $invoice_lastname;
            $customer->firstname = $invoice_firstname;
            $customer->email = $invoice_email;
            $customer->id_default_group = $id_group;
            $customer->active = 1;
            $customer->newsletter = 0;
            $customer->optin = 0;

            // Define new account as non guest
            $customer->is_guest = 0;
            $customer->date_add = date("Y-m-d H:i:s");
            $customer->date_upd = date("Y-m-d H:i:s");
            $password = Tools::passwdGen();
            $customer->passwd = Tools::encrypt($password);
            if (REGISTERDB == 'ON') {
                $customer->add();
            }
            $customer_id = $customer->id;
        }

        return $customer;
    }

    /**
     * Function to register a group and return group id
     *
     * @param int $marketPlaceName
     */
    public static function addGroup($marketPlaceName)
    {

        // Request to verify if group exists
        $rqt_verif_group = 'SELECT * FROM `' . _DB_PREFIX_ . 'group_lang`
            WHERE `name` = "iziflux-' . pSQL($marketPlaceName) . '"';
        $resultat_group = Db::getInstance()->getRow($rqt_verif_group);

        if ($resultat_group === false) {
            // If group does not exist
            $tabNameGroupe = array();

            // Retrieve number of languages
            $req_recup_nb_group_lang = 'SELECT max(id_lang) as nblangue FROM `' . _DB_PREFIX_ . 'lang`';
            $res_recup_nb_group_lang = Db::getInstance()->getRow($req_recup_nb_group_lang);
            for ($nbLangue = 0; $nbLangue < $res_recup_nb_group_lang["nblangue"]; $nbLangue ++) {
                $tabNameGroupe[] = "iziflux-" . $marketPlaceName;
            }
            $tabNameGroupe[] = "iziflux-" . $marketPlaceName;

            IzifluxTools::testOutput('tabNameGroupe: ', $tabNameGroupe);
            IzifluxTools::testOutput('nb lang: ' . $res_recup_nb_group_lang['nblangue']);
            IzifluxTools::testOutput('res_recup_nb_group_lang: ', $res_recup_nb_group_lang);

            // Create a group
            $group = new Group();
            $group->name = $tabNameGroupe;
            $group->reduction = '0';
            $group->show_prices = '1';
            $group->price_display_method = '0';
            $group->date_add = date("Y-m-d H:i:s");
            $group->date_upd = '0000-00-00 00:00:00';
            if (REGISTERDB == 'ON') {
                $group->add();
            }

            $id_group = $group->id;
        } else {
            // Retrieve the group
            $id_group = $resultat_group['id_group'];
        }

        // Return id_group
        return $id_group;
    }

    /**
     * Function to get all order list
     *
     * @param
     *            $statusClient
     */
    public static function getOrdersList($statusClient)
    {
        $group_by = 'GROUP BY `' . _DB_PREFIX_ . 'order_iziflux`.id_order, `'
            . _DB_PREFIX_ . 'order_iziflux`.reference_product_market';

        $sql = 'SELECT *
        			FROM `' . _DB_PREFIX_ . 'order_iziflux`
        				LEFT JOIN `' . _DB_PREFIX_ . 'order_detail`
        					ON `' . _DB_PREFIX_ . 'order_iziflux`.id_order = `' . _DB_PREFIX_ . 'order_detail`.id_order
        				LEFT JOIN `' . _DB_PREFIX_ . 'order_history`
        					ON `' . _DB_PREFIX_ . 'order_detail`.id_order = `' . _DB_PREFIX_ . 'order_history`.id_order
        			WHERE `' . _DB_PREFIX_ . 'order_history`.id_order_state IN("5", "4" ' . pSQL($statusClient) . ')
        				AND `' . _DB_PREFIX_ . 'order_history`.date_add > "'
                        . date("Y-m-d H-i-s", mktime(date('H') - NBHEURES, 0, 0, date('m'), date('d'), date('Y'))) . '"
        				AND `' . _DB_PREFIX_ . 'order_history`.date_add < "'
                        . date("Y-m-d H-i-s", mktime(date('H') + 1, 0, 0, date('m'), date('d'), date('Y')))
                        . '" ' . $group_by;
        IzifluxTools::testOutput('IzifluxTools::getOrdersList >> sql:', $sql);
        return Db::getInstance()->executeS($sql);
    }

    /**
     * Get carrier info for product
     *
     * @param array $ligne_produit
     * @return unknown
     */
    public static function getOrdersListCarrier($ligne_produit)
    {
        $sql_transporteur = 'SELECT OO.shipping_number, CA.name, CA.url
							FROM `' . _DB_PREFIX_ . 'orders` OO
								LEFT JOIN `' . _DB_PREFIX_ . 'carrier` CA
									ON OO.id_carrier = CA.id_carrier
							WHERE OO.id_order = "' . (int)$ligne_produit['id_order'] . '"';
        $transporteur = Db::getInstance()->executeS($sql_transporteur);

        if (! ($transporteur[0]['shipping_number'])) {
            $sql_transporteur = 'SELECT OC.tracking_number as shipping_number, CA.name, CA.url
								FROM `' . _DB_PREFIX_ . 'order_carrier` OC
									LEFT JOIN `' . _DB_PREFIX_ . 'carrier` CA
										ON OC.id_carrier = CA.id_carrier
								WHERE OC.id_order = "' . (int)$ligne_produit['id_order'] . '"';
            $transporteur = Db::getInstance()->executeS($sql_transporteur);
        }
        IzifluxTools::testOutput('IzifluxTools::getOrdersListCarrier >> sql:', $sql_transporteur);
        return $transporteur;
    }

    /**
     * Get remote filename
     */
    public static function getRemoteFilename($get, $timestamp)
    {

        // Send filename via cron
        if ($get['file_name']) {
            // Verify if file exists else redefine url
            $url = '';
            if (! IzifluxTools::remoteFileExists($url)) {
                $url = "http://www." .
                    Configuration::get('IZIFLUX_ORDER_INTEGRATION_URL') .
                    ".com/iziflux/marketPlace/partenaires/prestashop/" .
                    Configuration::get('IZIFLUX_ACCOUNT') . "/prestashop_" .
                    Configuration::get('IZIFLUX_ACCOUNT') . "_" . $timestamp . ".txt";
            } else {
                $url = "http://www." .
                    Configuration::get('IZIFLUX_ORDER_INTEGRATION_URL') .
                    ".com/iziflux/marketPlace/partenaires/prestashop/" .
                    Configuration::get('IZIFLUX_ACCOUNT') . "/" .
                    Tools::getValue('file_name') . ".txt";
            }
        } else {
            $url = "http://www." .
                Configuration::get('IZIFLUX_ORDER_INTEGRATION_URL') .
                ".com/iziflux/marketPlace/partenaires/prestashop/" .
                Configuration::get('IZIFLUX_ACCOUNT') . "/prestashop_" .
                Configuration::get('IZIFLUX_ACCOUNT') . "_" . $timestamp . ".txt";
        }

        if ($get['compteSupp']) {
            $url = "http://www." .
                Configuration::get('IZIFLUX_ORDER_INTEGRATION_URL') .
                ".com/iziflux/marketPlace/partenaires/prestashop/" .
                Tools::getValue('compteSupp') .
                "/prestashop_" . Tools::getValue('compteSupp') . "_" . $timestamp . ".txt";
        }

        return $url;
    }

    public static function transformIntoArrayOrders($file)
    {
        $tbl_commandes = array();
        foreach ($file as $line) {
            $line = trim($line);

            // check if Configuration::get('IZIFLUX_ACCOUNT') . '_getLine' function exists in SpecificCustomers class
            if (method_exists('SpecificCustomers', Configuration::get('IZIFLUX_ACCOUNT') . '_getLine')) {
                $line = call_user_func(array(
                    'SpecificCustomers',
                    Configuration::get('IZIFLUX_ACCOUNT') . '_getLine'
                ), array(
                    $line
                ));
            }

            $line = str_replace(array(
                'NON-RENSEIGNE',
                'Ø'
            ), array(
                '',
                ''
            ), $line);
            $temp = explode('|', utf8_encode($line));
            if ($temp[4] != '') {
                if (strstr(Tools::strtolower($temp[4]), 'date')) {
                    // continue;
                }
                $tbl_commandes[$temp[0]][] = $temp;
            }
        }
        return $tbl_commandes;
    }

    /**
     * Function to set file/directory permissions to 755 recursively
     * @param module path
     */
    public static function chmodRecursive($path)
    {
        $dir = new DirectoryIterator($path);
        foreach ($dir as $item) {
            @chmod($item->getPathname(), 0755);
            if ($item->isDir() && !$item->isDot()) {
                self::chmodRecursive($item->getPathname());
            }
        }
    }

    /**
     * Function to retrieve all supplier reference for product
     */
    public static function getProductSupplierReference($id_product, $id_product_attribute = null)
    {
        $sql = 'SELECT ps.product_supplier_reference';
        $sql .= ' FROM `'._DB_PREFIX_.'product_supplier` ps';
        $sql .= ' WHERE ps.`id_product` = '.(int)$id_product;

        if ($id_product_attribute !== null) {
            $sql .= ' AND ps.`id_product_attribute` = '.(int)$id_product_attribute;
        }

        $results = Db::getInstance()->executeS($sql);

        $supplierNames = '';
        $eocount = 0;
        foreach ($results as $result) {
            $eocount++;
            $supplierNames .= $result['product_supplier_reference'];
            if (count($results) != $eocount) {
                $supplierNames .= ', ';
            }
        }

        return $supplierNames;
    }

    /**
     * @static
     * @param $iso_code
     * @param int $id_shop
     * @return int
     */
    public static function getIdByIsoCodeNum($iso_code_num, $id_shop = 0)
    {
        $query = Currency::getIdByQuery($id_shop);
        $query->where('`iso_code`= \''.pSQL($iso_code_num).'\'');

        return (int)Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query->build());
    }

    /**
     * output if test enabled
     * @param  string $str
     * @param  mixed $var_to_dump
     * @return
     */
    public static function testOutput($str, $var_to_dump = null)
    {
        if (defined('TESTMODE') && TESTMODE == 'ON') {
            $output = $str;
            if ($var_to_dump) {
                $output .= print_r($var_to_dump, true);
            }
            $output .= PHP_EOL;
            echo $output;
        }
    }
}
