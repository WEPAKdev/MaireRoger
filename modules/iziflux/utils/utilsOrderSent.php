<?php
/**
 *  2016 Profileo
 *
 *  @author    Profileo <contact@profileo.com>
 *  @copyright 2016 Profileo
 *  @license   Profileo
 *  @link  http://www.profileo.com
 */

include_once(dirname(__FILE__) . '/IzifluxTools.php');
include_once(dirname(__FILE__) . '/../classes/OrderIziflux.php');
include_once(dirname(__FILE__) . '/../classes/OrderIzifluxFile.php');

include_once(dirname(__FILE__) . '../../lib/SpecificCustomers.php');

class OrderSent
{
    /**
     * RETRIEVE PARTNER FILE AND RECORDED THE UPDATES
     * Order number
     * Product number
     * Product Reference market place ( Provided in the export file that you get from us)
     * Transaction Number ( Provided in the export file that you get from us)
     * Market Place
     * Shipping Date
     * Carrier Name (Required for most market place )
     * Shipping Number
     * Note (optional and only asked at ebay. ( This field can be left blank ) )
     * Tracking URL (optional and only asked at Pixmania . ( This field can be left blank ) )
     * Phone carrier (optional and only asked at Pixmania . ( This field can be left blank ) )
     * Quantity ( only asked at amazon . (Defaults provided in the export file that you get in
     * Us) )
     */
    public static function executeOrderSent()
    {
        if (Tools::getValue('nbHeures')) {
            define('NBHEURES', Tools::getValue('nbHeures'));
        }
        if (! defined('NBHEURES')) {
            define('NBHEURES', 12);
        }

        $tbl_colonnes = array(
            "Numéro de commande",
            "Numéro du produit",
            "Reference produit market place",
            "Numero de transaction",
            "Market place",
            "Date expédition",
            "Nom transporteur",
            "Numéro d’expédition",
            "Note",
            "Url de suivi",
            "Téléphone transporteur",
            "Quantité"
        );

        if (Tools::getValue('date')) {
            $timestamp = Tools::getValue('date');
        } else {
            $timestamp = date("Y-m-d-H-i-s", mktime(date('H') - 1, 0, 0, date('m'), date('d'), date('Y')));
        }

        $client = Configuration::get('IZIFLUX_ACCOUNT');
        $url_file = './flux/sent/prestashop_' . $client . '_' . $timestamp . '.txt';
        $name_transporteur = '';

        IzifluxTools::testOutput('url_file : ' . $url_file);
        IzifluxTools::logDebug('utilsOrderSent.php '.__LINE__.": url_file : " . $url_file, 3);
        $statusClient = null;

        if (method_exists('SpecificCustomers', Configuration::get('IZIFLUX_ACCOUNT') . '_getStatus')) {
            $statusClient = call_user_func(array(
                'SpecificCustomers',
                Configuration::get('IZIFLUX_ACCOUNT') . '_getStatus'
            ), array());
        }

        $orderProductsReturnLine = IzifluxTools::getOrdersList($statusClient);
        IzifluxTools::testOutput('orderProductsReturnLine = ', $orderProductsReturnLine);
        $nbTotalProduct = count($orderProductsReturnLine);
        IzifluxTools::logDebug('utilsOrderSent.php '.__LINE__.': Nb de commandes : ' . $nbTotalProduct, 3);

        if (empty($orderProductsReturnLine)) {
            IzifluxTools::testOutput('EXIT No orders to be shipped available');
            IzifluxTools::logDebug('utilsOrderSent.php '.__LINE__.': EXIT No orders to be shipped available', 3);
            // echo ("EXIT No orders to be shipped available");
            exit();
        }

        $handle = fopen($url_file, "w+");
        fwrite($handle, implode('|', $tbl_colonnes) . PHP_EOL);

        $name_transporteur_defaut = '';
        $defaultLanguage = (int) (Configuration::get('PS_LANG_DEFAULT'));
        $carriers = Carrier::getCarriers($defaultLanguage);
        foreach ($carriers as $carrier) {
            if (Configuration::get('IZIFLUX_DEFAULT_CARRIER') == $carrier['id_carrier']) {
                $name_transporteur_defaut = $carrier['name'];
            }
        }
        if ($name_transporteur_defaut == '') {
            $name_transporteur_defaut = Configuration::get('PS_SHOP_NAME');
        }

        $counter = 0;
        foreach ($orderProductsReturnLine as $productLine) {
            $toLog = (++$counter) . ' / '.$nbTotalProduct.' :
                id_order_iziflux = ' . $productLine['id_order_iziflux'] . ',  id_product ' . $productLine['id_product'];
            IzifluxTools::logDebug('utilsOrderSent.php '.__LINE__.':
                '.$toLog, 3);

            $numero_suivi = '';
            $name_transporteur = '';
            $note = '';
            $url_de_suivi = '';
            $telephone_transporteur = '';

            $transporteur = IzifluxTools::getOrdersListCarrier($productLine);

            if ($transporteur === false) {
                $name_transporteur = $name_transporteur_defaut;
            } elseif ($transporteur[0]['name'] == '0') {
                $name_transporteur = $name_transporteur_defaut;
            } else {
                $numero_suivi = $transporteur[0]['shipping_number'];
                $name_transporteur = utf8_decode(preg_replace("/\s+/", "_", trim($transporteur[0]['name'])));
                if (isset($transporteur[0]['url'])) {
                    $url_de_suivi = str_replace('@', $numero_suivi, $transporteur[0]['url']);
                }
            }

            if (method_exists('SpecificCustomers', Configuration::get('IZIFLUX_ACCOUNT') . '_getOrderSpecificDetail')) {
                $result = call_user_func(array(
                    'SpecificCustomers',
                    Configuration::get('IZIFLUX_ACCOUNT') . '_getOrderSpecificDetail'
                ), array(
                    $numero_suivi,
                    $name_transporteur,
                    $note,
                    $url_de_suivi,
                    $telephone_transporteur
                ));

                $numero_suivi = $result['numero_suivi'];
                $name_transporteur = $result['name_transporteur'];
                $note = $result['note'];
                $url_de_suivi = $result['url_de_suivi'];
                $telephone_transporteur = $result['telephone_transporteur'];
            }

            // check if Configuration::get('IZIFLUX_ACCOUNT') . '_getMarketPlace' function exists in SpecificCustomers
            // class
            if (method_exists('SpecificCustomers', Configuration::get('IZIFLUX_ACCOUNT') . '_getMarketPlace')) {
                $productLine['market_place'] = call_user_func(array(
                    'SpecificCustomers',
                    Configuration::get('IZIFLUX_ACCOUNT') . '_getMarketPlace'
                ), array(
                    $productLine['market_place']
                ));
            }

            if (method_exists('SpecificCustomers', Configuration::get('IZIFLUX_ACCOUNT') . '_getCarrierName')) {
                $name_transporteur = call_user_func(array(
                    'SpecificCustomers',
                    Configuration::get('IZIFLUX_ACCOUNT') . '_getCarrierName'
                ), array());
            }

            if ($client=="expertbynet") {
                if (preg_match("/amazon/i", $productLine['market_place'])) {
                    $productLine['market_place']="amazon";
                }
            } elseif ($client=="expertbynetnew") {
                if (preg_match("/amazon/i", $productLine['market_place'])) {
                    $productLine['market_place']="amazon";
                }
            }

            $toBeWritten = array();

            $toBeWritten[] = $productLine['id_iziflux_order'];
            $toBeWritten[] = $productLine['id_product'];
            $toBeWritten[] = $productLine['reference_product_market'];
            $toBeWritten[] = $productLine['transaction'];
            $toBeWritten[] = $productLine['market_place'];

            if (! empty($productLine['date_add'])) {
                $toBeWritten[] = date("Y-m-d", strtotime($productLine['date_add']));
            } else {
                $toBeWritten[] = date("Y-m-d");
            }

            $toBeWritten[] = $name_transporteur;
            $toBeWritten[] = $numero_suivi;
            $toBeWritten[] = $note;
            $toBeWritten[] = $url_de_suivi;
            $toBeWritten[] = $telephone_transporteur;
            $toBeWritten[] = $productLine['quantity'];

            fwrite($handle, implode('|', $toBeWritten) . PHP_EOL);
        }
        fclose($handle);

        IzifluxTools::logDebug('utilsOrderSent.php '.__LINE__.": END", 0);

        if (date('H') > 22) {
            error_reporting(E_ERROR | E_WARNING | E_PARSE);
            $it = new RecursiveDirectoryIterator(_PS_MODULE_DIR_ . 'iziflux');
            $display = array(
                'txt'
            );

            $now = time();

            foreach (new RecursiveIteratorIterator($it) as $file) {
                if (in_array(Tools::strtolower(array_pop(explode('.', $file))), $display)) {
                    $file_path = trim($file);

                    $dateGeneration = filemtime($file_path);
                    $age = ($now - $dateGeneration);

                    // file age
                    // 5184000 seconds = 60 days (60 * 24 * 3600)
                    if ($age > 5184000) {
                        unlink($file_path);
                    }
                }
            }
        }
    }
}
