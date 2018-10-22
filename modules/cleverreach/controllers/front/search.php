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

include_once _PS_MODULE_DIR_ . 'cleverreach/classes/CleverReachApiClient.php';
include_once _PS_MODULE_DIR_ . 'cleverreach/classes/ConfigModel.php';
include_once _PS_MODULE_DIR_ . 'cleverreach/classes/Data.php';
include_once _PS_MODULE_DIR_ . 'cleverreach/classes/CleverReachUtility.php';

class CleverReachSearchModuleFrontController extends ModuleFrontController
{
    /**
     * @var CleverReachApiClient
     */
    private $apiClient;

    /**
     * @var ConfigModel
     */
    private $model;

    public function __construct()
    {
        $this->apiClient = new CleverReachApiClient();
        $this->model = new ConfigModel();

        parent::__construct();
    }

    public function initContent()
    {
        if (Tools::getValue('password') !== $this->model->getProductEndpointPassword()) {
            header($_SERVER['SERVER_PROTOCOL'].' 401 Unauthorized');
            header('WWW-Authenticate: Password"');
            die('401 Unauthorized');
        }

        $param = Tools::getValue('get');

        switch ($param) {
            case 'filter':
                $filter = array(
                    'name' => 'Product reference or ID',
                    'description' => '',
                    'required' => false,
                    'query_key' => 'sku',
                    'type' => 'input',
                );

                CleverReachUtility::dieJson(array($filter, $this->getFilterSelect()));
                break;
            case 'search':
                CleverReachUtility::dieJson($this->getSearchItems());
                break;
        }

        Controller::getController('PageNotFoundController')->run();
    }

    /**
     * Create select filter of all available shops
     *
     * @return array
     */
    private function getFilterSelect()
    {
        $filterSelect = array(
            'name' => 'Store',
            'description' => '',
            'required' => false,
            'query_key' => 'shop',
            'type' => 'dropdown',
        );

        $shops = $this->context->shop->getShops();

        $filterSelect['values'] = array(
            array(
                'text' => 'Please select shop',
                'value' => 0,
            )
        );

        foreach ($shops as $shop) {
            $filterSelect['values'][] = array(
                'text' => $shop['name'],
                'value' => $shop['id_shop'],
            );
        }

        return $filterSelect;
    }

    /**
     * Searches for the product by given POST parameters
     * @return stdClass|array
     */
    private function getSearchItems()
    {
        $items = new \stdClass();
        $items->settings = new \stdClass();
        $items->settings->type = 'product';
        $items->settings->link_editable = false;
        $items->settings->link_text_editable = false;
        $items->settings->image_size_editable = false;

        $item = new \stdClass();

        // SEARCH
        $productClass = new Product();
        $sku = Tools::getValue('sku');
        $shopId = (int)Tools::getValue('shop') ?: null;

        $referenceExists = $productClass->existsRefInDatabase($sku);

        if (!$referenceExists) {
            $product = new Product((int)$sku, true, $this->context->language->id, $shopId);
        } else {
            $product = array();
            $reference = $productClass->searchByName($this->context->language->id, $sku);
            if (isset($reference[0]['id_product'])) {
                $product = new Product((int)$reference[0]['id_product'], true, $this->context->language->id, $shopId);
            }
        }

        if (empty($product) || $product->id === null) {
            return array(
                'status' => Data::NO_PRODUCT,
                'message' => $this->module->l('There is no product with given Reference or ID', 'en'),
            );
        }

        $item->title = $product->name;
        $item->description = $product->description;

        // get product image link
        $imageId = $product->getCover($product->id);
        if (_PS_VERSION_ >= '1.7.0.0') {
            $imageSize = ImageType::getFormattedName('large');
        } else {
            $imageSize = ImageType::getFormatedName('large');
        }

        $item->image = $this->context->link->getImageLink($product->link_rewrite, $imageId['id_image'], $imageSize);

        // get product price
        if (!empty($product->tax_rate)) {
            $item->price = $this->calculateProductPrice($product->price, $product->tax_rate);
        } else {
            $item->price = (float)$product->price;
        }

        $item->price .= ' ' . Context::getContext()->currency->sign;
        $item->link = Context::getContext()->link->getProductLink((int)$product->id, null, null, null, null, $shopId);

        $items->items[] = $item;

        return $items;
    }

    /**
     * Calculate product price based on tax rate
     *
     * @param $price
     * @param $tax_rate
     * @return float
     */
    private function calculateProductPrice($price, $tax_rate)
    {
        return round($price + ((float)$price * (float)$tax_rate * 0.01), 2);
    }
}
