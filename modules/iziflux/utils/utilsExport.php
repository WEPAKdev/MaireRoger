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
 * Class Export to generate txt file of all orders to send to Iziflux
 */
class Export
{
    /**
     * Function in charge to export all the sellables products from Prestashop
     */
    public static function executeExport()
    {
        // Initialise variables
        define('SEP', '|');
        $link = new Link();
        $file_export = _PS_MODULE_DIR_ . 'iziflux/export.txt';
        $max_cat_children = 10;
        $handle = fopen($file_export, 'w+');

        // clear log
        IzifluxTools::clearLogFile(_PS_MODULE_DIR_ . 'iziflux/log.txt');

        if (! Configuration::get('PS_SHOP_DEFAULT')) {
            IzifluxTools::logDebug('utilsExport.php '.__LINE__.': UtilsExport: no PS_SHOP_DEFAULT exiting', 0);
            die();
        }

        // Carriers and localization
        $carrier = new Carrier(Configuration::get('IZIFLUX_DEFAULT_CARRIER'), Configuration::get('PS_LANG_DEFAULT'));
        $allPrestashopCarriers = Carrier::getCarriers(
            Configuration::get('PS_LANG_DEFAULT'),
            false,
            false,
            false,
            null,
            5
        );
        $allPrestashopCarriersIds = IzifluxTools::getAllPrestashopCarriersIds($allPrestashopCarriers);
        $countryLoaded = new Country(Configuration::get('PS_COUNTRY_DEFAULT'));
        $id_zone = $countryLoaded->id_zone;
        $liste_carrier_header = "";
        foreach ($allPrestashopCarriersIds as $carrierLocal) {
            $carrierLocalObject = new Carrier($carrierLocal, Configuration::get('PS_LANG_DEFAULT'));
            $liste_carrier_header .=
                SEP . trim(
                    preg_replace(
                        "/[^a-z0-9]/i",
                        "",
                        IzifluxTools::winToAscii(
                            trim(
                                Tools::strtolower(
                                    utf8_decode(
                                        $carrierLocalObject->name
                                    )
                                )
                            )
                        )
                    )
                );
        }

        // Load all products
        $query = 'SELECT p.additional_shipping_cost, p.ean13, ps.id_category_default,
            p.id_product,p.reference,p.supplier_reference, p.out_of_stock, p.price,
            p.quantity, p.weight,
            pl.available_later, pl.link_rewrite, pl.meta_keywords,
            pl.description, pl.description_short, pl.name, p.id_manufacturer
            FROM `'._DB_PREFIX_.'product` p
            JOIN `'._DB_PREFIX_.'product_shop` ps
                ON (
                    ps.id_product = p.id_product AND
                    ps.`id_shop` = '.(int)Context::getContext()->shop->id.'
                )
            LEFT JOIN `'._DB_PREFIX_.'product_lang` pl
                ON pl.id_product = p.id_product
            LEFT JOIN `'._DB_PREFIX_.'image` pi
                ON pi.id_product = p.id_product
            WHERE p.active = 1
            AND pi.cover = 1
            AND pl.`id_shop` = '.(int)Context::getContext()->shop->id.'
            AND pl.id_lang = '.(int)Configuration::get('PS_LANG_DEFAULT');

        $products = Db::getInstance()->executeS($query);

        $nbTotalProduct = count($products);

        // Load the features and the headers
        $features_header = Feature::getFeatures(Configuration::get('PS_LANG_DEFAULT'));
        $features_use = array();
        $features_name_list = array();
        foreach ($features_header as $feature_header) {
            $featureValues = FeatureValue::getFeatureValues($feature_header['id_feature']);
            if (count($featureValues) > 0) {
                $features_use[] = $feature_header['id_feature'];
                $features_name_list[$feature_header['id_feature']] =
                trim(
                    preg_replace(
                        "/[^a-z0-9]/i",
                        "",
                        IzifluxTools::winToAscii(
                            trim(
                                Tools::strtolower(
                                    utf8_decode(
                                        $feature_header['name']
                                    )
                                )
                            )
                        )
                    )
                );
            }
        }

        IzifluxTools::logDebug(
            'utilsExport.php '.__LINE__.
            ': UtilsExport: total products to be exported: ' . $nbTotalProduct,
            0
        );

        // Write file headers
        // Specific customer handle
        if (method_exists('SpecificCustomers', Configuration::get('IZIFLUX_ACCOUNT') . '_getHeaderArray')) {
            $headerArray = call_user_func(array(
                'SpecificCustomers',
                Configuration::get('IZIFLUX_ACCOUNT') . '_getHeaderArray'
            ), array());
        } else {
            $headerArray = Export::getHeaderArray();
        }

        $headerToWrite = implode(SEP, $headerArray) . chr(10);
        fwrite($handle, $headerToWrite);

        $j = 0;

        // Extract the languages of the shop
        $tabIdLang = array();
        $tabIdLang = getIdLang();
        $traductions = array();

        $tabFeatureLang = array();
        $tabFeatureLang = getFeatureLang();
        $tabFeatureValueLang = array();
        $tabFeatureValueLang = getFeatureValueLang();

        // Loop on all products
        foreach ($products as $product) {
            $traductionsCaracteristiques = array();
            $j ++;

            // We get the existing translations of the product
            foreach ($tabIdLang as $idLang => $isoCode) {
                $queryTrad ='SELECT name,description_short,description
                                FROM `'._DB_PREFIX_.'product_lang`
                                WHERE id_lang='.$idLang.'
                                    AND name<>""
                                    AND id_product="'.$product['id_product'].'"';
                $texteTrad = Db::getInstance()->ExecuteS($queryTrad);
                $traductions[$isoCode]=$texteTrad[0];
            }

            /* extract trads caracteristiques */
            foreach ($tabIdLang as $idLang => $isoCode) {
                $queryTrad ='SELECT *
                               FROM `'._DB_PREFIX_.'feature_product`
                               WHERE `id_product` = "'.$product['id_product'].'"';
                $texteTrad = Db::getInstance()->ExecuteS($queryTrad);
                foreach ($texteTrad as $valeur) {
                    $value = "#==#tradizi_".$tabFeatureLang[$valeur["id_feature"]][$idLang];
                    $value .= "_".$isoCode.":".$tabFeatureValueLang[$valeur["id_feature_value"]][$idLang];
                    $traductionsCaracteristiques[$isoCode] .= $value;
                }
            }

            // Product Shipping price
            $shippingPrice = "";
            $shippingPriceFinal = "";
            foreach ($allPrestashopCarriersIds as $carrierLocal) {
                $carrierLocalObject = new Carrier($carrierLocal, Configuration::get('PS_LANG_DEFAULT'));
                $shippingPrice .= SEP . IzifluxTools::getShippingPrice(
                    $carrierLocalObject,
                    $product['weight'],
                    $product['price'],
                    (isset($product['additional_shipping_cost']) ?
                        $product['additional_shipping_cost'] : 0),
                    $id_zone
                );

                $shippingPriceFinal .= trim(
                    preg_replace(
                        "/[^a-z0-9]/i",
                        "",
                        IzifluxTools::winToAscii(
                            trim(
                                Tools::strtolower(
                                    utf8_decode(
                                        $carrierLocalObject->name
                                    )
                                )
                            )
                        )
                    )
                ) . ":" . IzifluxTools::getShippingPrice(
                    $carrierLocalObject,
                    $product['weight'],
                    $product['price'],
                    (isset($product['additional_shipping_cost']) ?
                        $product['additional_shipping_cost'] : 0),
                    $id_zone
                ) . "#==#";
            }
            if (preg_match("/#==#$/", $shippingPriceFinal)) {
                $shippingPriceFinal = Tools::substr($shippingPriceFinal, 0, - 4);
            }

            IzifluxTools::logDebug('utilsExport.php '.__LINE__.':
                UtilsExport: exporting ' . $j . ' / ' . $nbTotalProduct . ' products', 0);

            $productSave = $product;

            if (! $product) {
                IzifluxTools::logDebug(
                    'utilsExport.php '.__LINE__.': UtilsExport: export product failed, could not load id product : '
                    . $product['id_product'],
                    0
                );
                $product = $productSave;
                continue;
            } else {
                IzifluxTools::logDebug(
                    'utilsExport.php '.__LINE__.': UtilsExport: starting export of id product : '
                    . $product['id_product'],
                    0
                );
            }

            // Get the tags for keywords if keywords are empty
            if (trim($product['meta_keywords']) == "") {
                $tagList = Tag::getProductTags($product['id_product']);
                if (is_array($tagList)) {
                    foreach ($tagList as $tabTag) {
                        foreach ($tabTag as $valTag) {
                            $product['meta_keywords'] .= $valTag . "#-#";
                        }
                    }
                    $product['meta_keywords'] = preg_replace("/#-#$/", "", $product['meta_keywords']);
                }
            }

            $product = SpecificCustomers::getCategoryDefaultMobilierShop($product);
            $product = SpecificCustomers::getInfosWaterConcept($product, $id_zone, $carrier);

            // Get product classification (category tree)
            $classification = '';
            $category_tab = IzifluxTools::getAllPrestashopCategories();
            if ($category_tab[$product['id_category_default']] == '') {
                IzifluxTools::logDebug(
                    'utilsExport.php '.__LINE__.': UtilsExport: could not load category ID = ' .
                    $product['id_category_default'],
                    0
                );
            } else {
                $category = $category_tab[$product['id_category_default']];
                $cpt_cat = 0;
                while ($category['id_category'] != 0 && $category['id_category'] != 1 && $cpt_cat < $max_cat_children) {
                    $cpt_cat ++;
                    if ($classification != '') {
                        $classification = ' > ' . $classification;
                    }
                    $classification = preg_replace("/\|/", " ", $category['name']) . $classification;
                    $category = $category_tab[$category['id_parent']];
                }
            }

            if (Tools::getIsset('extractAttributes') && Tools::getValue('extractAttributes') == "0") {
                $attributes = false;
            } else {
                $attributes = IzifluxTools::getAttributesFull($product);
            }

            // Compute features
            $featuresList = '';
            $featuresList2 = '';
            $temp = array();

            $sql_feature = ('SELECT name, value, pf.id_feature
				FROM '._DB_PREFIX_.'feature_product pf
				LEFT JOIN '._DB_PREFIX_.'feature_lang fl ON (fl.id_feature = pf.id_feature
                AND fl.id_lang = '.(int)Configuration::get('PS_LANG_DEFAULT').')
				LEFT JOIN '._DB_PREFIX_.'feature_value_lang fvl ON (fvl.id_feature_value = pf.id_feature_value
                AND fvl.id_lang = '.(int)Configuration::get('PS_LANG_DEFAULT').')
				LEFT JOIN '._DB_PREFIX_.'feature f ON (f.id_feature = pf.id_feature
                AND fl.id_lang = '.(int)Configuration::get('PS_LANG_DEFAULT').')
				'.Shop::addSqlAssociation('feature', 'f').'
				WHERE pf.id_product = '.(int)$product['id_product'].'
				ORDER BY f.position ASC');

            $features = Db::getInstance()->executeS($sql_feature);

            foreach ($features as $feature) {
                $temp[$feature['id_feature']] = $feature['value'];
            }

            foreach ($features_use as $feature_use) {
                if (isset($temp[$feature_use])) {
                    $featuresList .= SEP . IzifluxTools::htmlEscape($temp[$feature_use]);
                    $featuresList2 .=
                        $features_name_list[$feature_use] . ":" .
                        IzifluxTools::htmlEscape($temp[$feature_use]) . "#==#";
                } else {
                    $featuresList .= SEP;
                }
            }
            if (preg_match("/#==#$/", $featuresList2)) {
                $featuresList2 = Tools::substr($featuresList2, 0, - 4);
            }

            // Product Availability
            $product['available_later'] = null;
            if (! preg_match("/^0000-.*/", $product['available_date'])) {
                $product['available_later'] = round((strtotime($product['available_date']) - time()) / (3600 * 24), 0);
            } else {
                $product['available_later'] = Configuration::get('IZIFLUX_DELAY_REPLENISHING');
            }
            if ($product['available_later'] < 0) {
                $product['available_later'] = Configuration::get('IZIFLUX_DELAY_REPLENISHING');
            }

            //Product link
            $product['link'] =  $link->getProductLink($product);

            if ($attributes) {
                // Handle export attributes
                IzifluxTools::logDebug(
                    'UtilsExport: product export with attributes loaded, will start looping on them',
                    0
                );

                foreach ($attributes as $decli) {
                    $poidsDeclinaison = $decli['weight'] + $product['weight'];
                    $ean13Declinaison = "";
                    if ($decli['ean13']) {
                        $ean13Declinaison = $decli['ean13'];
                    } else {
                        $ean13Declinaison = $product['ean13'];
                    }
                    // Shipping price for the combination
                    $shippingPrice = "";
                    $shippingPriceFinal = "";
                    foreach ($allPrestashopCarriersIds as $carrierLocal) {
                        $carrierLocalObject = new Carrier($carrierLocal, Configuration::get('PS_LANG_DEFAULT'));
                        $shippingPrice .= SEP . IzifluxTools::getShippingPrice(
                            $carrierLocalObject,
                            $poidsDeclinaison,
                            $product['price'],
                            (isset($product['additional_shipping_cost']) ?
                                $product['additional_shipping_cost'] : 0),
                            $id_zone
                        );
                        $shippingPriceFinal .= trim(
                            preg_replace(
                                "/[^a-z0-9]/i",
                                "",
                                IzifluxTools::winToAscii(
                                    trim(
                                        Tools::strtolower(
                                            utf8_decode(
                                                $carrierLocalObject->name
                                            )
                                        )
                                    )
                                )
                            )
                        ) . ":" . IzifluxTools::getShippingPrice(
                            $carrierLocalObject,
                            $poidsDeclinaison,
                            $product['price'],
                            (isset($product['additional_shipping_cost']) ?
                                $product['additional_shipping_cost'] : 0),
                            $id_zone
                        ) . "#==#";
                    }
                    if (preg_match("/#==#$/", $shippingPriceFinal)) {
                        $shippingPriceFinal = Tools::substr($shippingPriceFinal, 0, - 4);
                    }

                    $featuresList2Decli = "";
                    if ($featuresList2) {
                        $featuresList2Decli = $featuresList2 . "#==#" . $decli['iziAttributeizi'];
                    } else {
                        $featuresList2Decli = $decli['iziAttributeizi'];
                    }

                    foreach ($product as $clefMenage => $valeurMenage) {
                        $product[$clefMenage] = preg_replace("/\|/", " ", $valeurMenage);
                    }

                    // Get product link with anchor for the combinations
                    $decli['link'] =
                        $product['link'] . IzifluxTools::getAnchor(
                            $decli['id_product_attribute'],
                            true,
                            $product['id_product']
                        );

                    $promo = IzifluxTools::getPromo($product, $decli['id_product_attribute']);
                    if (method_exists(
                        'SpecificCustomers',
                        Configuration::get('IZIFLUX_ACCOUNT') . '_getProductAttributeExport'
                    )) {
                        $resultsArray = call_user_func(array(
                            'SpecificCustomers',
                            Configuration::get('IZIFLUX_ACCOUNT') . '_getProductAttributeExport'
                        ), array(
                            $product,
                            $ean13Declinaison,
                            $decli,
                            $classification,
                            $carrier,
                            $promo,
                            $poidsDeclinaison,
                            $featuresList2Decli,
                            $shippingPriceFinal,
                            $id_zone
                        ));
                    } else {
                        $resultsArray = Export::getProductAttributeExport(
                            $product,
                            $ean13Declinaison,
                            $decli,
                            $classification,
                            $carrier,
                            $promo,
                            $poidsDeclinaison,
                            $featuresList2Decli,
                            $shippingPriceFinal,
                            $id_zone
                        );
                    }

                    // For each language, we had the field 37 if necessary
                    foreach ($tabIdLang as $idLang => $isoCode) {
                        if ($traductions[$isoCode]["name"]) {
                            $resultsArray[37] = $resultsArray[37]."#==#tradizi_nom_".$isoCode.":";
                            $resultsArray[37] .= cleanForTraduction($traductions[$isoCode]["name"]);
                        }
                        if ($traductions[$isoCode]["description_short"]) {
                            $resultsArray[37] = $resultsArray[37]."#==#tradizi_description_courte_";
                            $resultsArray[37] .= $isoCode.":";
                            $resultsArray[37] .= cleanForTraduction($traductions[$isoCode]["description_short"]);
                        }
                        if ($traductions[$isoCode]["description"]) {
                            $resultsArray[37] = $resultsArray[37]."#==#tradizi_description_longue_";
                            $resultsArray[37] .= $isoCode.":".cleanForTraduction($traductions[$isoCode]["description"]);
                        }
                        if ($traductionsCaracteristiques[$isoCode]) {
                            $resultsArray[37].=$traductionsCaracteristiques[$isoCode];
                        }
                    }

                    // Write to file
                    fwrite($handle, implode(SEP, $resultsArray) . chr(10));
                }
            } else {
                // Handle export product
                IzifluxTools::logDebug(
                    'utilsExport.php '.__LINE__.
                    ': UtilsExport: product export without attributes',
                    0
                );

                $links_image_mkp = "";

                // Not using image function :
                // $images = Image::getImages(Configuration::get('PS_LANG_DEFAULT'), $product['id_product']);
                // Because it does not gives priority to default image
                $sqlGetImages = 'SELECT *
					FROM `'._DB_PREFIX_.'image` i
					LEFT JOIN `'._DB_PREFIX_.'image_lang` il ON (i.`id_image` = il.`id_image`)';
                $sqlGetImages .= ' WHERE i.`id_product` = '.(int)$product['id_product'];
                $sqlGetImages .= ' AND il.`id_lang` = '.(int)Configuration::get('PS_LANG_DEFAULT').'
					ORDER BY i.`cover` DESC ,i.`position` ASC';
                $images = Db::getInstance()->executeS($sqlGetImages);

                $links_image = '';
                $protocol_link = (Configuration::get('PS_SSL_ENABLED')) ? 'https://' : 'http://';
                for ($i = 0; $i <= 4; $i ++) {
                    if ($images[$i]['id_image'] != '') {
                        $ids = $product['id_product'] . '-' . $images[$i]['id_image'];
                        $imageType = ImageType::getFormatedName('large');
                        $links_image .= $protocol_link.$link->getImageLink(
                            $product['link_rewrite'],
                            $ids,
                            $imageType
                        ) . SEP;

                        if (method_exists(
                            'SpecificCustomers',
                            Configuration::get('IZIFLUX_ACCOUNT') . '_getProductAttributeImage'
                        )) {
                            $links_image_mkp .= call_user_func(array(
                                'SpecificCustomers',
                                Configuration::get('IZIFLUX_ACCOUNT') . '_getProductAttributeImageMkp'
                            ), array(
                                $ids
                            ));
                        } else {
                            $links_image_mkp = "";
                        }
                    } else {
                        $links_image .= SEP;
                    }
                }

                if (trim($product['quantity']) == "") {
                    $product['quantity'] = "0";
                }
                $dispo = ($product['quantity'] >= 0 ? 'Y' :
                    (Product::isAvailableWhenOutOfStock($product['out_of_stock']) ? 'Y' : 'N'));
                $statut_dispo = ($product['quantity'] >= 0 ? 'S' :
                    (Product::isAvailableWhenOutOfStock($product['out_of_stock']) ? 'R' : 'V'));

                foreach ($product as $clefMenage => $valeurMenage) {
                    $product[$clefMenage] = preg_replace("/\|/", " ", $valeurMenage);
                }

                $promo = IzifluxTools::getPromo($product);

                if (method_exists('SpecificCustomers', Configuration::get('IZIFLUX_ACCOUNT') . '_getProductExport')) {
                    $resultsArray = call_user_func(array(
                        'SpecificCustomers',
                        Configuration::get('IZIFLUX_ACCOUNT') . '_getProductAttributeExport'
                    ), array(
                        $product,
                        $links_image,
                        $classification,
                        $dispo,
                        $statut_dispo,
                        $carrier,
                        $promo,
                        $featuresList2,
                        $shippingPriceFinal,
                        $links_image_mkp,
                        $id_zone
                    ));
                } else {
                    $resultsArray = Export::getProductExport(
                        $product,
                        $links_image,
                        $classification,
                        $dispo,
                        $statut_dispo,
                        $carrier,
                        $promo,
                        $featuresList2,
                        $shippingPriceFinal,
                        $id_zone
                    );
                }

                // For each language, we had the field 37 if necessary
                foreach ($tabIdLang as $idLang => $isoCode) {
                    if ($traductions[$isoCode]["name"]) {
                        $resultsArray[37] = $resultsArray[37]."#==#tradizi_nom_".$isoCode.":";
                        $resultsArray[37] .= cleanForTraduction($traductions[$isoCode]["name"]);
                    }
                    if ($traductions[$isoCode]["description_short"]) {
                        $resultsArray[37] = $resultsArray[37]."#==#tradizi_description_courte_";
                        $resultsArray[37] .= $isoCode.":";
                        $resultsArray[37] .= cleanForTraduction($traductions[$isoCode]["description_short"]);
                    }
                    if ($traductions[$isoCode]["description"]) {
                        $resultsArray[37] = $resultsArray[37]."#==#tradizi_description_longue_";
                        $resultsArray[37] .= $isoCode.":".cleanForTraduction($traductions[$isoCode]["description"]);
                    }
                    if ($traductionsCaracteristiques[$isoCode]) {
                        $resultsArray[37].=$traductionsCaracteristiques[$isoCode];
                    }
                }

                fwrite($handle, implode(SEP, $resultsArray) . chr(10));
            }
            IzifluxTools::logDebug('utilsExport.php '.__LINE__.': UtilsExport: ===========================', 0);
        }

        // Print the content to the browser
        echo Tools::file_get_contents($file_export);
    }

    /**
     * Function getheaderArray()
     *
     * @return array headers
     */
    public static function getHeaderArray()
    {
        $arrayHeader = array(
            'id-produit',
            'code-ean',
            'denomination-concise',
            'denomination-subjective',
            'description-concise',
            'description-complète',
            'url-article',
            'urlphoto1',
            'url-photo2',
            'url-photo3',
            'url-photo4',
            'url-photo5',
            'marque',
            'genre',
            'classification',
            'id-reference',
            'disponibilite',
            'statut-dedisponibilite',
            'prix-ttc',
            'ecotaxe',
            'retractation',
            'quantite',
            'delai-de-reappro',
            'delai-de-livraison',
            'unite-delai-de-livraison',
            'delai-dexpedition',
            'unite-delai-d-expedition',
            'frais-de-port',
            'description-livraison',
            'prix-public-generalement-constate',
            'mots-clefs',
            'dateheure-de-debut-promotion',
            'date-heure-de-fin-promotion',
            'prix-ttc-avant-promotion',
            'promotion',
            'pourcentage-de-la-remise',
            'prixremise',
            'type-de-promotion',
            'garantie',
            'message-promotionnel',
            'refInterne',
            'refFAbricant',
            'poidsauto',
            'caracteristiques',
            'transporteurs'
        );
        return $arrayHeader;
    }

    /**
     * Function getProductAttributeExport
     *
     * @param array $product
     * @param string $ean13Declinaison
     * @param array $decli
     * @param string $classification
     * @param array $carrier
     * @param array $promo
     * @param int $poidsDeclinaison
     * @param array $featuresList2Decli
     * @param decimal $shippingPriceFinal
     * Exports the fields of a combination into an array
     */
    public static function getProductAttributeExport(
        $product,
        $ean13Declinaison,
        $decli,
        $classification,
        $carrier,
        $promo,
        $poidsDeclinaison,
        $featuresList2Decli,
        $shippingPriceFinal,
        $id_zone
    ) {
        $result = array(
            $product['id_product'],
            $ean13Declinaison,
            $product['name'],
            $product['name'] . ' ' . $decli['name_subjective'],
            IzifluxTools::htmlEscape($product['description_short']),
            IzifluxTools::htmlEscape($product['description']),
            $decli['link'],
            $decli['images'] . Manufacturer::getNameById($product['id_manufacturer']),
            '',
            $classification,
            $product['id_product'] . '#IZI#' . $decli['id_product_attribute'],
            $decli['disponibilite'],
            $decli['statut_disponibilite'],
            $decli['price'],
            round($product['ecotax'], 2),
            '',
            Product::getQuantity($product['id_product'], $decli['id_product_attribute']),
            ($decli['disponibilite'] == 'Y' ? (is_numeric($product['available_later']) ?
                $product['available_later'] : Configuration::get('IZIFLUX_DELAY_REPLENISHING')) : 0),
            (is_numeric($product['available_now']) ?
                $product['available_now'] : Configuration::get('IZIFLUX_DELAY_DELIVERY')),
            Configuration::get('IZIFLUX_DELAY_UNIT'),
            '',
            '',
            IzifluxTools::getShippingPrice(
                $carrier,
                $poidsDeclinaison,
                $decli['price'],
                (isset($product['additional_shipping_cost']) ? $product['additional_shipping_cost'] : 0),
                $id_zone
            ),
            $carrier->name,
            $decli['price'],
            $product['meta_keywords'],
            $promo['from'],
            $promo['to'],
            $decli['price_without_reduction'],
            $promo['amount'],
            $promo['percent'],
            $decli['price'],
            $promo['type'] . '|0|',
            $decli['reference'],
            IzifluxTools::getProductSupplierReference($product['id_product'], $decli['id_product_attribute']),
            $poidsDeclinaison,
            $featuresList2Decli,
            $shippingPriceFinal
        );

        return $result;
    }

    /**
     * Function getProductExport
     *
     * @param array $product
     * @param array $links_image
     * @param string $classification
     * @param string $dispo
     * @param int $statut_dispo
     * @param array $carrier
     * @param array $promo
     * @param array $featuresList2
     * @param decimal $shippingPriceFinal
     * @param array $links_image_mkp
     * @param int $id_zone
     *Exports the fields of a product into an array
     *
     */
    public static function getProductExport(
        $product,
        $links_image,
        $classification,
        $dispo,
        $statut_dispo,
        $carrier,
        $promo,
        $featuresList2,
        $shippingPriceFinal,
        $id_zone
    ) {
        $shipping_cost = $product['additional_shipping_cost'];
        $product['price_without_reduction'] =
            Product::getPriceStatic($product['id_product'], true, null, 6, null, false, false);
        $result = array(
            $product['id_product'],
            $product['ean13'],
            $product['name'],
            $product['name'],
            IzifluxTools::htmlEscape($product['description_short']),
            IzifluxTools::htmlEscape($product['description']),
            $product['link'],
            $links_image . Manufacturer::getNameById($product['id_manufacturer']),
            '',
            $classification,
            $product['id_product'],
            $dispo,
            $statut_dispo,
            Product::getPriceStatic($product['id_product'], true),
            round($product['ecotax'], 2),
            '',
            Product::getQuantity($product['id_product'], null),
            ($dispo == 'Y' ? (is_numeric($product['available_later']) ?
                    $product['available_later'] : Configuration::get('IZIFLUX_DELAY_REPLENISHING')) : 0),
            (is_numeric($product['available_now']) ? $product['available_now'] :
                Configuration::get('IZIFLUX_DELAY_DELIVERY')),
            Configuration::get('IZIFLUX_DELAY_UNIT'),
            '',
            '',
            IzifluxTools::getShippingPrice(
                $carrier,
                $product['weight'],
                $product['price'],
                (isset($shipping_cost) ? $shipping_cost : 0),
                $id_zone
            ),
            $carrier->name,
            Product::getPriceStatic($product['id_product'], true),
            $product['meta_keywords'],
            $promo['from'],
            $promo['to'],
            $product['price_without_reduction'],
            $promo['amount'],
            $promo['percent'],
            Product::getPriceStatic($product['id_product'], true),
            $promo['type'] . '|0|',
            $product['reference'],
            IzifluxTools::getProductSupplierReference($product['id_product']),
            $product['weight'],
            $featuresList2,
            $shippingPriceFinal
        );

        return $result;
    }


    public static function getProductInfoAndDecrement($fileReference, $fileStock, $isDeclinaison, $decrement = false)
    {
        $productsInfoTable = array();

        // Table Product potential columns
        $productReferenceTableColumn = array(
            'reference',
            'supplier_reference',
            'ean13',
            'upc',
            'id_product',
            'id_product_attribute'
        );

        if (Configuration::get('IZIFLUX_ACCOUNT') == 'expertbynet'
            || Configuration::get('IZIFLUX_ACCOUNT') == 'expertbynetnew') {
            $productReferenceTableColumn = array(
                'id_product',
                'id_product_attribute'
            );
        }

        if (method_exists('SpecificCustomers', Configuration::get('IZIFLUX_ACCOUNT') . '_getProductInfoAndDecrement')) {
            $result = call_user_func(array(
                'SpecificCustomers',
                Configuration::get('IZIFLUX_ACCOUNT') . '_getProductInfoAndDecrement'
            ), array());

            $productReferenceTableColumn = array(
                $result['0'],
                $result['1']
            );
        }

        if (method_exists(
            'SpecificCustomers',
            Configuration::get('IZIFLUX_ACCOUNT') . '_getProductInfoAndDecrement2'
        )) {
            $result = call_user_func(array(
                'SpecificCustomers',
                Configuration::get('IZIFLUX_ACCOUNT') . '_getProductInfoAndDecrement2'
            ), array());

            $productReferenceTableColumn = array(
                $result['0'],
                $result['1'],
                $result['2'],
                $result['3'],
                $result['4']
            );
        }

        if (method_exists(
            'SpecificCustomers',
            Configuration::get('IZIFLUX_ACCOUNT') . '_getProductInfoAndDecrement3'
        )) {
            $result = call_user_func(array(
                'SpecificCustomers',
                Configuration::get('IZIFLUX_ACCOUNT') . '_getProductInfoAndDecrement3'
            ), array());

            $productReferenceTableColumn = array(
                $result['0'],
                $result['1'],
                $result['2'],
                $result['3']
            );
        }

        if (method_exists(
            'SpecificCustomers',
            Configuration::get('IZIFLUX_ACCOUNT') . '_getProductInfoAndDecrement4'
        )) {
            $result = call_user_func(array(
                'SpecificCustomers',
                Configuration::get('IZIFLUX_ACCOUNT') . '_getProductInfoAndDecrement4'
            ), array(
                IzifluxTools::getIdProductIdProductAttribute($fileReference),
                $fileReference
            ));

            $productReferenceTableColumn = array(
                $result['0']
            );

            $fileReference = $result['1'];
            $isDeclinaison = $result['2'];
        }

        if ($isDeclinaison == false) {
            // If boolean $isDeclinaison is false, do normal search
            // Else look into table product_attribute having column name id_product_attribute

            // Look for column in product table
            foreach ($productReferenceTableColumn as $productReferenceColumn) {
                IzifluxTools::logDebug(
                    'utilsExport.php '.__LINE__.
                    ": getProductInfoAndDecrement / potential column tested : $productReferenceColumn.
                    request: " . 'SELECT * FROM `' . _DB_PREFIX_ . 'product` WHERE `' .
                    $productReferenceColumn . '` = "' . $fileReference . '";',
                    0
                );

                // If file reference exists in product column
                if ($productReferenceColumn != "id_product_attribute" && $retrieveProductResult =
                        Db::getInstance()->executeS('SELECT * FROM `' . _DB_PREFIX_ . 'product`
                            WHERE `' . $productReferenceColumn . '` = "' . $fileReference . '";')) {
                    $msg = "getProductInfoAndDecrement / if DollarColonne_potentielle_reference_product";
                    $msg .= "!= id_product_attribute / potential column:";
                    IzifluxTools::logDebug(
                        'utilsExport.php '.__LINE__.": ".$msg . $productReferenceColumn . " request: " .
                        'SELECT * FROM `' . _DB_PREFIX_ . 'product` WHERE `' .
                        $productReferenceColumn . '` = "' . $fileReference . '";',
                        0
                    );

                    if (isset($retrieveProductResult[1])) {
                        $msg = "Error looking for product and decrementing stock. The product ";
                        IzifluxTools::logDebug(
                            'utilsExport.php '.__LINE__.": ".$msg . $fileReference .
                            " exists multitple times in table '" .
                            _DB_PREFIX_ . "product' column '$productReferenceColumn'",
                            0
                        );

                        IzifluxTools::testOutput($retrieveProductResult);
                        IzifluxTools::testOutput('Error searching for product and decrementing the stock');
                        IzifluxTools::testOutput('The product "'.$fileReference.'" exists several times');
                        IzifluxTools::testOutput('in the table `product` column '.$productReferenceColumn);

                        break;
                    }

                    $productsInfoTable = $retrieveProductResult[0];

                    // Decrement in product stock quantity
                    // Deduct stock in stock_available table
                    if ($decrement) {
                        $decrementProductQuery = 'UPDATE `' . _DB_PREFIX_ . 'stock_available`
                            SET `quantity` = (`quantity` - "' . $fileStock . '")
                             WHERE `id_product` = "' . $retrieveProductResult[0]['id_product'] . '"
                                 and `id_product_attribute`="0";';

                        if (REGISTERDB) {
                            Db::getInstance()->execute($decrementProductQuery);
                        }

                        $msg = "getProductInfoAndDecrement / if DollarColonne_potentielle_reference_product";
                        $msg .= "!= id_product_attribute / potential column tested: ";
                        IzifluxTools::logDebug(
                            'utilsExport.php '.__LINE__.": ".$msg . $productReferenceColumn .
                            " request: $decrementProductQuery",
                            0
                        );
                        IzifluxTools::logDebug(
                            'utilsExport.php '.__LINE__.
                            ": Request: $decrementProductQuery",
                            0
                        );
                    }
                    break;
                }

                if ($productAttributeResult =
                    Db::getInstance()->executeS('SELECT * FROM `' . _DB_PREFIX_ . 'product_attribute`
                        WHERE `' . $productReferenceColumn . '` = "' . pSQL($fileReference) . '";')) {
                    // If file reference is present in product_attribute column
                    $msg = "getProductInfoAndDecrement / out of DollarColonne_potentielle_reference_product ";
                    $msg .= "!= id_product_attribute / potential column tested : ";
                    IzifluxTools::logDebug(
                        'utilsExport.php '.__LINE__.": ".$msg . $productReferenceColumn . " request: " .
                        'SELECT * FROM `' . _DB_PREFIX_ . 'product_attribute` WHERE `' .
                        $productReferenceColumn . '` = "' . pSQL($fileReference) . '";',
                        0
                    );

                    // If more than 1 result, table contains duplicates
                    if (isset($productAttributeResult[1])) {
                        $msg = "Error during search of product and stock decrement. The product ";
                        IzifluxTools::logDebug(
                            'utilsExport.php '.__LINE__.": ".$msg .
                            fileReference . " exists multiple times in the table '" .
                            _DB_PREFIX_ . "product_attribute' column '$productReferenceColumn'",
                            0
                        );
                        IzifluxTools::testOutput($productAttributeResult);
                        IzifluxTools::testOutput('Error searching for product and decrementing the stock');
                        IzifluxTools::testOutput('The product "'.$fileReference.'" exists several times');
                        IzifluxTools::testOutput('in the table `product` column '.$productReferenceColumn);
                        return $productAttributeResult[0];
                    }

                    // return product table
                    $productsInfoTable = $productAttributeResult[0];

                    // For enviedemeubles
                    if (Configuration::get('IZIFLUX_ACCOUNT') == 'enviedemeubles') {
                        if (! (preg_match("/id_product_attribute/", $productReferenceColumn))) {
                            $fileReference = $productAttributeResult[0]['id_product_attribute'];
                            $productReferenceColumn = "id_product_attribute";
                        }
                    }

                    if ($decrement) {
                        // Deduct stock in product_attibute table
                        $decrementProductQuery =
                            'UPDATE `' . _DB_PREFIX_ . 'product`
                                SET `quantity` = (`quantity` - "' . $fileStock . '")
                                    WHERE `id_product` = "' . $productAttributeResult[0]['id_product'] . '";';

                        // Deduct stock in stock_available table

                        $productAttributeDecrementQuery =
                            'UPDATE `' . _DB_PREFIX_ . 'stock_available` SET
                                `quantity` = (`quantity` - "' . $fileStock . '")
                                    WHERE `id_product` = "'
                                    . $productAttributeResult[0]['id_product'] . '"
                                        AND `' . $productReferenceColumn . '` = "' . pSQL($fileReference) . '";';
                        if (REGISTERDB) {
                            Db::getInstance()->execute($productAttributeDecrementQuery);
                        }
                    }

                    $msg = "getProductInfoAndDecrement / out of if DollarColonne_potentielle_reference_product";
                    $msg .= "!= id_product_attribute / Potential column test: ";
                    $msg2 = " Request query_decrement_product_attribute:";
                    IzifluxTools::logDebug(
                        'utilsExport.php '.__LINE__.": ".$msg .
                        $productReferenceColumn . $msg2 . $productAttributeDecrementQuery
                        . "//  Request query_decrement_product: $decrementProductQuery",
                        0
                    );
                    IzifluxTools::logDebug('utilsExport.php '.__LINE__.": request: $decrementProductQuery", 0);
                    break;
                }
            }
        } else {
            // If boolean $isDeclinaison is true; look into product_attribute table having column name
            // id_product_attribute
            $productAttributeResult =
                Db::getInstance()->executeS('SELECT * FROM `' . _DB_PREFIX_ . 'product_attribute`
                    WHERE `id_product_attribute` = "' . pSQL($fileReference) . '";');

            $msg = "getProductInfoAndDecrement / out of if DollarColonne_potentielle_reference_product";
            $msg .= "!= id_product_attribute / Potential column tested: id_product_attribute. Request: ";
            IzifluxTools::logDebug(
                'utilsExport.php '.__LINE__.": ".$msg . 'SELECT * FROM `'
                . _DB_PREFIX_ . 'product_attribute` WHERE `id_product_attribute` = "'
                . $fileReference . '";',
                0
            );

            // If more than 1 result, then table is not clean and contains identifiers
            if (isset($productAttributeResult[1])) {
                $msg = "Error looking for product and decrementing stock. The product ";
                IzifluxTools::logDebug(
                    'utilsExport.php '.__LINE__.": ".$msg . $fileReference .
                    " exists multitple times in table '" . _DB_PREFIX_ .
                    "product_attribute' column 'id_product_attribute'",
                    0
                );
                IzifluxTools::testOutput($productAttributeResult);
                IzifluxTools::testOutput('Error searching for product and decrementing the stock');
                IzifluxTools::testOutput('The product "'.$fileReference.'" exists several times');
                IzifluxTools::testOutput('in the table `product` column `id_product_attribute`');

                return false;
            }

            // Determing table to return
            $productsInfoTable = $productAttributeResult[0];

            if ($decrement) {
                // Decrement stock in product_attribute
                $decrementProductQuery = 'UPDATE `' . _DB_PREFIX_ . 'product`
                    SET `quantity` = (`quantity` - "' . $fileStock . '")
                    WHERE `id_product` = "' . $productAttributeResult[0]['id_product'] . '";';

                // Deduct stock in stock_available table
                $productAttributeDecrementQuery =
                    'UPDATE `' . _DB_PREFIX_ . 'stock_available` SET `quantity` =
                            (`quantity` - "' . $fileStock . '") WHERE `id_product` =
                                    "' . $productAttributeResult[0]['id_product'] . '"
                                        AND `id_product_attribute` = "' . $fileReference . '";';

                if ((Configuration::get('IZIFLUX_ACCOUNT') == 'phonearea'
                        && (! (isset($productAttributeResult[0]['id_product']))))) {
                    $productAttributeDecrementQuery
                        = 'UPDATE `' . _DB_PREFIX_ .
                        'stock_available` SET `quantity` =
                                (`quantity` - "' . (int)$fileStock . '") WHERE `id_product` = "'
                                    . $productAttributeResult[0]['id_product'] . '";';
                }
                if (REGISTERDB) {
                    Db::getInstance()->execute($productAttributeDecrementQuery);
                }
            }

            $msg = "getProductInfoAndDecrement / out of if DollarColonne_potentielle_reference_product ";
            $msg .= "!= id_product_attribute /Potential column tested: id_product_attribute.";
            $msg .= "Request query_decrement_product_attribute: ";
            IzifluxTools::logDebug(
                'utilsExport.php '.__LINE__.": ".$msg . $productAttributeDecrementQuery .
                " //  Request query_decrement_product: $decrementProductQuery",
                0
            );
            IzifluxTools::logDebug(
                'utilsExport.php '.__LINE__.": request: ".$decrementProductQuery,
                0
            );
        }
        return $productsInfoTable;
    }

    /**
     * Function to create new address
     *
     * @param array $tableau_infos
     * @param string $type_adresse
     * @param int $customer_id
     */
    public static function createAddress($tableau_infos, $type_adresse, $customer_id)
    {
        // Initialise fields
        $new_address = new Address();
        $new_address->id_customer = $customer_id;
        $alias = Tools::substr(($type_adresse . trim($tableau_infos[7])), 0, 32);
        $new_address->alias = ($alias == "") ? "." : $alias;
        $new_address->id_state = '';

        if ($type_adresse == 'i-') {
            $company= mb_substr(
                IzifluxTools::clean(
                    trim(
                        $tableau_infos[10]
                    ),
                    'address'
                ),
                0,
                32
            );
            if ($company == "") {
                $company=".";
            }
            $new_address->company = $company;
            $new_address->lastname = (
                mb_substr(
                    IzifluxTools::clean(
                        trim(
                            $tableau_infos[8]
                        ),
                        'lastname'
                    ),
                    0,
                    32
                )
            );

            $new_address->firstname = (
                mb_substr(
                    IzifluxTools::clean(
                        trim(
                            $tableau_infos[9]
                        ),
                        'firstname'
                    ),
                    0,
                    32
                )
            );

            $address = (mb_substr(IzifluxTools::clean(trim($tableau_infos[11]), 'address'), 0, 128));

            $address2 = (mb_substr(IzifluxTools::clean(trim($tableau_infos[12]), 'address'), 0, 128));

            if ($address=="" && $address2=="") {
                $address = (mb_substr(IzifluxTools::clean(trim($tableau_infos[22]), 'address'), 0, 128));
                $address2 = (mb_substr(IzifluxTools::clean(trim($tableau_infos[23]), 'address'), 0, 128));
            }

            if ($address=="") {
                $address=".";
            }
            if ($address2=="") {
                $address2=".";
            }


            $new_address->address1 = ($address == "") ? $address2 : $address;
            $new_address->address2 = ($address == "") ? "" : $address2;

            $new_address->postcode = IzifluxTools::clean(trim($tableau_infos[13]), 'postcode');
            $new_address->city = IzifluxTools::clean(trim($tableau_infos[14]), 'city');

            $phone = mb_substr(IzifluxTools::clean(trim($tableau_infos[17]), 'phone'), 0, 16);
            $portable = mb_substr(IzifluxTools::clean(trim($tableau_infos[28]), 'phone'), 0, 16);
            if ($phone == "") {
                $phone = $portable;
            }
            if ($portable != "") {
                $phone = $portable;
            }
            if ($portable == "") {
                $portable="0123456789";
            }
            if ($phone == "") {
                $phone="0123456789";
            }

            $new_address->phone = $phone;
            $new_address->phone_mobile = $portable;

            $new_address->date_add = date('Y-m-d H:i:s');
            $new_address->date_upd = date('Y-m-d H:i:s');

            $country = trim($tableau_infos[16]);

            //Check if $country is iso format
            if (Tools::strlen($country) > 3) {
                $country = (int) Country::getIdByName(null, $country);
            } else {
                if ($country == '-' || $country == ' -' || $country == '- ' || $country == ' - ') {
                    $country = (int) Country::getIdByName(null, Configuration::get('PS_COUNTRY_DEFAULT'));
                } else {
                    $country = (int) Country::getByIso($country);
                }
            }

            // If id country not available, use prestashop default country
            if (!$country) {
                $country = (int) Configuration::get('PS_COUNTRY_DEFAULT');
            }

            $new_address->id_country = $country;
        } else {
            $company= mb_substr(
                IzifluxTools::clean(
                    trim(
                        $tableau_infos[21]
                    ),
                    'address'
                ),
                0,
                32
            );
            if ($company == "") {
                $company=".";
            }

            $new_address->company = $company;

            $new_address->lastname = (
                mb_substr(
                    IzifluxTools::clean(
                        trim(
                            $tableau_infos[19]
                        ),
                        'lastname'
                    ),
                    0,
                    32
                )
            );

            $new_address->firstname = (
                mb_substr(
                    IzifluxTools::clean(
                        trim(
                            $tableau_infos[20]
                        ),
                        'firstname'
                    ),
                    0,
                    31
                )
            );

            $address = (mb_substr(IzifluxTools::clean(trim($tableau_infos[22]), 'address'), 0, 128));
            $address2 = (mb_substr(IzifluxTools::clean(trim($tableau_infos[23]), 'address'), 0, 128));

            if ($address=="" && $address2=="") {
                $address = (mb_substr(IzifluxTools::clean(trim($tableau_infos[11]), 'address'), 0, 128));
                $address2 = (mb_substr(IzifluxTools::clean(trim($tableau_infos[12]), 'address'), 0, 128));
            }

            if ($address=="") {
                $address=".";
            }
            if ($address2=="") {
                $address2=".";
            }

            $new_address->address1 = ($address == "") ? $address2 : $address;
            $new_address->address2 = ($address == "") ? "" : $address2;

            $new_address->postcode = IzifluxTools::clean(trim($tableau_infos[24]), 'postcode');
            $new_address->city = IzifluxTools::clean(trim($tableau_infos[25]), 'city');

            $phone = mb_substr(IzifluxTools::clean(trim($tableau_infos[28]), 'phone'), 0, 16);
            $portable = mb_substr(IzifluxTools::clean(trim($tableau_infos[17]), 'phone'), 0, 16);
            if ($phone == "") {
                $phone = $portable;
            }
            if ($portable != "") {
                $phone = $portable;
            }
            if ($portable == "") {
                $portable="0123456789";
            }
            if ($phone == "") {
                $phone="0123456789";
            }

            $new_address->phone = $phone;
            $new_address->phone_mobile = $portable;

            $new_address->date_add = date('Y-m-d H:i:s');
            $new_address->date_upd = date('Y-m-d H:i:s');


            $country = trim($tableau_infos[27]);
            //Check if $country is iso format
            if (Tools::strlen($country) > 3) {
                $country = (int) Country::getIdByName(null, $country);
            } else {
                if ($country == '-' || $country == ' -' || $country == '- ' || $country == ' - ') {
                    $country = (int) Country::getIdByName(null, Configuration::get('PS_COUNTRY_DEFAULT'));
                } else {
                    $country = (int) Country::getByIso($country);
                }
            }

            // If id country not available, use prestashop default country
            if (!$country) {
                $country = (int) Configuration::get('PS_COUNTRY_DEFAULT');
            }
            $new_address->id_country = $country;
        }

        $GLOBALS['id_country'] = $country;
        $new_address->save();
        return $new_address->id;
    }

    /**
     * Function to add a new order
     *
     * @param array $commande
     * @param int $id_currency
     * @param int $id_carrier
     * @param int $id_lang
     * @param array $customer
     * @param int $shipping_address_id
     * @param int $invoice_address_id
     * @param string $paymentMethod
     * @param date $date_commande
     * @param int $poidsTotalDeLacomande
     */
    public static function addOrder(
        $commande,
        $id_currency,
        $id_carrier,
        $id_lang,
        $customer,
        $shipping_address_id,
        $invoice_address_id,
        $paymentMethod,
        $date_commande,
        $poidsTotalDeLacomande
    ) {

        // START Calculate prices
        $total_product_tax = $total_paid = $total_paid_tax_incl =
        $total_paid_tax_excl = $total_paid_real = $total_products =
        $total_products_wt = $total_shipping = $total_shipping_tax_incl =
        $total_shipping_tax_excl = 0;
        foreach ($commande as $product) {
            if ($product[34] <= "0" && $product[36] <= "0") {
                // if tax not avaialble, prices are vat incl.
                // Recaulculate prices vat excl., using tax rate

                // explode $product[1] by # to retrieve id_product
                $pieces = explode('#', $product[1]);
                $product_id = '';

                if (count($pieces) > 1) {
                    $product_id = $pieces[0];
                } else {
                    $product_id = $product[1];
                }

                // retrieve productid
                $extractIdProduct = IzifluxTools::getIdProductIdProductAttribute($product_id);
                $extractIdProduct = $extractIdProduct["id_product"];
                $tax_rate = Tax::getProductTaxRate((int) ($extractIdProduct), $shipping_address_id);
                if (Configuration::get('IZIFLUX_ACCOUNT') == 'enviedemeubles') {
                    $tax_rate = "20";
                }

                // VAT amount on product price
                $product[34] = $product[33] - ($product[33] / (1 + ($tax_rate / 100)));
                if ($product[32] != 0) {
                    $product[34] = $product[34] * $product[32];
                }

                // VAT amount for shipping cost
                $product[36] = $product[35] - ($product[35] / (1 + ($tax_rate / 100)));

                // Product Price Vat Inc
                $prix_produit_wt = $product[33];

                if ($product[32] != 0) {
                    $prix_produit_wt = $prix_produit_wt * $product[32];
                }

                // Product Price vat excl.
                $product[33] = $product[33] / (1 + ($tax_rate / 100));

                // Shipping cost vat excl.
                $product[35] = $product[35] / (1 + ($tax_rate / 100));

                $prix_produit = $product[33];
                if ($product[32] != 0) {
                    $prix_produit = $prix_produit * $product[32];
                }

                $total_paid_tax_incl +=
                (float) (Tools::ps_round((float) ($prix_produit + $product[34] + $product[35] + $product[36]), 2));
                $total_paid_tax_excl +=
                    (float) (Tools::ps_round((float) ($prix_produit + $product[35]), 2));
            } else {
                $prix_produit = $product[33];

                // Product Price Vat Inc
                $prix_produit_wt = $product[33];

                if ($product[32] != 0) {
                    $prix_produit_wt = $prix_produit_wt * $product[32];
                }

                if ($product[32] != 0) {
                    $prix_produit = $prix_produit * $product[32];
                }

                $total_paid_tax_incl +=
                    (float) (Tools::ps_round((float) ($prix_produit + $product[34] + $product[35] + $product[36]), 2));
                $total_paid_tax_excl += (float) (Tools::ps_round((float) ($prix_produit + $product[35]), 2));
            }

            $total_products += (float) (Tools::ps_round((float) ($prix_produit), 2));
            $total_products_wt += (float) (Tools::ps_round((float) ($prix_produit_wt), 2));
            $total_shipping += (float) (Tools::ps_round((float) ($product[35]), 2));
            $total_shipping_tax_incl += (float) (Tools::ps_round((float) ($product[35] + $product[36]), 2));
            $total_shipping_tax_excl += (float) (Tools::ps_round((float) ($product[35]), 2));

            $total_paid_real = $total_paid_tax_incl;
            if (Configuration::get('IZIFLUX_ACCOUNT') == '1kmapieds') {
                $total_paid_real = "0.00";
            }
            if (version_compare(Configuration::get('PS_VERSION_DB'), '1.5.1', '>')
                || version_compare(Configuration::get('PS_INSTALL_VERSION'), '1.5.1', '>')) {
                $total_paid_real = "0.00";
            }

            $total_paid = $total_paid_tax_incl;

            // Rounding to 2dp
            $total_paid = round($total_paid, 2);
            $total_paid_tax_incl = round($total_paid_tax_incl, 2);
            $total_paid_tax_excl = round($total_paid_tax_excl, 2);
            if (Configuration::get('IZIFLUX_ACCOUNT') == '1kmapieds') {
                $total_paid_real = "0.00";
            }
            if (version_compare(Configuration::get('PS_VERSION_DB'), '1.5.1', '>')
                || version_compare(Configuration::get('PS_INSTALL_VERSION'), '1.5.1', '>')) {
                $total_paid_real = "0.00";
            }
            $total_paid_real = round($total_paid_real, 2);

            $total_products = round($total_products, 2);
            $total_products_wt = round($total_products_wt, 2);
            $total_shipping = round($total_shipping, 2);
            $total_shipping_tax_incl = round($total_shipping_tax_incl, 2);

            // For toutacoo
            $total_shipping = $total_shipping_tax_incl;

            $total_shipping_tax_excl = round($total_shipping_tax_excl, 2);
            $total_product_tax += (float) (Tools::ps_round((float) ($product[34]), 2));
        }

        // Verify total prices and reassign values to blank fields
        if ($total_paid_tax_incl <= "0") {
            $total_paid_tax_incl = $total_paid;
        }
        if ($total_paid_tax_excl <= "0") {
            if ($tax_rate > "0") {
                $total_paid_tax_excl = (float) (Tools::ps_round((float) ($total_paid / (1 + ($tax_rate / 100)))));
            } else {
                $total_paid_tax_excl = $total_paid;
            }
        }
        if ($total_shipping_tax_incl <= "0") {
            $total_shipping_tax_incl = $total_shipping;
        }
        if ($total_shipping_tax_excl <= "0") {
            if ($tax_rate > "0") {
                $total_shipping_tax_excl =
                (float) (Tools::ps_round((float) ($total_shipping / (1 + ($tax_rate / 100)))));
            } else {
                $total_shipping_tax_excl = $total_shipping;
            }
        }
        $toLog = date("Y-m-d H:i:s", strtotime($date_commande)) . "::##::" . $date_commande . ':: date_commande';
        IzifluxTools::testOutput($toLog);

        $id_cart = 0;
        IzifluxTools::testOutput('utilsExport::addOrder BEFORE calling manageCreateCartForOrder()');
        $id_cart = IzifluxTools::manageCreateCartForOrder(
            $customer->id,
            $invoice_address_id,
            $shipping_address_id,
            $id_currency,
            $id_carrier
        );
        IzifluxTools::testOutput('utilsExport::addOrder AFTER calling manageCreateCartForOrder()');

        // Initialise fields for orders
        $currency = new Currency($id_currency);
        $order = new Order();
        $order->reference = Tools::substr($commande[0][0], - 9);
        if ((Configuration::get('IZIFLUX_ACCOUNT') == 'clubcase')) {
            $order->id_shop_group = 2;
        } else {
            $order->id_shop_group = 1;
        }
        if (Configuration::get('IZIFLUX_ACCOUNT') == 'maquillageetcosmetique') {
            $order->id_shop = 1;
        } else {
            $order->id_shop = 1;
        }
        $order->id_carrier = (int) ($id_carrier);
        $order->id_lang = (int) ($id_lang);
        $order->id_customer = (int) ($customer->id);
        $order->id_cart = $id_cart;
        $order->id_currency = (int) ($id_currency);
        $order->id_address_delivery = (int) ($shipping_address_id);
        $order->id_address_invoice = (int) ($invoice_address_id);
        if (preg_match("/FBA/", $product[6])) {
            // If FBA Amazon, change order status to Delivered
            $order->current_state = 5;
        } else {
            if (Configuration::get('IZIFLUX_ACCOUNT') == 'babyzone') {
                $order->current_state = 14;
            } else {
                $order->current_state = 2;
            }
        }
        $order->secure_key = pSQL($customer->secure_key);
        $order->payment = $paymentMethod;
        $order->conversion_rate = $currency->conversion_rate;
        $order->module = "iziflux";
        $order->recyclable = 0;

        $message = false;
        if ($product[30]) {
            $message = "Mode de livraison : " . $product[30];
        }
        if ((Configuration::get('IZIFLUX_ACCOUNT') == 'monsieurcyberman')
            || (Configuration::get('IZIFLUX_ACCOUNT') == 'laboutiquesport')) {
            if ($product[37]) {
                $message .= " // Commission Market Place : " . $product[37];
            }
        }
        if (Configuration::get('IZIFLUX_ACCOUNT') == 'millumine') {
            $message = false;
        }
        if ($message) {
            $order->gift = 1;
            $order->gift_message = $message;
        } else {
            $order->gift = 0;
            $order->gift_message = 0;
        }

        $order->total_discounts = 0;

        $order->total_paid = $total_paid;
        $order->total_paid_tax_incl = $total_paid_tax_incl;
        $order->total_paid_tax_excl = $total_paid_tax_excl;

        if (version_compare(Configuration::get('PS_VERSION_DB'), '1.5.1', '>')
            || version_compare(Configuration::get('PS_INSTALL_VERSION'), '1.5.1', '>')) {
            $order->total_paid_real = "0.00";
        } else {
            if (Configuration::get('IZIFLUX_ACCOUNT') == '1kmapieds') {
                $order->total_paid_real = "0.00";
            } else {
                $order->total_paid_real = $total_paid;
            }
        }
        $order->total_products = round(($total_paid - $total_shipping_tax_incl - $total_product_tax), 2);
        $order->total_products_wt = $total_products_wt;
        $order->total_shipping = $total_shipping;
        $order->total_shipping_tax_incl = $total_shipping_tax_incl;
        $order->total_shipping_tax_excl = $total_shipping_tax_excl;
        $order->carrier_tax_rate = (float) Tax::getCarrierTaxRate($id_carrier, $shipping_address_id);
        $order->total_wrapping = 0;
        $order->invoice_date = date("Y-m-d H:i:s", strtotime($date_commande));
        $order->delivery_date = '0000-00-00 00:00:00';

        if ((Configuration::get('IZIFLUX_ACCOUNT') == 'clubcase')) {
            $order->valid = 1;
        }
        if ((Configuration::get('IZIFLUX_ACCOUNT') == 'babyzone')) {
            $order->valid = 1;
        }

        IzifluxTools::logDebug("addOrder / before order add()", 2);
        foreach ($order as $keyHistorique => $valueHistorique) {
            IzifluxTools::logDebug('utilsExport.php '.__LINE__.": order: $keyHistorique => $valueHistorique", 2);
        }

        IzifluxTools::testOutput('utilsExport::addOrder BEFORE add()');
        if (REGISTERDB == 'ON') {
            $order->add();
        }
        IzifluxTools::testOutput('utilsExport::addOrder AFTER add()', $order);

        $query = 'INSERT INTO `' . _DB_PREFIX_ . 'order_carrier`
            VALUES ("", "' . (int) $order->id . '", "' . (int) $id_carrier . '", "' . pSQL($order->invoice_number)
            . '", "'  . pSQL($poidsTotalDeLacomande) . '", "' . (float) $total_shipping_tax_excl . '", "'
                    . (float) total_shipping_tax_incl . '", "", "'
                        . date("Y-m-d H:i:s", strtotime($date_commande)) . '");';
        if (REGISTERDB == 'ON') {
            Db::getInstance()->execute($query);
        }
        IzifluxTools::testOutput($query . ' :: query2');
        IzifluxTools::logDebug('utilsExport.php '.__LINE__.": addOrder / order_carrier" . $query, 2);

        // Return orderid for new order created
        return $order->id;
    }

    /**
     * Function to add order details
     *
     * @param int $id_order
     * @param array $product
     * @param array $tableau_infos_produit
     * @param string $nom_article
     * @param int $cart_quantity
     * @param int $quantityInStock
     * @param int $shipping_address_id
     * @param array $detailsTax
     */
    public static function addOrderDetails(
        $id_order,
        $product,
        $tableau_infos_produit,
        $nom_article,
        $cart_quantity,
        $quantityInStock,
        $detailsTax
    ) {
        // Block calculate total price, shipping price
        $total_price_tax_incl = $total_price_tax_excl =
            $unit_price_tax_incl = $unit_price_tax_excl = $total_shipping_price_tax_incl =
            $total_shipping_price_tax_excl = '';
        $unit_price_tax_incl = (float) (Tools::ps_round((float) (($product[33]) + ($product[34])), 2));
        $unit_price_tax_excl = (float) (Tools::ps_round((float) ($product[33]), 2));
        if ($product[36] == 0) {
            $total_shipping_price_tax_incl = 0;
        } else {
            $total_shipping_price_tax_incl = (float) (Tools::ps_round((float) ($product[35] / $product[36]), 2));
        }
        $total_shipping_price_tax_excl = (float) (Tools::ps_round((float) ($product[35]), 2));

        if (preg_match("/cdiscount/", $product[31])) {
            $unit_price_tax_incl = (float) (Tools::ps_round((float) (($product[33]) + ($product[34])), 2));
            $unit_price_tax_excl = (float) (Tools::ps_round((float) ($product[33]), 2));
        }
        $total_price_tax_incl = (float) (Tools::ps_round((float) (($product[33] + $product[34]) * $cart_quantity), 2));
        $total_price_tax_excl = (float) (Tools::ps_round((float) ($product[33] * $cart_quantity), 2));
        $total_price_tax_incl = round($total_price_tax_incl, 2);
        $total_price_tax_excl = round($total_price_tax_excl, 2);
        $unit_price_tax_incl = round($unit_price_tax_incl, 2);
        $unit_price_tax_excl = round($unit_price_tax_excl, 2);
        $total_shipping_price_tax_incl = round($total_shipping_price_tax_incl, 2);
        $total_shipping_price_tax_excl = round($total_shipping_price_tax_excl, 2);

        // Block calculate tax
        $tax_rate = $detailsTax['taxRate'];

        if (method_exists('SpecificCustomers', Configuration::get('IZIFLUX_ACCOUNT') . 'iziflux')) {
            $tax_rate = call_user_func(array(
                'SpecificCustomers',
                Configuration::get('IZIFLUX_ACCOUNT') . 'iziflux'
            ), array());
        }

        $deadline = '0000-00-00 00:00:00';
        $download_hash = null;
        if ($id_product_download = ProductDownload::getIdFromIdProduct((int) ($tableau_infos_produit['id_product']))) {
            $productDownload = new ProductDownload((int) ($id_product_download));
            $deadline = $productDownload->getDeadLine();
            $download_hash = $productDownload->getHash();
        }
        if ($tax_rate !== '') {
            $product[33] = round(($product[33] / (($tax_rate / 100) + 1)), 2);
            $total_price_tax_excl = round(($total_price_tax_excl / (($tax_rate / 100) + 1)), 2);
            $unit_price_tax_excl = round(($unit_price_tax_excl / (($tax_rate / 100) + 1)), 2);
        }

        // Verify total and reallocate in case field is empty
        if ($total_price_tax_excl <= "0") {
            if ($tax_rate > "0") {
                $total_price_tax_excl =
                (float) (Tools::ps_round((float) ($total_price_tax_incl / (1 + ($tax_rate / 100)))));
            } else {
                $total_price_tax_excl = $total_price_tax_incl;
            }
        }
        if ($unit_price_tax_incl <= "0") {
            $unit_price_tax_incl = (float) (Tools::ps_round((float) ($total_price_tax_incl / $cart_quantity)));
        }
        if ($unit_price_tax_excl <= "0") {
            if ($tax_rate > "0") {
                $unit_price_tax_excl = $unit_price_tax_incl / (1 + ($tax_rate / 100));
            } else {
                $unit_price_tax_excl = $unit_price_tax_incl;
            }
        }

        if ($total_shipping_price_tax_excl <= "0") {
            if ($tax_rate > "0") {
                $total_shipping_price_tax_excl =
                (float) (Tools::ps_round((float) ($product[35] / (1 + ($tax_rate / 100)))));
            } else {
                $total_shipping_price_tax_excl = $product[35];
            }
        }

        IzifluxTools::testOutput($id_order . '::taxrate');
        IzifluxTools::testOutput($tax_rate);
        IzifluxTools::testOutput($total_price_tax_incl);

        // Block tbl_order_detail
        $tbl_order_detail = array();
        $tbl_order_detail['id_order'] = (int) ($id_order);

        if (method_exists('SpecificCustomers', Configuration::get('IZIFLUX_ACCOUNT') . '_getShopId')) {
            $tbl_order_detail['id_shop'] = call_user_func(array(
                'SpecificCustomers',
                Configuration::get('IZIFLUX_ACCOUNT') . '_getShopId'
            ), array());
        }

        $tbl_order_detail['product_id'] = (int) ($tableau_infos_produit['id_product']);
        $tbl_order_detail['product_attribute_id'] =
        (isset($tableau_infos_produit['id_product_attribute']) ?
            (int) ($tableau_infos_produit['id_product_attribute']) : 'NULL');
        $tbl_order_detail['product_name'] = pSQL($nom_article);
        $tbl_order_detail['product_quantity'] = (int) ($cart_quantity);
        $tbl_order_detail['product_quantity_in_stock'] = $quantityInStock;
        $tbl_order_detail['product_price'] = (float) ($product[33]);
        $tbl_order_detail['reduction_percent'] = 0;
        $tbl_order_detail['reduction_amount'] = 0;
        $tbl_order_detail['group_reduction'] = 0;
        $tbl_order_detail['product_quantity_discount'] = 0;
        $tbl_order_detail['product_ean13']
            = (empty($tableau_infos_produit['ean13']) ? "" : pSQL($tableau_infos_produit['ean13']));
        $tbl_order_detail['product_upc']
            = (empty($tableau_infos_produit['upc']) ? "" : pSQL($tableau_infos_produit['upc']));
        $tbl_order_detail['product_reference']
            = (empty($tableau_infos_produit['reference']) ? "" : pSQL($tableau_infos_produit['reference']));
        $tbl_order_detail['product_supplier_reference']
            = (empty($tableau_infos_produit['supplier_reference']) ?
                "" : pSQL($tableau_infos_produit['supplier_reference']));
        $tbl_order_detail['product_weight'] = (float) ($tableau_infos_produit['weight']);
        $tbl_order_detail['tax_name'] = pSQL($detailsTax['taxName']);
        $tbl_order_detail['tax_rate'] = (float) ($tax_rate);
        $tbl_order_detail['ecotax'] = '0';
        $tbl_order_detail['ecotax_tax_rate'] = '0';

        if (Configuration::get('IZIFLUX_ACCOUNT') == 'enviedemeubles') {
            $tbl_order_detail['ecotax'] = (float) ($tableau_infos_produit['ecotax']);
            $tbl_order_detail['ecotax_tax_rate'] = $tax_rate;
        }

        $tbl_order_detail['discount_quantity_applied'] = '0';
        $tbl_order_detail['download_hash'] = pSQL($download_hash);
        $tbl_order_detail['download_deadline'] = pSQL($deadline);
        $tbl_order_detail['total_price_tax_incl'] = $total_price_tax_incl;
        $tbl_order_detail['total_price_tax_excl'] = $total_price_tax_excl;
        $tbl_order_detail['unit_price_tax_incl'] = $unit_price_tax_incl;
        $tbl_order_detail['unit_price_tax_excl'] = $unit_price_tax_excl;
        $tbl_order_detail['total_shipping_price_tax_incl'] = $total_shipping_price_tax_incl;
        $tbl_order_detail['total_shipping_price_tax_excl'] = $total_shipping_price_tax_excl;

        // Create request to register product details
        $values_orderDetail = '';
        $insert_orderDetail = '';
        foreach ($tbl_order_detail as $key_order_detail => $value_order_detail) {
            $values_orderDetail .= '`' . $key_order_detail . '`,';
            $insert_orderDetail .= '"' . $value_order_detail . '",';
        }

        // Register
        $query = 'INSERT INTO `' . _DB_PREFIX_ . 'order_detail` (' . Tools::substr($values_orderDetail, 0, - 1) . ')
        VALUES (' . Tools::substr($insert_orderDetail, 0, - 1) . ');';
        if (REGISTERDB == 'ON') {
            Db::getInstance()->execute($query);
        }
        IzifluxTools::testOutput($query . ' :: query');
        IzifluxTools::logDebug('utilsExport.php '.__LINE__.": addOrderDetails: " . $query, 2);

        $result_id_order_detail = $id_order_detail = "";
        $result_id_order_detail =
            Db::getInstance()->executeS(
                'SELECT max(`id_order_detail`) as miod
                FROM `' . _DB_PREFIX_ . 'order_detail` WHERE `id_order` = "' .
                $tbl_order_detail['id_order'] . '";'
            );
        IzifluxTools::logDebug(
            'utilsExport.php '.__LINE__.": order_detail_tax // select id_order_detail: " .
            'SELECT max(`id_order_detail`) as miod FROM `' . _DB_PREFIX_ . 'order_detail`
                WHERE `id_order` = "' . $tbl_order_detail['id_order'] . '";',
            2
        );
        $id_order_detail = $result_id_order_detail[0]["miod"];
        IzifluxTools::logDebug(
            'utilsExport.php '.__LINE__.
            ": order_detail_tax // id_order_detail: " . $id_order_detail,
            2
        );
        if (Configuration::get('IZIFLUX_ACCOUNT') == 'toutacoo') {
            $id_tax = "10";
        } elseif (Configuration::get('IZIFLUX_ACCOUNT') == 'monsieurcyberman') {
            $id_tax = "2";
        } else {
            if (empty($detailsTax['taxId'])) {
                $id_tax = 0;
            } else {
                $id_tax = $detailsTax['taxId'];
            }
        }

        IzifluxTools::logDebug('utilsExport.php '.__LINE__.": order_detail_tax // id_tax: " . $id_tax, 2);
        $unit_amount = abs((float) ($unit_price_tax_incl - $unit_price_tax_excl));
        IzifluxTools::logDebug('utilsExport.php '.__LINE__.": order_detail_tax // unit_amount: " . $unit_amount, 2);
        $total_amount = (float) ($unit_amount * $tbl_order_detail['product_quantity']);
        IzifluxTools::logDebug('utilsExport.php '.__LINE__.": order_detail_tax // total_amount: " . $total_amount, 2);
        $query = 'INSERT INTO `' . _DB_PREFIX_ . 'order_detail_tax`
            (`id_order_detail`,`id_tax`,`unit_amount`,`total_amount`)
            VALUES (' . $id_order_detail . ',' . $id_tax . ',' . $unit_amount . ',' . $total_amount . ');';
        IzifluxTools::logDebug('utilsExport.php '.__LINE__.": order_detail_tax // query: " . $query, 2);
        if (REGISTERDB == 'ON') {
            Db::getInstance()->execute($query);
        }
        IzifluxTools::testOutput($query . ' :: query2');
    }
}


/**
 * Function in charge to clean string to traduction
 */
function cleanForTraduction($string)
{
    $string = str_replace(chr(9), ' ', $string);
    $string = str_replace(chr(10), ' ', $string);
    $string = str_replace(chr(13), ' ', $string);
    $string = StrTr($string, "\x7C", "\x20");
    // Necessary for IziFlux communication
    $string = base64_encode($string);
    return $string;
}

/**
 * Function in charge to get all of the couple id_lang/iso_code active in shop
 */
function getIdLang()
{
    $tabLang = array();
    $queryLang = 'SELECT distinct(id_lang) FROM `'._DB_PREFIX_.'product_lang`
                    WHERE id_lang<>'.(int)Configuration::get('PS_LANG_DEFAULT');
    $distinctLangRes = Db::getInstance()->ExecuteS($queryLang);
    if (!empty($distinctLangRes)) {
        foreach ($distinctLangRes as $idLang) {
            $idLang = $idLang['id_lang'];
            $queryIsoCode = 'SELECT iso_code FROM `'._DB_PREFIX_.'lang` where id_lang='.(int)$idLang;
            $resIsoCode = Db::getInstance()->ExecuteS($queryIsoCode);
            if (isset($resIsoCode[0]["iso_code"])) {
                $tabLang[$idLang] = $resIsoCode[0]["iso_code"];
            }
        }
    }
    return $tabLang;
}

function getFeatureLang()
{
    $tabLang = array();
    $queryLang = 'SELECT *
                    FROM `'._DB_PREFIX_.'feature_lang`
                    WHERE id_lang<>'.(int)Configuration::get('PS_LANG_DEFAULT').'
                    order by `id_feature` ASC';
    $distinctLangRes = Db::getInstance()->ExecuteS($queryLang);
    if (!empty($distinctLangRes)) {
        foreach ($distinctLangRes as $idLang) {
            $preValue = IzifluxTools::winToAscii(trim(Tools::strtolower(utf8_decode($idLang['name']))));
            $value = trim(preg_replace("/[^a-z0-9]/i", "", $preValue));
            $tabLang[$idLang['id_feature']][$idLang['id_lang']] = $value;
        }
    }
    return $tabLang;
}

function getFeatureValueLang()
{
    $tabLang = array();
    $queryLang = 'SELECT *
                    FROM `'._DB_PREFIX_.'feature_value_lang`
                    order by `id_feature_value` ASC';
    $distinctLangRes = Db::getInstance()->ExecuteS($queryLang);
    if (!empty($distinctLangRes)) {
        foreach ($distinctLangRes as $idLang) {
            $tabLang[$idLang['id_feature_value']][$idLang['id_lang']]=cleanForTraduction($idLang['value']);
        }
    }
    return $tabLang;
}
