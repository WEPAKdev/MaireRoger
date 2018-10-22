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

class CleverReachAdminController extends AdminController
{
    /**
     * @var ConfigModel
     */
    private $model;

    /**
     * @var CleverReachApiClient
     */
    private $apiClient;

    public function __construct()
    {
        $this->bootstrap = true;
        $this->apiClient = new CleverReachApiClient();
        $this->model = new ConfigModel();

        parent::__construct();
    }

    /**
     * Initialize content
     */
    public function initContent()
    {
        parent::initContent();
        $this->initTabModuleList();
        $this->initToolbar();
        $this->initPageHeaderToolbar();
        $this->addToolBarModulesListButton();
        unset($this->toolbar_btn['save']);

        $logoUrl = $this->getModuleFileUrl('views/img/cr.png');
        $lang = $this->context->language->iso_code;
        if (!file_exists(_PS_MODULE_DIR_ . 'cleverreach' . DS . 'CleverReach-UserManual_' . $lang . '.pdf')) {
            $lang = 'en';
        }

        $userManualUrl = $this->getModuleFileUrl("CleverReach-UserManual_$lang.pdf");

        $redirectUrl = $this->context->link->getModuleLink(
            'cleverreach',
            'auth',
            array(),
            null,
            null,
            Configuration::get('PS_SHOP_DEFAULT')
        );
        $this->context->smarty->assign(
            array(
                'show_page_header_toolbar' => $this->show_page_header_toolbar,
                'page_header_toolbar_title' => $this->page_header_toolbar_title,
                'page_header_toolbar_btn' => $this->page_header_toolbar_btn,
                'cleverreach_isConnected' => $this->isConnected(),
                'cleverreach_logoUrl' => $logoUrl,
                'cleverreach_baseUrl' => $this->context->shop->getBaseUrl(),
                'cleverreach_userManualUrl' => $userManualUrl,
                'cleverreach_adminurl' => $this->context->link->getAdminLink('CleverReachConfig'),
                'cleverreach_importurl' => $this->context->link->getAdminLink('CleverReachImport'),
                'cleverreach_authorize_url' => $this->getAuthorizeUrl($redirectUrl),
                'cleverreach_mapping' => $this->getShopGroups(),
            )
        );

        $this->setTemplate('content.tpl');
    }

    public function createTemplate($tpl_name)
    {
        $path = dirname(__FILE__) . DS . '..' . DS . '..' . DS . 'views' . DS . 'templates' . DS . 'admin' . DS;

        return $this->context->smarty->createTemplate($path . $tpl_name, $this->context->smarty);
    }

    public function checkAccess()
    {
        return true;
    }

    public function viewAccess()
    {
        return true;
    }

    /**
     * Returns if connection to CleverReach status
     *
     * @return bool
     */
    private function isConnected()
    {
        return $this->model->isConnected();
    }

    /**
     * Returns static module file web URL
     *
     * @param string $file
     * @return string
     */
    private function getModuleFileUrl($file)
    {
        return $this->context->shop->getBaseUrl() . 'modules/cleverreach/' . $file;
    }

    /**
     * Return two static groups one for all customers and one for guest subscribers
     *
     * @return array
     * @throws PrestaShopDatabaseException
     */
    private function getShopGroups()
    {
        $result = array();

        $result['1'] = $this->l('PrestaShop customers and subscribers');

        return $result;
    }

    /**
     * Returns authorization URL
     *
     * @param $redirectUrl
     * @return string
     */
    private function getAuthorizeUrl($redirectUrl)
    {
        $address = Configuration::get('PS_SHOP_ADDR1', null, null, $this->context->shop->id);
        if (empty($address)) {
            $address = Configuration::get('PS_SHOP_ADDR2', null, null, $this->context->shop->id);
        }

        $countryId = Configuration::get('PS_SHOP_COUNTRY_ID', null, null, $this->context->shop->id);
        $country = new Country();

        $registrationData = array(
            'email' => $this->context->employee->email,
            'company' => $this->context->shop->name,
            'firstname' => $this->context->employee->firstname,
            'lastname' => $this->context->employee->lastname,
            'gender' => '',
            'street' => $address,
            'zip' => Configuration::get('PS_SHOP_CODE', null, null, $this->context->shop->id),
            'city' => Configuration::get('PS_SHOP_CITY', null, null, $this->context->shop->id),
            'country' => $country->getNameById($this->context->language->id, $countryId),
            'phone' => Configuration::get('PS_SHOP_PHONE', null, null, $this->context->shop->id)
        );

        $registrationData = base64_encode(json_encode($registrationData));

        return $this->apiClient->getLoginUrl($redirectUrl, $registrationData);
    }
}
