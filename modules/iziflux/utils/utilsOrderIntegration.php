<?php
/**
 *  2016 Profileo
 *
 *  @author    Profileo <contact@profileo.com>
 *  @copyright 2016 Profileo
 *  @license   Profileo
 *  @link  http://www.profileo.com
 */

// Load utility classes
include_once(dirname(__FILE__) . '/IzifluxTools.php');
include_once(dirname(__FILE__) . '/utilsExport.php');
include_once(dirname(__FILE__) . '/../classes/OrderIziflux.php');
include_once(dirname(__FILE__) . '/../classes/OrderIzifluxFile.php');
include_once(dirname(__FILE__) . '../../lib/SpecificCustomers.php');

/**
 * Class OrderIntegration to add new orders from Iziflux
 */
class OrderIntegration
{
    /**
     * Function to create new orders
     */
    public static function executeOrderIntegration()
    {
        if (Tools::getIsset('nbHeures')) {
            $nbHeures = Tools::getValue('nbHeures');

            for ($nbHeurePourTime = 0; $nbHeurePourTime < $nbHeures; $nbHeurePourTime ++) {
                $urlAppelCurl =
                    $_SERVER["HTTP_HOST"] .
                    $_SERVER["SCRIPT_NAME"] .
                    "?date=" . date(
                        "Y-m-d-H-i-s",
                        mktime(
                            date('H') - $nbHeurePourTime,
                            0,
                            0,
                            date('m'),
                            date('d'),
                            date('Y')
                        )
                    );
                $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443)
                ? "https://" : "http://";
                if (substr_count($urlAppelCurl, 'http') == 0) {
                    $urlAppelCurl = $protocol . $urlAppelCurl;
                }
                $curl = curl_init();
                curl_setopt($curl, CURLOPT_URL, $urlAppelCurl);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_exec($curl);
                curl_close($curl);
                sleep(1);
            }
            exit();
        }

        // Force suppression du blockage temporaire
        if (Tools::getValue('menage') == 1) {
            unlink('./importCommandeEnCours');
        }

        if (file_exists("./importCommandeEnCours")) {
            $file_creation_time = filemtime("./importCommandeEnCours");

            // Get current time
            $current_time = time();

            $time_diff = ($current_time - $file_creation_time);
            $heure = round(($time_diff / 3600), 0);

            if (Tools::getIsset('date')) {
                $timestamp = Tools::getValue('date');
            } else {
                $timestamp = date("Y-m-d-H-i-s", mktime(date('H') - 1, 0, 0, date('m'), date('d'), date('Y')));
            }

            if ($heure > 5) {
                unlink('./importCommandeEnCours');
                IzifluxTools::logDebug(
                    'utilsOrderIntegration.php '.__LINE__.
                    ": DOUBLE CALL since $heure hour(s) + deletion of importCommandeEnCours file",
                    2
                );
            } else {
                IzifluxTools::logDebug(
                    'utilsOrderIntegration.php '.__LINE__.": DOUBLE CALL since $heure hour(s)",
                    2
                );
            }
            exit();
        } else {
            $file_importCommandeEnCours = _PS_MODULE_DIR_ . 'iziflux/importCommandeEnCours';
            $content = "";
            file_put_contents($file_importCommandeEnCours, $content);

            $GLOBALS['versionPourSwitch'] = false;
            $GLOBALS['versionPourSwitch'] = Configuration::get('PS_VERSION_DB');
            if (! ($GLOBALS['versionPourSwitch'])) {
                $GLOBALS['versionPourSwitch'] = Configuration::get('PS_INSTALL_VERSION');
            }

            // Initialise fields
            $tbl_commandes = array();
            if (! defined('TESTMODE')) {
                define('TESTMODE', "OFF");
            }
            if (! defined('REGISTERDB')) {
                define('REGISTERDB', 'ON');
            }
            if (Tools::getValue('date')) {
                $timestamp = Tools::getValue('date');
            } else {
                $timestamp = date("Y-m-d-H-i-s", mktime(date('H') - 1, 0, 0, date('m'), date('d'), date('Y')));
            }
            if (! defined('TIMESTAMP')) {
                define('TIMESTAMP', $timestamp);
            }

            $url = IzifluxTools::getRemoteFilename($_GET, $timestamp);

            IzifluxTools::testOutput('url = ' .$url);
            IzifluxTools::logDebug('utilsOrderIntegration.php '.__LINE__.": Url definition : " . $url, 2);

            // Register filename currently being analysed
            $filename = basename($url);

            // Initiate import
            $newOrderIzifluxFile = new OrderIzifluxFile();
            $newOrderIzifluxFile->file = $filename;
            $newOrderIzifluxFile->date_add = date("Y-m-d H:i:s");
            $newOrderIzifluxFile->save();

            // Set permissions to directory import
            $pathToDir = _PS_ROOT_DIR_ . '/modules/iziflux/import';
            if (! (Configuration::get('IZIFLUX_ACCOUNT') == 'monsieurcyberman')) {
                chmod($pathToDir, 0755);
            }

            $importOrderInfos = Tools::file_get_contents($url);

            $account = Configuration::get('IZIFLUX_ACCOUNT');
            if ($importOrderInfos) {
                $fp = fopen(
                    dirname(__FILE__) .
                    "/../import/prestashop_" .
                    $account . "_" . $timestamp . ".txt",
                    'w'
                );
                fwrite($fp, $importOrderInfos);
                fclose($fp);
            }
            $file = file(
                dirname(__FILE__) .
                "/../import/prestashop_" . $account . "_" .
                $timestamp . ".txt"
            );

            if ($file === false) {
                $file = file($url);
            }

            $local_file = './import/prestashop_' . $account . '_' .
                $timestamp . '.txt';
            IzifluxTools::testOutput('Local file: '. $local_file, $file);
            IzifluxTools::logDebug(
                'utilsOrderIntegration.php '.__LINE__.": local file: ./import/prestashop_" . $account . "_" .
                $timestamp . ".txt<br>",
                2
            );

            // Foreach line in order, create a two dimensional array
            // with order id as key and products ordered as value
            $tbl_commandes = IzifluxTools::transformIntoArrayOrders($file);

            // Transform infos in txt files into array(id_order => array(element => attribut))

            // Each order
            $num_ligneCommande = 0;

            //IzifluxTools::logDebug(print_r($tbl_commandes, true), 0);
            IzifluxTools::testOutput('tbl_commandes: ', $tbl_commandes);
            foreach ($tbl_commandes as $order) {
                if (Tools::getIsset('testCarrier')) {
                    $carrier_name = $order[0][30];
                    $id_carrier = IzifluxTools::getCarrierByName($carrier_name);
                    continue;
                }

                // Continue if order and product have already been processed

                // Ignore first line as it contains headers
                $num_ligneCommande ++;
                if ($num_ligneCommande <= 1) {
                    continue;
                }
                $count_commande = 0;
                $count_commande = count($order);
                for ($uio = 0; $uio <= $count_commande; $uio ++) {
                    if (trim($order[$uio][0])=="" && trim($order[$uio][2])=="") {
                        unset($order[$uio]);
                    } else {
                        if (OrderIziflux::iziFluxOrderExist($order[$uio][0], $order[$uio][2])) {
                            IzifluxTools::testOutput('Order "'. $order[$uio][0] . '" and product ' . $order[$uio][2]);
                            IzifluxTools::testOutput(' already exists');
                            unset($order[$uio]);
                        }
                    }
                }
                $order = array_values($order);

                if (empty($order)) {
                    continue;
                }
                if ($order[0][0] == '') {
                    continue;
                }
                if ($order[0][2] == '') {
                    continue;
                }

                $toLog = "The order '" . $order[0][0] . "' and product '" . $order[0][2] . "' not yet created ";
                IzifluxTools::testOutput($toLog);
                IzifluxTools::logDebug('utilsOrderIntegration.php '.__LINE__.': '.$toLog, 2);

                // Initialise fields

                // Retriever id_order_state where template == payment
                $result_id_order_state = Db::getInstance()->executeS("SELECT id_order_state
                FROM " . _DB_PREFIX_ . "order_state_lang WHERE template = 'payment' LIMIT 0,1;");
                if ($result_id_order_state['id_order_state'] != '') {
                    $id_order_state = $result_id_order_state['id_order_state'];
                } else {
                    // Defines that order has already been paid (Payment accepted status)
                    $id_order_state = 2;
                }
                if (Configuration::get('IZIFLUX_ACCOUNT') == 'maquillageetcosmetique') {
                    // Order currently being processed
                    $id_order_state = 3;
                }

                $data_nom = array();

                if ($data_nom) {
                }

                $invoice_email = '';
                $invoice_lastname = '';
                $invoice_firstname = '';
                $carrier_name = $order[0][30];
                $id_carrier = IzifluxTools::getCarrierByName($carrier_name);

                if (! ($id_carrier >= 0)) {
                    $id_carrier = Tools::getValue('default_carrier', Configuration::get('IZIFLUX_DEFAULT_CARRIER'));
                }
                if (method_exists('SpecificCustomers', Configuration::get('IZIFLUX_ACCOUNT') . '_getCarrierId')) {
                    $id_carrier = call_user_func(array(
                        'SpecificCustomers',
                        Configuration::get('IZIFLUX_ACCOUNT') . '_getCarrierId'
                    ), array(
                        $order[0][1],
                        $order[0][30],
                        $order[0][31]
                    ));
                }


                // Determine country of origin for expertbynet
                $matchPays = array();

                if (preg_match("/amazon/i", $order[0][31])) {
                    if (Configuration::get('IZIFLUX_ACCOUNT') == 'expertbynet') {
                        preg_match("/marketplace.amazon.(*.)$/", $invoice_email, $matchPays);
                        /* on réaffecte la variable 31 de toutes les lignes de la commandes
                        à la nouvelle valeur de la marketplace */
                        $tailleTabCommande=count($order);
                        for ($cptTabCommande=0; $cptTabCommande<$tailleTabCommande; $cptTabCommande++) {
                            $order[$cptTabCommande][31]="Amazon.".$matchPays[1];
                        }
                    } elseif (Configuration::get('IZIFLUX_ACCOUNT') == 'expertbynetnew') {
                        preg_match("/marketplace.amazon.(*.)$/", $invoice_email, $matchPays);
                        /* on réaffecte la variable 31 de toutes les lignes de la commandes
                        à la nouvelle valeur de la marketplace */
                        $tailleTabCommande=count($order);
                        for ($cptTabCommande=0; $cptTabCommande<$tailleTabCommande; $cptTabCommande++) {
                            $order[$cptTabCommande][31]="Amazon.".$matchPays[1];
                        }
                    }
                }

                $paymentMethod = $order[0][6] . " " . $order[0][31];
                if (! $paymentMethod) {
                    $paymentMethod = "standard";
                }
                if (method_exists(
                    'SpecificCustomers',
                    Configuration::get('IZIFLUX_ACCOUNT') . '_getPaymentMethod'
                )) {
                    $paymentMethod = call_user_func(array(
                        'SpecificCustomers',
                        Configuration::get('IZIFLUX_ACCOUNT') . '_getPaymentMethod'
                    ), array());
                }
                $date_commande = date("Y-m-d H:i:s", strtotime($order[0][4]));
                if ($order[0][39]) {
                    if (method_exists('Currency', 'getIdByIsoCodeNum')) {   // <= ps.1.6.x
                        $id_currency = Currency::getIdByIsoCodeNum($order[0][39]);
                    } else {                                                // >= ps.1.7.x
                        $id_currency = IzifluxTools::getIdByIsoCodeNum($order[0][39]);
                    }
                }
                $id_currency = $id_currency ? $id_currency : (int) (Configuration::get('PS_CURRENCY_DEFAULT'));
                $id_lang = Configuration::get('PS_LANG_DEFAULT');
                $GLOBALS['idLang'] = $id_lang;

                // START : Initialise fields for registering customers

                $invoice_email = trim($order[0][18]);
                if (method_exists(
                    'SpecificCustomers',
                    Configuration::get('IZIFLUX_ACCOUNT') .'_getTransformedEmail'
                )) {
                    $invoice_email = call_user_func(array(
                        'SpecificCustomers',
                        Configuration::get('IZIFLUX_ACCOUNT') . '_getTransformedEmail'
                    ), array(
                        $order[0][31],
                        $order[0][0],
                        $order[0][18]
                    ));
                }

                if (preg_match("/amazon/i", $order[0][31])) {
                    $invoice_email = $order[0][0] . '_' . date("YmdHis") . '@noreply.fr';
                } elseif ($invoice_email == '') {
                    $invoice_email = $order[0][0] . '_' . date("YmdHis") . '@noreply.fr';
                }

                IzifluxTools::testOutput('invoice_email = ' . $invoice_email. ' mail ::');

                if (! Validate::isEmail($invoice_email)) {
                    continue;
                }

                // check if Configuration::get('IZIFLUX_ACCOUNT') . '_getTransformedEmail' function exists in
                // SpecificCustomers class
                if (method_exists(
                    'SpecificCustomers',
                    Configuration::get('IZIFLUX_ACCOUNT') .'_getTransformedEmail'
                )) {
                    $invoice_email = call_user_func(array(
                        'SpecificCustomers',
                        Configuration::get('IZIFLUX_ACCOUNT') . '_getTransformedEmail'
                    ), array(
                        $order[0][31],
                        $order[0][0],
                        $order[0][18]
                    ));
                }

                if ($invoice_email == '') {
                    $invoice_email = $order[0][0] . '_' . date("YmdHis") . '@noreply.fr';
                }
                if (! Validate::isEmail($invoice_email)) {
                    continue;
                }

                $clientInfo = self::computeInvoiceName($order[0][8], $order[0][7], $order[0][9]);

                $invoice_lastname = $clientInfo['0'];
                $invoice_firstname = $clientInfo['1'];

                $id_group = $invoice_address_id = $shipping_address_id = $id_order = 0;

                $toLog = 'invoice_firstname : #'.$invoice_firstname.'#invoice_lastname: #'.$invoice_lastname.'#';
                IzifluxTools::testOutput($toLog);
                // Manage group
                // Add iziflux_ebay, iziflux_amazon, iziflux_rueducommerce,
                // iziflux_pixmania, iziflux_cdiscount... if
                // not present in group table
                $id_group = IzifluxTools::addGroup($order[0][31]);
                IzifluxTools::logDebug(
                    'utilsOrderIntegration.php '.__LINE__.
                    ": Registering and retrieving of group OK. id_group = "
                    . $id_group,
                    2
                );
                IzifluxTools::testOutput('id_group :', $id_group);

                if (Configuration::get('IZIFLUX_ACCOUNT') == 'monsieurcyberman') {
                    $invoice_lastname = "$invoice_lastname $invoice_firstname";
                    $invoice_firstname = " - ";
                }

                // Register or retrieve customer
                $customer = IzifluxTools::addCustomer(
                    $invoice_email,
                    $invoice_lastname,
                    $invoice_firstname,
                    $id_group
                );
                IzifluxTools::testOutput('customer_id :', $customer->id);
                IzifluxTools::logDebug(
                    'utilsOrderIntegration.php '.__LINE__.": Registering client : invoice_email: $invoice_email,
                    invoice_lastname: $invoice_lastname,
                    invoice_firstname: $invoice_firstname,
                    id_group: $id_group",
                    2
                );
                IzifluxTools::logDebug(
                    'utilsOrderIntegration.php '.__LINE__.
                    ": Registering/Retrieval of client OK. customer_id = "
                    . $customer->id,
                    2
                );

                $invoice_adress_id = '';

                // Register invoice address
                if (REGISTERDB == 'ON') {
                    $invoice_address_id = Export::createAddress($order[0], 'i-', $customer->id);
                }
                $msg = "Register/retrieve invoice address OK. invoice_adress_id = ";
                IzifluxTools::logDebug('utilsOrderIntegration.php '.__LINE__.": ".$msg . $invoice_adress_id, 2);

                // Register shipping address

                $shipping_address_id = Export::createAddress($order[0], 's-', $customer->id);
                $msg = "Register/retrieve shipping address OK. shipping_address_id = ";
                IzifluxTools::logDebug('utilsOrderIntegration.php '.__LINE__.": ".$msg . $shipping_address_id, 2);

                // Calculate order total weight and insert into order_carrier table
                $orderTotalWeight = 0;
                $line = array();
                foreach ($order as $product) {
                    $count = 0;
                    foreach ($product as $productValue) {
                        $line[$count] = trim($productValue);
                        $count ++;
                    }

                    $isCombination = false;
                    $cart_qty = $product[32];
                    $nom_article = addslashes(Tools::substr($product[3], 0, 255));
                    if (preg_match("/#IZI#/", $product[1]) !== 0) {
                        $data_new_reference = array();
                        $data_new_reference = explode("#IZI#", $product[1]);
                        $product[1] = $data_new_reference[1];
                        IzifluxTools::testOutput('REFERENCE', $data_new_reference);
                        $isCombination = true;
                    } elseif (preg_match("/IIIZIII/", $product[1]) !== 0) {
                        $data_new_reference = array();
                        $data_new_reference = explode("IIIZIII", $product[1]);
                        $product[1] = $data_new_reference[1];
                        $isCombination = true;
                    }

                    $productInfoTable = array();
                    $productInfoTable = Export::getProductInfoAndDecrement($product[1], $cart_qty, $isCombination);

                    if ($isCombination) {
                        if ($productInfoTable['weight'] <= "0") {
                            // If combination weight <= 0, retrieve parent product weight
                            $extractIdProduct = IzifluxTools::getIdProductIdProductAttribute($product[1]);
                            $extractIdProduct = $extractIdProduct["id_product"];
                            $req_recup_weight_product = 'SELECT weight FROM `' . _DB_PREFIX_ . 'product`
                            WHERE `id_product` = "' . (int)$extractIdProduct . '";';
                            $result_recup_weight_product = Db::getInstance()->executeS($req_recup_weight_product);
                            $productInfoTable['weight'] = $result_recup_weight_product["weight"];
                            if (is_array($productInfoTable['weight'])) {
                                $productInfoTable['weight'] = $result_recup_weight_product[0]["weight"];
                            }
                            $msg = "retrieve weight if is combination and combination field is empty : req: ";
                            $toLog = $msg . $req_recup_weight_product . " => " . $productInfoTable['weight'];
                            IzifluxTools::logDebug('utilsOrderIntegration.php '.__LINE__.': '.$toLog, 2);
                        }
                    }
                    $productInfoTable['weight'] = $productInfoTable['weight'] * $cart_qty;

                    $orderTotalWeight = $orderTotalWeight + $productInfoTable['weight'];
                }
                $orderTotalWeight = (float) (Tools::ps_round((float) ($orderTotalWeight), 6));

                // Register order
                $id_order = Export::addOrder(
                    $order,
                    $id_currency,
                    $id_carrier,
                    $id_lang,
                    $customer,
                    $shipping_address_id,
                    $invoice_address_id,
                    $paymentMethod,
                    $date_commande,
                    $orderTotalWeight
                );
                IzifluxTools::testOutput('id_order', $id_order);
                IzifluxTools::logDebug(
                    'utilsOrderIntegration.php '.__LINE__.
                    ": Order register OK. id_order = " . $id_order,
                    2
                );

                if (REGISTERDB == 'OFF') {
                    $id_order = '';
                }

                // If id_order present, then proceed
                if (isset($id_order)) {
                    // Loop for each product present in order
                    foreach ($order as $product) {
                        $count = 0;

                        foreach ($product as $productValue) {
                            $line[$count] = trim($productValue);
                            $count ++;
                        }

                        // Retrieve rows from tables product, product_attribute using reference
                        $isCombination = false;
                        $cart_qty = $product[32];

                        // Define article name
                        $nom_article = addslashes(Tools::substr($product[3], 0, 255));
                        if (preg_match("/#IZI#/", $product[1]) !== 0) {
                            $data_new_reference = array();
                            $data_new_reference = explode("#IZI#", $product[1]);
                            $product[1] = $data_new_reference[1];
                            IzifluxTools::testOutput('REFERENCE', $data_new_reference);
                            $isCombination = true;
                        } elseif (preg_match("/IIIZIII/", $product[1]) !== 0) {
                            $data_new_reference = array();
                            $data_new_reference = explode("IIIZIII", $product[1]);
                            $product[1] = $data_new_reference[1];
                            $isCombination = true;
                        }

                        $account = Configuration::get('IZIFLUX_ACCOUNT');
                        if (method_exists('SpecificCustomers', $account . '_getNewReference')) {
                            $result = call_user_func(array(
                                'SpecificCustomers',
                                Configuration::get('IZIFLUX_ACCOUNT') . '_getNewReference'
                            ), array(
                                $product[1]
                            ));

                            if (! empty($result)) {
                                $product[1] = $result['product'];
                                $isCombination = $result['isDeclinaison'];
                            }
                        }

                        $productInfoTable = array();
                        $productInfoTable = Export::getProductInfoAndDecrement(
                            $product[1],
                            $cart_qty,
                            $isCombination,
                            true
                        );

                        if ($isCombination) {
                            if ($productInfoTable['weight'] <= "0") {
                                // If weight <= 0 for a combination, then retrieve weight of parent product
                                $extractIdProduct = IzifluxTools::getIdProductIdProductAttribute($product[1]);
                                $extractIdProduct = $extractIdProduct["id_product"];
                                $req_recup_weight_product = 'SELECT weight FROM `' . _DB_PREFIX_ . 'product`
                                WHERE `id_product` = "' . (int)$extractIdProduct . '";';
                                $result_recup_weight_product =
                                Db::getInstance()->executeS($req_recup_weight_product);
                                $productInfoTable['weight'] = $result_recup_weight_product["weight"];
                                if (is_array($productInfoTable['weight'])) {
                                    $productInfoTable['weight'] = $result_recup_weight_product[0]["weight"];
                                }
                                $msg = "retrieve combination weight if combination weight is empty: req: ";
                                $msg .= $req_recup_weight_product . "=> " . $productInfoTable['weight'];
                                IzifluxTools::logDebug('utilsOrderIntegration.php '.__LINE__.': '.$msg, 2);
                            }
                        } else {    /* bloc else retrouv?chez un client */
                            if ($productInfoTable['weight'] <= "0") {
                                // If weight <= 0 for a combination, then retrieve weight of parent product
                                $extractIdProduct = IzifluxTools::getIdProductIdProductAttribute($product[1]);
                                $extractIdProduct = $extractIdProduct["id_product"];
                                $req_recup_weight_product = 'SELECT weight FROM `' . _DB_PREFIX_ . 'product`
                                WHERE `id_product` = "' . (int)$extractIdProduct . '";';
                                $result_recup_weight_product =
                                Db::getInstance()->executeS($req_recup_weight_product);
                                $productInfoTable['weight'] = $result_recup_weight_product["weight"];
                                if (is_array($productInfoTable['weight'])) {
                                    $productInfoTable['weight'] = $result_recup_weight_product[0]["weight"];
                                }
                                $msg = "retrieve combination weight if combination weight is empty: req: ";
                                $msg .= $req_recup_weight_product . "=> " . $productInfoTable['weight'];
                                IzifluxTools::logDebug('utilsOrderIntegration.php '.__LINE__.': '.$msg, 2);
                            }
                        }
                        IzifluxTools::testOutput('productInfoTable', $productInfoTable);
                        $toLog = 'objet_produit_id::' . $productInfoTable['id_product'] . ':: product : ';
                        IzifluxTools::testOutput($toLog, $product);
                        IzifluxTools::logDebug(
                            'utilsOrderIntegration.php '.__LINE__.
                            ": Register/retrieval of product OK :",
                            2
                        );
                        $reference = '';

                        IzifluxTools::logDebug(
                            'utilsOrderIntegration.php '.__LINE__.":   productId = " .
                            $productInfoTable['id_product'],
                            2
                        );
                        IzifluxTools::logDebug(
                            'utilsOrderIntegration.php '.__LINE__.
                            ":   reference = " . $reference,
                            2
                        );

                        // Retrieve tax information of current country, from where product is ordered
                        $detailsTax = array();
                        $detailsTax = IzifluxTools::getTaxInfoDetails($productInfoTable['id_product']);

                        // Register order details
                        $quantityInStock =
                        ($productInfoTable['quantity'] - (int) ($cart_qty) < 0) ?
                        $productInfoTable['quantity'] : (int) ($cart_qty);

                        Export::addOrderDetails(
                            $id_order,
                            $product,
                            $productInfoTable,
                            $nom_article,
                            $cart_qty,
                            $quantityInStock,
                            $detailsTax
                        );

                        IzifluxTools::logDebug(
                            'utilsOrderIntegration.php '.__LINE__.
                            ": Save of ordered product OK.",
                            2
                        );

                        // Register order_iziflux infos
                        $tbl_order_iziflux = array(
                            'id_order' => $id_order,
                            'id_iziflux_order' => $product[0],
                            'id_product' => $productInfoTable['id_product'],
                            'reference_product_market' => $product[2],
                            'transaction' => $product[40],
                            'market_place' => $product[31],
                            'carrier_name' => $product[30],
                            'quantity' => $cart_qty
                        );

                        IzifluxTools::testOutput('tbl_order_iziflux :', $tbl_order_iziflux);
                        OrderIziflux::iziFluxNewOrder(
                            $tbl_order_iziflux['id_order'],
                            $tbl_order_iziflux['id_iziflux_order'],
                            $tbl_order_iziflux['id_product'],
                            $tbl_order_iziflux['reference_product_market'],
                            $tbl_order_iziflux['transaction'],
                            $tbl_order_iziflux['market_place'],
                            $tbl_order_iziflux['carrier_name'],
                            $tbl_order_iziflux['quantity']
                        );

                        $account = Configuration::get('IZIFLUX_ACCOUNT');
                        if (method_exists('SpecificCustomers', $account . '_updateOrderDetailsAndHistory')) {
                            call_user_func(array(
                                'SpecificCustomers',
                                Configuration::get('IZIFLUX_ACCOUNT') . '_updateOrderDetailsAndHistory'
                            ), array(
                                $id_order
                            ));
                        }
                    }

                    // Register order_history
                    // Very Important
                    // Need to reinitialise Context::getContext()->smarty else it will cause issues with other
                    // modules
                    // eg: issue with _clearCache in module blockbestsellers
                    // Context::getContext()->smarty = new SmartyCustom();

                    $objOrder = new Order($id_order);
                    $history = new OrderHistory();
                    $history->id_order = (int) $objOrder->id;
                    $history->changeIdOrderState($id_order_state, (int) ($objOrder->id));
                    $history->add();

                    // Register payment mode in order_payment table
                    // Retrieve order reference from orders table using id_order
                    $sql = 'SELECT `reference`,`total_paid`
                    FROM `' . _DB_PREFIX_ . 'orders`
                    WHERE `id_order` ="' . $id_order . '"';
                    IzifluxTools::logDebug(
                        'utilsOrderIntegration.php '.__LINE__.
                        ": retrieve payment mode order reference." . $sql,
                        2
                    );
                    $res = Db::getInstance()->executeS($sql);
                    if ($res[0]['reference']) {
                        $account = Configuration::get('IZIFLUX_ACCOUNT');
                        if (method_exists('SpecificCustomers', $account . '_getPaymentMethod')) {
                            $product[31] = call_user_func(array(
                                'SpecificCustomers',
                                Configuration::get('IZIFLUX_ACCOUNT') . '_getPaymentMethod'
                            ), array());
                        }

                        $sql = 'UPDATE `' . _DB_PREFIX_ . 'order_payment`
                        SET `payment_method` = "' . $product[6] . ' ' . $product[31] . '"
                        WHERE `' . _DB_PREFIX_ . 'order_payment`.`order_reference` ="' .
                        pSQL($res[0]['reference']) . '";';

                        IzifluxTools::logDebug(
                            'utilsOrderIntegration.php '.__LINE__.
                            ": Update payment mode." . $sql,
                            2
                        );

                        if (REGISTERDB == 'ON') {
                            Db::getInstance()->Execute($sql);
                        }
                        $sql = 'select `payment_method` FROM `' . _DB_PREFIX_ . 'order_payment`
                        WHERE `' . _DB_PREFIX_ . 'order_payment`.`order_reference` ="' .
                        pSQL($res[0]['reference']) . '";';
                        $res2 = Db::getInstance()->executeS($sql);

                        IzifluxTools::logDebug(
                            'utilsOrderIntegration.php '.__LINE__.
                            ": Payment mode request verification. " . $sql,
                            2
                        );
                        $toLog = "Payment verification method request results. " . $res2[0]['payment_method'];
                        IzifluxTools::logDebug('utilsOrderIntegration.php '.__LINE__.': '.$toLog, 2);
                        if ($res2[0]['payment_method'] !== $product[6] . ' ' . $product[31]) {
                            // If update fails, the do insert
                            $sql = 'INSERT INTO `' . _DB_PREFIX_ . 'order_payment` ( `order_reference`,
                                `id_currency`, `amount`, `payment_method`, `conversion_rate`,`transaction_id`,
                            `card_number`, `card_brand`, `card_expiration`, `card_holder`, `date_add`)
                            VALUES(\'' . pSQL($res[0]['reference']) . '\', \'' . (int)$id_currency . '\',
                                \'' . (float)$res[0]['total_paid'] . '\', \'' .
                                pSQL($product[31]) . '\', \'1.000000\', \'\',
                                \'\', \'\', \'\', \'\', \'' . date('Y-m-d H:i:s') . '\');';
                            if (REGISTERDB == 'ON') {
                                Db::getInstance()->execute($sql);
                            }

                            IzifluxTools::logDebug(
                                'utilsOrderIntegration.php '.__LINE__.
                                ": Payment verification method request 2
                                // insertion" . $sql,
                                2
                            );
                            $ref = $res[0]['reference'];
                            $sql = 'select `payment_method` FROM `' . _DB_PREFIX_ . 'order_payment`
                            WHERE `' . _DB_PREFIX_ . 'order_payment`.`order_reference` ="' . pSQL($ref) . '";';
                            $res2 = Db::getInstance()->executeS($sql);
                            IzifluxTools::logDebug(
                                'utilsOrderIntegration.php '.__LINE__.
                                ": Payment verification method request 2."
                                . $sql,
                                2
                            );
                            $toLog = "Payment verification method request 2 results." . $res2[0]['payment_method'];
                            IzifluxTools::logDebug('utilsOrderIntegration.php '.__LINE__.": ".$toLog, 2);
                        }
                    }
                }
            }


            IzifluxTools::logDebug('utilsOrderIntegration.php '.__LINE__.": End of analysis.", 2);
            unlink('./importCommandeEnCours');

            if (method_exists('SpecificCustomers', Configuration::get('IZIFLUX_ACCOUNT') . '_getExecuteCurl')) {
                call_user_func(array(
                    'SpecificCustomers',
                    Configuration::get('IZIFLUX_ACCOUNT') . '_getExecuteCurl'
                ), array(
                    Configuration::get('IZIFLUX_ACCOUNT'),
                    Tools::getIsset('compteSupp')
                ));
            }
        }
    }

    /**
     *
     * Function computeInvoiceName
     *
     * @param array $clientName
     * @param array $clientName2
     * @param array $clientFirstName
     *
     */
    public static function computeInvoiceName($clientName, $clientName2, $clientFirstName)
    {
        $result = array();

        $invoice_lastname = $clientName;
        if (trim($invoice_lastname) == '') {
            $invoice_lastname = $clientName2;
        }
        $invoice_lastname = preg_replace(array(
            '/[0-9!<>,;?=+()@#"°{}_$%:~]/ui',
            '/mr /i',
            '/mme /i',
            '/mlle /i',
            '/melle /i',
            '/mll /i'
        ), array(
            '',
            '',
            '',
            '',
            '',
            ''
        ), $invoice_lastname);
        if (preg_match("/ /", $invoice_lastname) !== 0) {
            $data_nom = explode(" ", $invoice_lastname);
        }

        if (trim($clientFirstName) != '') {
            $invoice_firstname = $clientFirstName;
        } elseif (! empty($data_nom)) {
            $invoice_lastname = $data_nom[0];
            $invoice_firstname = $data_nom[1];
        } else {
            $invoice_firstname = $clientName2;
        }
        $invoice_firstname = preg_replace('/[0-9!<>,;?=+()@#"°{}_$%:~]/ui', "", $invoice_firstname);
        $invoice_lastname = ucwords(Tools::strtolower(trim($invoice_lastname)));
        $invoice_firstname = ucwords(Tools::strtolower(trim($invoice_firstname)));
        if ($invoice_lastname == "") {
            if ($invoice_firstname == "") {
                $invoice_firstname = $invoice_lastname = "non communique";
            } else {
                $invoice_lastname = $invoice_firstname;
            }
        }
        if ($invoice_firstname == "") {
            if ($invoice_lastname == "") {
                $invoice_lastname = $invoice_firstname = "non communique";
            } else {
                $invoice_firstname = $invoice_lastname;
            }
        }
        if (trim($invoice_firstname) == "") {
            $invoice_firstname = "non communique";
        }

        $result['0'] = $invoice_lastname;
        $result['1'] = $invoice_firstname;

        return $result;
    }
}
