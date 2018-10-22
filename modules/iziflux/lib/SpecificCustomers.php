<?php
/**
 *  2016 Profileo
 *
 *  @author    Profileo <contact@profileo.com>
 *  @copyright 2016 Profileo
 *  @license   Profileo
 *  @link  http://www.profileo.com
 */

/**
 * Class to load specific functionalities for different customer
 *
 * Naming convention shopname_functionName
 *
 * shopname => Shop Name
 * _functionName => name of function
 */
include_once (dirname(__FILE__) . '/IzifluxLib.php');

class SpecificCustomers
{

    public static function clubcase_getHeaderArray()
    {
        return array(
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
            'transporteurs',
            'links_image_mkp'
        );
    }

    public static function alloshoes_getHeaderArray()
    {
        array(
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
            'transporteurs',
            'nom_espagnol',
            'description_courte_espagnol',
            'description_longue_espagnol',
            'nom_anglais',
            'description_courte_anglais',
            'description_longue_anglais',
            'nom_italien',
            'description_courte_italien',
            'description_longue_italien',
            'nom_allemand',
            'description_courte_allemand',
            'description_longue_allemand'
        );
    }

    public static function biocheminee_getHeaderArray()
    {
        return array(
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
            'transporteurs',
            'nom_espagnol',
            'description_courte_espagnol',
            'description_longue_espagnol',
            'nom_anglais',
            'description_courte_anglais',
            'description_longue_anglais',
            'nom_italien',
            'description_courte_italien',
            'description_longue_italien'
        );
    }

    public static function getInfosWaterConcept($product, $id_zone, $carrier)
    {
        if ((Configuration::get('IZIFLUX_ACCOUNT') != 'waterconcept')) {
            return $product;
        } else {
            $link = new Link();
            $infosWaterConcept = Db::getInstance()->ExecuteS('SELECT * FROM ' . _DB_PREFIX_ . 'product_mp
                WHERE id_product = ' . (int)$product['id_product']);
            $infosWaterConcept = $infosWaterConcept[0];
            if (empty($infosWaterConcept) || count($infosWaterConcept) == 0) {
                return $product;
            }
            $product["InfosWaterConcept"] = "";
            
            foreach ($infosWaterConcept as $nomInfoWaterConcept => $valeurInfoWaterConcept) {
                $valeurInfoWaterConcept = trim($valeurInfoWaterConcept);
                
                // Manage text fields
                if (preg_match("/(.*)_mp$/", $nomInfoWaterConcept, $matchNomInfoWaterConcept)) {
                    if ($valeurInfoWaterConcept) {
                        $product["InfosWaterConcept"] .= "#==#$nomInfoWaterConcept:"
                            . IzifluxLib::base64encode(IzifluxTools::htmlEscape($valeurInfoWaterConcept));
                    } else {
                        $product["InfosWaterConcept"] .= "#==#$nomInfoWaterConcept:"
                            . IzifluxLib::base64encode(IzifluxTools::htmlEscape(
                                $product[$matchNomInfoWaterConcept[1]]
                            ));
                    }
                }
 
                /**
                 * gestion des autre champs
                 *
                 * Ebay
                 * /* active_eba> si 0 inactif, si 1 actif
                 * price_ttc_eba> si 'NULL' prendre prix du flux, si valeur forcer cette valeur pour le prix, Attention
                 * valeur TTC
                 *
                 * ship_ttc_eba> si 'NULL' prendre Frais de port du flux, si valeur forcer cette valeur pour le prix du
                 * transport, Attention valeur TTC
                 */
                
                // Manage other fields
                // Ebay active_eba > 0 inactive, 1 active
                // price_ttc_eba > if NULL use flux price, use TTC
                // ship_ttc_eba > if NULL use flux shipping price, use TTC
                
                // force_eba > 0 use ps_product stock, 1 force stock 100
                elseif (preg_match("/^active_(.*)$/", $nomInfoWaterConcept, $matchNomInfoWaterConcept)) {
                    // $matchNomInfoWaterConcept[1] => mkp code
                    $product["InfosWaterConcept"] .= "#==#exporter_vers_" . $matchNomInfoWaterConcept[1] . ":" . $valeurInfoWaterConcept;
                } elseif (preg_match("/^force_(.*)$/", $nomInfoWaterConcept, $matchNomInfoWaterConcept)) {
                    // $matchNomInfoWaterConcept[1] => mkp code
                    if ($valeurInfoWaterConcept) {
                        $product["InfosWaterConcept"] .= "#==#stock_" . $matchNomInfoWaterConcept[1] . ":100";
                    } else {
                        $product["InfosWaterConcept"] .= "#==#stock_" . $matchNomInfoWaterConcept[1] . ":" . $product['quantity'];
                    }
                } elseif (preg_match("/^price_ttc_(.*)$/", $nomInfoWaterConcept, $matchNomInfoWaterConcept)) {
                    // $matchNomInfoWaterConcept[1] => mkp code
                    if ($valeurInfoWaterConcept) {
                        $product["InfosWaterConcept"] .= "#==#prix_de_vente_" . $matchNomInfoWaterConcept[1] . ":" . round($valeurInfoWaterConcept, 2);
                    } else {
                        $product["InfosWaterConcept"] .= "#==#prix_de_vente_" . $matchNomInfoWaterConcept[1] . ":" . $product['price'];
                    }
                } elseif (preg_match("/^ship_ttc_(.*)$/", $nomInfoWaterConcept, $matchNomInfoWaterConcept)) {
                    // $matchNomInfoWaterConcept[1] => mkp code
                    if ($valeurInfoWaterConcept) {
                        $product["InfosWaterConcept"] .= "#==#frais_de_port_" . $matchNomInfoWaterConcept[1] . ":" . round($valeurInfoWaterConcept, 2);
                    } else {
                        $product["InfosWaterConcept"] .= "#==#frais_de_port_" . $matchNomInfoWaterConcept[1] . ":" . UtilsExport::getShipping($carrier, $product['weight'], $product['price'], (Tools::getIsset($product['additional_shipping_cost']) ? $product['additional_shipping_cost'] : 0), $id_zone);
                    }
                }
            }
            return $product;
        }
    }

    public static function coquinesstreet_getProductAttributeExport($product, $ean13Declinaison, $decli, $classification, $carrier, $promo, $poidsDeclinaison, $liste_features2Decli, $cumulTransporteurs2, $id_zone)
    {
        $product_ship = $product['additional_shipping_cost'];
        $replenish = Configuration::get('IZIFLUX_DELAY_REPLENISHING');
        $delivery_delay = Configuration::get('IZIFLUX_DELAY_DELIVERY');
        $product_available = $product['available_now'];
        
        return array(
            $product['id_product'],
            $ean13Declinaison,
            $product['name'] . ' ' . $decli['name'],
            IzifluxTools::htmlEscape($product['description_short']),
            IzifluxTools::htmlEscape($product['description']),
            $product['link'],
            $decli['images'] . Manufacturer::getNameById($product['id_manufacturer']),
            $classification,
            $product['id_product'] . '_' . $decli['id_product_attribute'],
            $decli['disponibilite'],
            $decli['statut_disponibilite'],
            $decli['price'],
            round($product['ecotax'], 2),
            $decli['quantity'],
            ($decli['disponibilite'] == 'Y' ? (is_numeric($product['available_later']) ? $product['available_later'] : $replenish) : 0),
            (is_numeric($product_available) ? $product_available : $delivery_delay),
            Configuration::get('IZIFLUX_DELAY_UNIT'),
            '',
            '',
            IzifluxTools::getShippingPrice($carrier, $poidsDeclinaison, $decli['price'], (Tools::getIsset($product_ship) ? $product_ship : 0), $id_zone),
            $carrier->name,
            $decli['price'],
            $product['meta_keywords'],
            $promo['from'],
            $promo['to'],
            $decli['price_without_reduction'],
            $promo['amount'],
            $promo['percent'],
            $decli['price'],
            $promo['type'],
            0,
            $decli['reference'],
            $decli['supplier_reference'],
            $poidsDeclinaison . $liste_features2Decli . $cumulTransporteurs2
        );
    }

    public static function clubcase_getProductAttributeExport($product, $ean13Declinaison, $decli, $classification, $carrier, $promo, $poidsDeclinaison, $liste_features2Decli, $cumulTransporteurs2, $id_zone)
    {
        $shipping_cost = $product['additional_shipping_cost'];
        $replenish = Configuration::get('IZIFLUX_DELAY_REPLENISHING');
        $product_available = $product['available_now'];
        return array(
            $product['id_product'],
            $ean13Declinaison,
            $product['name'],
            $product['name'] . ' ' . $decli['name_subjective'],
            IzifluxTools::htmlEscape($product['description_short']),
            IzifluxTools::htmlEscape($product['description']),
            $product['link'],
            $decli['images'] . Manufacturer::getNameById($product['id_manufacturer']),
            $classification,
            $product['id_product'] . '#IZI#' . $decli['id_product_attribute'],
            $decli['disponibilite'],
            $decli['statut_disponibilite'],
            $decli['price'],
            round($product['ecotax'], 2),
            '|' . $decli['quantity'],
            ($decli['disponibilite'] == 'Y' ? (is_numeric($product['available_later']) ? $product['available_later'] : $replenish) : 0),
            (is_numeric($product_available) ? $product_available : Configuration::get('IZIFLUX_DELAY_DELIVERY')),
            Configuration::get('IZIFLUX_DELAY_UNIT'),
            '',
            '',
            IzifluxTools::getShippingPrice($carrier, $poidsDeclinaison, $decli['price'], (Tools::getIsset($shipping_cost) ? $shipping_cost : 0), $id_zone),
            $carrier->name,
            $decli['price'],
            $product['meta_keywords'],
            $promo['from'],
            $promo['to'],
            $decli['price_without_reduction'],
            $promo['amount'],
            $promo['percent'],
            $decli['price'],
            $promo['type'],
            0,
            $decli['reference'],
            $decli['supplier_reference'],
            $poidsDeclinaison,
            $liste_features2Decli,
            $cumulTransporteurs2,
            $decli['imagesMkp']
        );
    }

    public static function costumecravate_getProductAttributeExport($product, $ean13Declinaison, $decli, $classification, $carrier, $promo, $poidsDeclinaison, $liste_features2Decli, $cumulTransporteurs2, $id_zone)
    {
        $shipping_cost = $product['additional_shipping_cost'];
        return array(
            $product['id_product'],
            $ean13Declinaison,
            $product['name'],
            $product['name'] . ' ' . $decli['name_subjective'],
            IzifluxTools::htmlEscape($product['description_short']),
            IzifluxTools::htmlEscape($product['description']),
            $product['link'],
            $decli['images'] . Manufacturer::getNameById($product['id_manufacturer']),
            $classification,
            $decli['reference'],
            $decli['disponibilite'],
            $decli['statut_disponibilite'],
            $decli['price'],
            round($product['ecotax'], 2),
            $decli['quantity'],
            ($decli['disponibilite'] == 'Y' ? (is_numeric($product['available_later']) ?
                    $product['available_later'] : Configuration::get('IZIFLUX_DELAY_REPLENISHING')) : 0),
            (is_numeric($product['available_now']) ? $product['available_now'] :
                    Configuration::get('IZIFLUX_DELAY_DELIVERY')),
                Configuration::get('IZIFLUX_DELAY_UNIT'),
            '',
            '',
            IzifluxTools::getShippingPrice(
                $carrier,
                $poidsDeclinaison,
                $decli['price'],
                (Tools::getIsset(
                    $shipping_cost
                ) ? $shipping_cost : 0),
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
            $promo['type'],
            0,
            $product['id_product'] . '#IZI#' . $decli['id_product_attribute'],
            $decli['supplier_reference'],
            $poidsDeclinaison,
            $liste_features2Decli,
            $cumulTransporteurs2
        );
    }

    public static function mobiliershop_getProductAttributeExport($product, $ean13Declinaison, $decli, $classification, $carrier, $promo, $poidsDeclinaison, $liste_features2Decli, $cumulTransporteurs2, $id_zone)
    {
        $shipping_cost = $product['additional_shipping_cost'];
        
        return array(
            $product['id_product'],
            $ean13Declinaison,
            $product['name'],
            $product['name'] . ' ' . $decli['name_subjective'],
            IzifluxTools::htmlEscape(htmlentities($product['description_article'])),
            $product['link'],
            $decli['images'] . Manufacturer::getNameById($product['id_manufacturer']) . '|',
            $classification,
            $product['id_product'] . '#IZI#' . $decli['id_product_attribute'],
            $decli['disponibilite'],
            $decli['statut_disponibilite'],
            $decli['price'],
            round($product['ecotax'], 2),
            '|' . $decli['quantity'],
            ($decli['disponibilite'] == 'Y' ? (is_numeric($product['available_later']) ?
                $product['available_later'] : Configuration::get('IZIFLUX_DELAY_REPLENISHING')) : 0),
            (is_numeric($product['available_now']) ? $product['available_now'] :
                Configuration::get('IZIFLUX_DELAY_DELIVERY')),
            Configuration::get('IZIFLUX_DELAY_UNIT'),
            '',
            '',
            IzifluxTools::getShippingPrice(
                $carrier,
                $poidsDeclinaison,
                $decli['price'],
                (Tools::getIsset($shipping_cost) ? $shipping_cost : 0),
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
            $promo['type'],
            0,
            $decli['reference'],
            $decli['supplier_reference'],
            $poidsDeclinaison,
            $liste_features2Decli,
            $cumulTransporteurs2
        );
    }

    public static function alloshoes_getProductAttributeExport($product, $ean13Declinaison, $decli, $classification, $carrier, $promo, $poidsDeclinaison, $liste_features2Decli, $cumulTransporteurs2, $id_zone)
    {
        
        // Spanish data
        $queryES = 'SELECT name,description_short,description
		                  FROM `' . _DB_PREFIX_ . 'product_lang` WHERE id_lang=6 and meta_title<>"" and id_product="' . (int)$product['id_product'] . '"';
        $texteEs = Db::getInstance()->ExecuteS($queryES);
        
        // English data
        $queryEn = 'SELECT name,description_short,description
		                  FROM `' . _DB_PREFIX_ . 'product_lang` WHERE id_lang=3 and meta_title<>"" and id_product="' . (int)$product['id_product'] . '"';
        $texteEn = Db::getInstance()->ExecuteS($queryEn);
        
        // Italian data
        $queryIt = 'SELECT name,description_short,description
		FROM `' . _DB_PREFIX_ . 'product_lang` WHERE id_lang=2 and meta_title<>"" and id_product="' . (int)$product['id_product'] . '"';
        $texteIt = Db::getInstance()->ExecuteS($queryIt);
        
        // German data
        $queryAl = 'SELECT name,description_short,description
		FROM `' . _DB_PREFIX_ . 'product_lang` WHERE id_lang=4 and meta_title<>"" and id_product="' . (int)$product['id_product'] . '"';
        $texteAl = Db::getInstance()->ExecuteS($queryAl);
        $shipping_cost = $product['additional_shipping_cost'];
        return array(
            $product['id_product'],
            $ean13Declinaison,
            $product['name'],
            $product['name'] . ' ' . $decli['name_subjective'],
            IzifluxTools::htmlEscape($product['description_short']),
            IzifluxTools::htmlEscape($product['description']),
            $product['link'],
            $decli['images'] . Manufacturer::getNameById($product['id_manufacturer']),
            $classification,
            $product['id_product'] . '#IZI#' . $decli['id_product_attribute'],
            $decli['disponibilite'],
            $decli['statut_disponibilite'],
            $decli['price'],
            round($product['ecotax'], 2),
            $decli['quantity'],
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
                (Tools::getIsset($shipping_cost) ? $shipping_cost : 0),
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
            $promo['type'],
            0,
            $decli['reference'],
            $decli['supplier_reference'],
            $poidsDeclinaison,
            $liste_features2Decli,
            $cumulTransporteurs2,
            $texteEs[0]["name"],
            IzifluxTools::htmlEscape($texteEs[0]["description_short"]),
            IzifluxTools::htmlEscape($texteEs[0]["description"]),
            $texteEn[0]["name"],
            IzifluxTools::htmlEscape($texteEn[0]["description_short"]),
            IzifluxTools::htmlEscape($texteEn[0]["description"]),
            $texteIt[0]["name"],
            IzifluxTools::htmlEscape($texteIt[0]["description_short"]),
            IzifluxTools::htmlEscape($texteIt[0]["description"]),
            $texteAl[0]["name"],
            IzifluxTools::htmlEscape($texteAl[0]["description_short"]),
            IzifluxTools::htmlEscape($texteAl[0]["description"])
        );
    }

    public static function biocheminee_getProductAttributeExport($product, $ean13Declinaison, $decli, $classification, $carrier, $promo, $poidsDeclinaison, $liste_features2Decli, $cumulTransporteurs2, $id_zone)
    {
        // Spanish data
        $queryES = 'SELECT name,description_short,description
		FROM `' . _DB_PREFIX_ . 'product_lang` WHERE id_lang=2 and meta_title<>"" and id_product="' . (int)$product['id_product'] . '"';
        $texteEs = Db::getInstance()->ExecuteS($queryES);
        
        // English data
        $queryEn = 'SELECT name,description_short,description
		FROM `' . _DB_PREFIX_ . 'product_lang` WHERE id_lang=3 and meta_title<>"" and id_product="' . (int)$product['id_product'] . '"';
        $texteEn = Db::getInstance()->ExecuteS($queryEn);
        
        // Italian data
        $queryIt = 'SELECT name,description_short,description
		FROM `' . _DB_PREFIX_ . 'product_lang` WHERE id_lang=4 and meta_title<>"" and id_product="' . (int)$product['id_product'] . '"';
        $texteIt = Db::getInstance()->ExecuteS($queryIt);
        
        $shipping_cost = $product['additional_shipping_cost'];
        return array(
            $product['id_product'],
            $ean13Declinaison,
            $product['name'],
            $product['name'] . ' ' . $decli['name_subjective'],
            IzifluxTools::htmlEscape($product['description_short']),
            IzifluxTools::htmlEscape($product['description']),
            $product['link'],
            $decli['images'] . Manufacturer::getNameById($product['id_manufacturer']),
            $classification,
            $product['id_product'] . '#IZI#' . $decli['id_product_attribute'],
            $decli['disponibilite'],
            $decli['statut_disponibilite'],
            $decli['price'],
            round($product['ecotax'], 2),
            $decli['quantity'],
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
                (Tools::getIsset($shipping_cost) ? $shipping_cost : 0),
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
            $promo['type'],
            0,
            $decli['reference'],
            $decli['supplier_reference'],
            $poidsDeclinaison,
            $liste_features2Decli,
            $cumulTransporteurs2,
            IzifluxLib::base64encode($texteEs[0]["name"]),
            IzifluxLib::base64encode(IzifluxTools::htmlEscape($texteEs[0]["description_short"])),
            IzifluxLib::base64encode(IzifluxTools::htmlEscape($texteEs[0]["description"])),
            IzifluxLib::base64encode($texteEn[0]["name"]),
            IzifluxLib::base64encode(IzifluxTools::htmlEscape($texteEn[0]["description_short"])),
            IzifluxLib::base64encode(IzifluxTools::htmlEscape($texteEn[0]["description"])),
            IzifluxLib::base64encode($texteIt[0]["name"]),
            IzifluxLib::base64encode(IzifluxTools::htmlEscape($texteIt[0]["description_short"])),
            IzifluxLib::base64encode(IzifluxTools::htmlEscape($texteIt[0]["description"]))
        );
    }

    public static function waterconcept_getProductAttributeExport($product, $ean13Declinaison, $decli, $classification, $carrier, $promo, $poidsDeclinaison, $liste_features2Decli, $cumulTransporteurs2, $id_zone)
    {
        $shipping_cost = $product['additional_shipping_cost'];
        return array(
            $product['id_product'],
            $ean13Declinaison,
            $product['name'],
            $product['name'] . ' ' . $decli['name_subjective'],
            IzifluxTools::htmlEscape($product['description_short']),
            IzifluxTools::htmlEscape($product['description']),
            $product['link'],
            $decli['images'] . Manufacturer::getNameById($product['id_manufacturer']),
            $classification,
            $product['id_product'] . '#IZI#' . $decli['id_product_attribute'],
            $decli['disponibilite'],
            $decli['statut_disponibilite'],
            $decli['price'],
            round($product['ecotax'], 2),
            $decli['quantity'],
            ($decli['disponibilite'] == 'Y' ? (is_numeric($product['available_later']) ? $product['available_later'] : Configuration::get('IZIFLUX_DELAY_REPLENISHING')) : 0),
            (is_numeric($product['available_now']) ? $product['available_now'] : Configuration::get('IZIFLUX_DELAY_DELIVERY')),
            Configuration::get('IZIFLUX_DELAY_UNIT'),
            '',
            '',
            IzifluxTools::getShippingPrice($carrier, $poidsDeclinaison, $decli['price'], (Tools::getIsset($shipping_cost) ? $shipping_cost : 0), $id_zone),
            $carrier->name,
            $decli['price'],
            $product['meta_keywords'],
            $promo['from'],
            $promo['to'],
            $decli['price_without_reduction'],
            $promo['amount'],
            $promo['percent'],
            $decli['price'],
            $promo['type'],
            0,
            $decli['reference'],
            $decli['supplier_reference'],
            $poidsDeclinaison,
            $liste_features2Decli . $product["InfosWaterConcept"],
            $cumulTransporteurs2
        );
    }

    public static function clubcase_getProductExport($product, $links_image, $classification, $dispo, $statut_dispo, $carrier, $promo, $liste_features2, $cumulTransporteurs2, $links_image_mkp, $id_zone)
    {
        $replenish = Configuration::get('IZIFLUX_DELAY_REPLENISHING');
        $shipping_cost = $product['additional_shipping_cost'];
        return array(
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
            $product['quantity'],
            ($dispo == 'Y' ? (is_numeric($product['available_later']) ? $product['available_later'] : $replenish) : 0),
            (is_numeric($product['available_now']) ? $product['available_now'] : Configuration::get('IZIFLUX_DELAY_DELIVERY')),
            Configuration::get('IZIFLUX_DELAY_UNIT'),
            '',
            '',
            IzifluxTools::getShippingPrice($carrier, $product['weight'], $product['price'], (Tools::getIsset(shipping_cost) ? shipping_cost : 0), $id_zone),
            $carrier->name,
            Product::getPriceStatic($product['id_product'], true),
            $product['meta_keywords'],
            $promo['from'],
            $promo['to'],
            $product['price_without_reduction'],
            $promo['amount'],
            $promo['percent'],
            Product::getPriceStatic($product['id_product'], true),
            $promo['type'],
            0,
            '',
            $product['reference'],
            $product['supplier_reference'],
            $product['weight'],
            $liste_features2,
            $cumulTransporteurs2,
            $links_image_mkp
        );
    }

    public static function costumecravate_getProductExport($product, $links_image, $classification, $dispo, $statut_dispo, $carrier, $promo, $liste_features2, $cumulTransporteurs2, $links_image_mkp, $id_zone)
    {
        $replenish = Configuration::get('IZIFLUX_DELAY_REPLENISHING');
        $shipping_cost = $product['additional_shipping_cost'];
        return array(
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
            $product['reference'],
            $dispo,
            $statut_dispo,
            Product::getPriceStatic($product['id_product'], true),
            round($product['ecotax'], 2),
            '',
            $product['quantity'],
            ($dispo == 'Y' ? (is_numeric($product['available_later']) ? $product['available_later'] : $replenish) : 0),
            (is_numeric($product['available_now']) ? $product['available_now'] : Configuration::get('IZIFLUX_DELAY_DELIVERY')),
            Configuration::get('IZIFLUX_DELAY_UNIT'),
            '',
            '',
            IzifluxTools::getShippingPrice($carrier, $product['weight'], $product['price'], (Tools::getIsset(shipping_cost) ? shipping_cost : 0), $id_zone),
            $carrier->name,
            Product::getPriceStatic($product['id_product'], true),
            $product['meta_keywords'],
            $promo['from'],
            $promo['to'],
            $product['price_without_reduction'],
            $promo['amount'],
            $promo['percent'],
            Product::getPriceStatic($product['id_product'], true),
            $promo['type'],
            0,
            $product['reference'],
            $product['supplier_reference'],
            $product['weight'],
            $liste_features2,
            $cumulTransporteurs2
        );
    }

    public static function mobiliershop_getProductExport($product, $links_image, $classification, $dispo, $statut_dispo, $carrier, $promo, $liste_features2, $cumulTransporteurs2, $links_image_mkp, $id_zone)
    {
        $replenish = Configuration::get('IZIFLUX_DELAY_REPLENISHING');
        $shipping_cost = $product['additional_shipping_cost'];
        return array(
            $product['id_product'],
            $product['ean13'],
            $product['name'],
            $product['name'],
            '',
            IzifluxTools::htmlEscape(htmlentities($product['description_article'])),
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
            $product['quantity'],
            ($dispo == 'Y' ? (is_numeric($product['available_later']) ? $product['available_later'] : $replenish) : 0),
            (is_numeric($product['available_now']) ? $product['available_now'] : Configuration::get('IZIFLUX_DELAY_DELIVERY')),
            Configuration::get('IZIFLUX_DELAY_UNIT'),
            '',
            '',
            IzifluxTools::getShippingPrice($carrier, $product['weight'], $product['price'], (Tools::getIsset($shipping_cost) ? $shipping_cost : 0), $id_zone),
            $carrier->name,
            Product::getPriceStatic($product['id_product'], true),
            $product['meta_keywords'],
            $promo['from'],
            $promo['to'],
            $product['price_without_reduction'],
            $promo['amount'],
            $promo['percent'],
            Product::getPriceStatic($product['id_product'], true),
            $promo['type'],
            0,
            $product['reference'],
            $product['supplier_reference'],
            $product['weight'],
            $liste_features2,
            $cumulTransporteurs2
        );
    }

    public static function alloshoes_getProductExport($product, $links_image, $classification, $dispo, $statut_dispo, $carrier, $promo, $liste_features2, $cumulTransporteurs2, $links_image_mkp, $id_zone)
    {
        $shipping_cost = $product['additional_shipping_cost'];
        // Spanish data
        $queryES = 'SELECT name,description_short,description
		                  FROM `' . _DB_PREFIX_ . 'product_lang` WHERE id_lang=6 and meta_title<>"" and id_product="' . (int)$product['id_product'] . '"';
        $texteEs = Db::getInstance()->ExecuteS($queryES);
        
        // English data
        $queryEn = 'SELECT name,description_short,description
		                  FROM `' . _DB_PREFIX_ . 'product_lang` WHERE id_lang=3 and meta_title<>"" and id_product="' . (int)$product['id_product'] . '"';
        $texteEn = Db::getInstance()->ExecuteS($queryEn);
        
        // Italian data
        $queryIt = 'SELECT name,description_short,description
		FROM `' . _DB_PREFIX_ . 'product_lang` WHERE id_lang=2 and meta_title<>"" and id_product="' . (int)$product['id_product'] . '"';
        $texteIt = Db::getInstance()->ExecuteS($queryIt);
        
        // German data
        $queryAl = 'SELECT name,description_short,description
		FROM `' . _DB_PREFIX_ . 'product_lang` WHERE id_lang=4 and meta_title<>"" and id_product="' . (int)$product['id_product'] . '"';
        $texteAl = Db::getInstance()->ExecuteS($queryAl);
        
        $replenish = Configuration::get('IZIFLUX_DELAY_REPLENISHING');
        
        return array(
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
            $product['quantity'],
            ($dispo == 'Y' ? (is_numeric($product['available_later']) ? $product['available_later'] : $replenish) : 0),
            (is_numeric($product['available_now']) ? $product['available_now'] : Configuration::get('IZIFLUX_DELAY_DELIVERY')),
            Configuration::get('IZIFLUX_DELAY_UNIT'),
            '',
            '',
            IzifluxTools::getShippingPrice($carrier, $product['weight'], $product['price'], (Tools::getIsset($shipping_cost) ? $shipping_cost : 0), $id_zone),
            $carrier->name,
            Product::getPriceStatic($product['id_product'], true),
            $product['meta_keywords'],
            $promo['from'],
            $promo['to'],
            $product['price_without_reduction'],
            $promo['amount'],
            $promo['percent'],
            Product::getPriceStatic($product['id_product'], true),
            $promo['type'],
            0,
            $product['reference'],
            $product['supplier_reference'],
            $product['weight'],
            $liste_features2,
            $cumulTransporteurs2,
            $texteEs[0]["name"],
            IzifluxTools::htmlEscape($texteEs[0]["description_short"]),
            IzifluxTools::htmlEscape($texteEs[0]["description"]),
            $texteEn[0]["name"],
            IzifluxTools::htmlEscape($texteEn[0]["description_short"]),
            IzifluxTools::htmlEscape($texteEn[0]["description"]),
            $texteIt[0]["name"],
            IzifluxTools::htmlEscape($texteIt[0]["description_short"]),
            IzifluxTools::htmlEscape($texteIt[0]["description"]),
            $texteAl[0]["name"],
            IzifluxTools::htmlEscape($texteAl[0]["description_short"]),
            IzifluxTools::htmlEscape($texteAl[0]["description"])
        );
    }

    public static function biocheminee_getProductExport($product, $links_image, $classification, $dispo, $statut_dispo, $carrier, $promo, $liste_features2, $cumulTransporteurs2, $links_image_mkp, $id_zone)
    {
        // Spanish data
        $queryES = 'SELECT name,description_short,description
		FROM `' . _DB_PREFIX_ . 'product_lang` WHERE id_lang=2 and meta_title<>"" and id_product="' . (int)$product['id_product'] . '"';
        $texteEs = Db::getInstance()->ExecuteS($queryES);
        
        // English data
        $queryEn = 'SELECT name,description_short,description
		FROM `' . _DB_PREFIX_ . 'product_lang` WHERE id_lang=3 and meta_title<>"" and id_product="' . (int)$product['id_product'] . '"';
        $texteEn = Db::getInstance()->ExecuteS($queryEn);
        
        // Italian data
        $queryIt = 'SELECT name,description_short,description
		FROM `' . _DB_PREFIX_ . 'product_lang` WHERE id_lang=4 and meta_title<>"" and id_product="' . (int)$product['id_product'] . '"';
        $texteIt = Db::getInstance()->ExecuteS($queryIt);
        
        $replenish = Configuration::get('IZIFLUX_DELAY_REPLENISHING');
        $shipping_cost = $product['additional_shipping_cost'];
        return array(
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
            $product['quantity'],
            ($dispo == 'Y' ? (is_numeric($product['available_later']) ? $product['available_later'] : $replenish) : 0),
            (is_numeric($product['available_now']) ? $product['available_now'] : Configuration::get('IZIFLUX_DELAY_DELIVERY')),
            Configuration::get('IZIFLUX_DELAY_UNIT'),
            '',
            '',
            IzifluxTools::getShippingPrice($carrier, $product['weight'], $product['price'], (Tools::getIsset(shipping_cost) ? shipping_cost : 0), $id_zone),
            $carrier->name,
            Product::getPriceStatic($product['id_product'], true),
            $product['meta_keywords'],
            $promo['from'],
            $promo['to'],
            $product['price_without_reduction'],
            $promo['amount'],
            $promo['percent'],
            Product::getPriceStatic($product['id_product'], true),
            $promo['type'],
            0,
            $product['reference'],
            $product['supplier_reference'],
            $product['weight'],
            $liste_features2,
            $cumulTransporteurs2,
            IzifluxLib::base64encode($texteEs[0]["name"]),
            IzifluxLib::base64encode(IzifluxTools::htmlEscape($texteEs[0]["description_short"])),
            IzifluxLib::base64encode(IzifluxTools::htmlEscape($texteEs[0]["description"])),
            IzifluxLib::base64encode($texteEn[0]["name"]),
            IzifluxLib::base64encode(IzifluxTools::htmlEscape($texteEn[0]["description_short"])),
            IzifluxLib::base64encode(IzifluxTools::htmlEscape($texteEn[0]["description"])),
            IzifluxLib::base64encode($texteIt[0]["name"]),
            IzifluxLib::base64encode(IzifluxTools::htmlEscape($texteIt[0]["description_short"])),
            IzifluxLib::base64encode(IzifluxTools::htmlEscape($texteIt[0]["description"]))
        );
    }

    public static function waterconcept_getProductExport($product, $links_image, $classification, $dispo, $statut_dispo, $carrier, $promo, $liste_features2, $cumulTransporteurs2, $links_image_mkp, $id_zone)
    {
        $replenish = Configuration::get('IZIFLUX_DELAY_REPLENISHING');
        $shipping_cost = $product['additional_shipping_cost'];
        return array(
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
            $product['quantity'],
            ($dispo == 'Y' ? (is_numeric($product['available_later']) ? $product['available_later'] : $replenish) : 0),
            (is_numeric($product['available_now']) ? $product['available_now'] : Configuration::get('IZIFLUX_DELAY_DELIVERY')),
            Configuration::get('IZIFLUX_DELAY_UNIT'),
            '',
            '',
            IzifluxTools::getShippingPrice($carrier, $product['weight'], $product['price'], (Tools::getIsset(shipping_cost) ? shipping_cost : 0), $id_zone),
            $carrier->name,
            Product::getPriceStatic($product['id_product'], true),
            $product['meta_keywords'],
            $promo['from'],
            $promo['to'],
            $product['price_without_reduction'],
            $promo['amount'],
            $promo['percent'],
            Product::getPriceStatic($product['id_product'], true),
            $promo['type'],
            0,
            $product['reference'],
            $product['supplier_reference'],
            $product['weight'],
            $liste_features2 . $product["InfosWaterConcept"],
            $cumulTransporteurs2
        );
    }

    public static function getCategoryDefaultMobilierShop($product)
    {
        if ((Configuration::get('IZIFLUX_ACCOUNT') == 'mobiliershop')) {
            $queryCateg = 'SELECT max(`id_category`) as id_category from `' . _DB_PREFIX_ . 'category_product`' . ' WHERE `id_product` ="' . (int)$product['id_product'] . '"';
            $resQueryCateg = Db::getInstance()->ExecuteS($queryCateg);
            $product['id_category_default'] = $resQueryCateg[0]["id_category"];
        }
        return $product;
    }

    public static function enviedemeubles_getTaxRate()
    {
        return '20';
    }

    public static function toutacoo_getShopId()
    {
        return '1';
    }

    public static function maquillageetcosmetique_getShopId()
    {
        return '1';
    }

    public static function toutacoo_getExecuteCurl($account_name, $compteSupp)
    {
        if (($account_name == 'toutacoo') && ! $compteSupp) {
            $urlAppelCurl = _PS_BASE_URL_ . "/modules/iziflux/order_integration.php?compteSupp=toutacoous";
            echo $urlAppelCurl . "<br>\r\n";
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $urlAppelCurl);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_exec($curl);
            curl_close($curl);
        }
    }
}
