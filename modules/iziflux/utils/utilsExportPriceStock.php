<?php
/**
 *  2016 Profileo
 *
 *  @author    Profileo <contact@profileo.com>
 *  @copyright 2016 Profileo
 *  @license   Profileo
 *  @link  http://www.profileo.com
 */

class ExportPriceStock
{

    /**
     * Executes the export of price and stocks, writes it to a file and displays it
     */
    public static function executePriceStock()
    {
        // Variables declaration
        $file_export = _PS_MODULE_DIR_ . 'iziflux/priceStock.txt';
        $handle = fopen($file_export, 'w+');
        define('SEP', '|');

        // Get the products to export
        $products = Product::getProducts(
            Configuration::get('PS_LANG_DEFAULT'),
            0,
            30000,
            'id_product',
            'ASC',
            false,
            true
        );

        $nbTotalProduct = count($products);

        if ($nbTotalProduct) {
        }

        // clear log
        IzifluxTools::clearLogFile(_PS_MODULE_DIR_ . 'iziflux/log/logPriceStock.txt');

        IzifluxTools::logDebug(
            'utilsExportPriceStock.php '.__LINE__.
            ': ExportPriceStock: total product to be exported = '
            . count($products),
            1
        );

        $fieldList = ExportPriceStock::getHeaders();
        fwrite($handle, implode(SEP, $fieldList) . chr(10));

        $j = 0;
        // Loop on all products
        foreach ($products as $product) {
            $j ++;

            IzifluxTools::logDebug(
                'utilsExportPriceStock.php '.__LINE__.
                ': ExportPriceStock: will start export for product id = '
                . $product['id_product'],
                1
            );

            // Get the datas
            $product = Product::getProductProperties(Configuration::get('PS_LANG_DEFAULT'), $product);
            if (! $product) {
                IzifluxTools::logDebug(
                    'utilsExportPriceStock.php '.__LINE__.
                    ': ExportPriceStock: load of product properties KO',
                    1
                );
                continue;
            } else {
                IzifluxTools::logDebug(
                    'utilsExportPriceStock.php '.__LINE__.
                    ': ExportPriceStock: load of product properties OK',
                    1
                );
            }
            if (Tools::getIsset('extractAttributes') && Tools::getValue('extractAttributes') == "0") {
                $attributes = false;
            } else {
                $attributes = IzifluxTools::getAttributesLight($product, Configuration::get('PS_LANG_DEFAULT'));
            }

            $dispo = '';
            $statut_dispo = '';

            if ($attributes) {
                IzifluxTools::logDebug(
                    'utilsExportPriceStock.php '.__LINE__.
                    ': ExportPriceStock: product has combinations, will export each combination',
                    1
                );
                foreach ($attributes as $decli) {
                    $promo = IzifluxTools::getPromo($product, $decli);
                    fwrite($handle, implode(
                        SEP,
                        ExportPriceStock::getExportPriceStockProductAttribute(
                            $fieldList,
                            $product,
                            $promo,
                            $decli
                        )
                    ) . chr(10));
                }
            } else {
                $promo = IzifluxTools::getPromo($product);
                IzifluxTools::logDebug(
                    'utilsExportPriceStock.php '.__LINE__.
                    ': ExportPriceStock: product has no combination, will export simple product',
                    1
                );
                $prod_stock = Product::isAvailableWhenOutOfStock($product['out_of_stock']);
                $dispo = ($product['quantity'] >= 0 ? 'Y' : ($prod_stock ? 'Y' : 'N'));
                $statut_dispo = ($product['quantity'] >= 0 ? 'S' : ($prod_stock ? 'R' : 'V'));
                $infos = ExportPriceStock::getExportPriceStockProduct(
                    $fieldList,
                    $product,
                    $dispo,
                    $statut_dispo,
                    $promo
                );
                fwrite($handle, implode(SEP, $infos) . chr(10));
            }
            IzifluxTools::logDebug(
                'utilsExportPriceStock.php '.__LINE__.
                ': ExportPriceStock: ##############################',
                1
            );
        }

        echo Tools::file_get_contents($file_export);
    }

    /**
     * Returns the headers to be written
     *
     * @return string[]
     */
    public static function getHeaders()
    {
        if (Tools::getIsset('champs')) {
            return explode(SEP, Tools::getValue('champs'));
        } else {
            return array(
                'id-produit',
                'code-ean',
                'id-reference',
                'disponibilite',
                'statut-dedisponibilite',
                'prix-ttc',
                'ecotaxe',
                'quantite',
                'dateheure-de-debut-promotion',
                'date-heure-de-fin-promotion',
                'prix-ttc-avant-promotion',
                'promotion',
                'pourcentage-de-la-remise',
                'prixremise',
                'type-de-promotion',
                'refInterne',
                'refFAbricant'
            );
        }
    }

    /**
     * Get the values to be exported for a product as an array
     */
    public static function getExportPriceStockProduct($fieldList, $product, $dispo, $statut_dispo, $promo)
    {
        $res = array();
        foreach ($fieldList as $champ) {
            switch ($champ) {
                case 'id-produit':
                    $res[] = $product['id_product'];
                    break;
                case 'code-ean':
                    $res[] = $product['ean13'];
                    break;
                case 'id-reference':
                    $res[] = $product['id_product'];
                    break;
                case 'disponibilite':
                    $res[] = $dispo;
                    break;
                case 'statut-dedisponibilite':
                    $res[] = $statut_dispo;
                    break;
                case 'prix-ttc':
                    $res[] = $product['price'];
                    break;
                case 'ecotaxe':
                    $res[] = round($product['ecotax'], 2);
                    break;
                case 'quantite':
                    $res[] = $product['quantity'];
                    break;
                case 'dateheure-de-debut-promotion':
                    $res[] = $promo['from'];
                    break;
                case 'date-heure-de-fin-promotion':
                    $res[] = $promo['to'];
                    break;
                case 'prix-ttc-avant-promotion':
                    $res[] = $product['price_without_reduction'];
                    break;
                case 'promotion':
                    $res[] = $promo['amount'];
                    break;
                case 'pourcentage-de-la-remise':
                    $res[] = $promo['percent'];
                    break;
                case 'prixremise':
                    $res[] = $product['price'];
                    break;
                case 'type-de-promotion':
                    $res[] = $promo['type'];
                    break;
                case 'refInterne':
                    $res[] = $product['reference'];
                    break;
                case 'refFAbricant':
                    $res[] = $product['supplier_reference'];
            }
        }
        return $res;
    }

    /**
     * Get the values to be exported for a product combination as an array
     */
    public static function getExportPriceStockProductAttribute(
        $fieldList,
        $product,
        $promo,
        $decli
    ) {
        $res = array();
        foreach ($fieldList as $champ) {
            switch ($champ) {
                case 'id-produit':
                    $res[] = $product['id_product'];
                    break;
                case 'code-ean':
                    $res[] = $decli['ean13'];
                    break;
                case 'id-reference':
                    $res[] = $product['id_product'] . '#IZI#' . $decli['id_product_attribute'];
                    break;
                case 'disponibilite':
                    $res[] = $decli['disponibilite'];
                    break;
                case 'statut-dedisponibilite':
                    $res[] = $decli['statut_disponibilite'];
                    break;
                case 'prix-ttc':
                    $res[] = $decli['price'];
                    break;
                case 'ecotaxe':
                    $res[] = round($product['ecotax'], 2);
                    break;
                case 'quantite':
                    $res[] = $decli['quantity'];
                    break;
                case 'dateheure-de-debut-promotion':
                    $res[] = $promo['from'];
                    break;
                case 'date-heure-de-fin-promotion':
                    $res[] = $promo['to'];
                    break;
                case 'prix-ttc-avant-promotion':
                    $res[] = $decli['price_without_reduction'];
                    break;
                case 'promotion':
                    $res[] = $promo['amount'];
                    break;
                case 'pourcentage-de-la-remise':
                    $res[] = $promo['percent'];
                    break;
                case 'prixremise':
                    $res[] = $decli['price'];
                    break;
                case 'type-de-promotion':
                    $res[] = $promo['type'];
                    break;
                case 'refInterne':
                    $res[] = $decli['reference'];
                    break;
                case 'refFAbricant':
                    $res[] = $decli['supplier_reference'];
                    break;
            }
        }
        return $res;
    }
}
