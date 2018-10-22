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

if (!defined('_PS_VERSION_')) {
    exit;
}

include_once dirname(__FILE__) . '/classes/ConfigModel.php';
include_once dirname(__FILE__) . '/classes/CleverReachCustomerFormatter.php';
include_once dirname(__FILE__) . '/classes/CleverReachApiClient.php';
include_once dirname(__FILE__) . '/classes/Data.php';
include_once dirname(__FILE__) . '/classes/BackgroundProcess.php';
include_once dirname(__FILE__) . '/classes/DataModel.php';

class CleverReach extends Module
{
    /**
     * @var ConfigModel
     */
    private $model;

    /**
     * @var CleverReachApiClient
     */
    private $apiClient;

    /**
     * @var CleverReachCustomerFormatter
     */
    private $formatter;

    /**
     * @var CleverReachCustomerFormatter
     */
    private $data;

    /**
     * @var BackgroundProcess
     */
    private $backgroundProcessHelper;

    /**
     * @var DataModel
     */
    private $dataModel;

    public function __construct()
    {
        $this->module_key = '70c54cb596a0f9c4428cce1ad60f7d19';
        $this->name = 'cleverreach';
        $this->tab = 'advertising_marketing';
        $this->version = '1.1.0';
        $this->author = 'CleverReach GmbH';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
        $this->bootstrap = true;
        $this->controllers = array('auth', 'import', 'search');

        parent::__construct();

        $this->displayName = $this->l('CleverReach® – THE Email Marketing Solution');
        $this->description = $this->l(
            'CleverReach® is THE Email Marketing Solution for beginners and experienced users alike
            that lets you easily create newsletters online and send them to target customers.'
        );
        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');
        $this->model = new ConfigModel();
        $this->apiClient = new CleverReachApiClient();
        $this->formatter = new CleverReachCustomerFormatter();
        $this->backgroundProcessHelper = new BackgroundProcess();
        $this->dataModel = new DataModel();
        $this->data = new Data();
    }

    /**
     * Handle plugin installation
     *
     * @return bool
     */
    public function install()
    {
        // Install Tabs
        $tab = new Tab();
        // Need a foreach for the language
        $tab->name[(int)Configuration::get('PS_LANG_DEFAULT')] = $this->l('CleverReach');
        $tab->class_name = 'CleverReachAdmin';
        // Set parent tab id
        $parent_id = (_PS_VERSION_ >= '1.7.0.0' ? (int)Tab::getIdFromClassName('CONFIGURE') : 0);
        $tab->id_parent = $parent_id;
        $tab->module = $this->name;
        $tab->add();

        // Set icon image when menu is collapsed
        if (_PS_VERSION_ >= '1.7') {
            $db = Db::getInstance();
            $db->update('tab', array('icon' => 'sms'), 'id_tab = ' . (int)$tab->id);
        }

        include(dirname(__FILE__) . '/sql/install.php');

        return parent::install()
            && $this->createAjaxControllers()
            && $this->registerHook('backOfficeHeader')
            && $this->registerHook('displayFooterProduct')
            && $this->registerHook('actionObjectCustomerAddAfter')
            && $this->registerHook('actionObjectCustomerUpdateAfter')
            && $this->registerHook('actionObjectShopAddAfter')
            && $this->registerHook('actionObjectShopDeleteAfter')
            && $this->registerHook('displayOrderConfirmation')
            && $this->registerHook('displayBlockNewsletterBottom');
    }

    /**
     * Handle plugin uninstall
     *
     * @return bool
     */
    public function uninstall()
    {
        $tab = new Tab((int)Tab::getIdFromClassName('CleverReachAdmin'));
        $tab->delete();

        include(dirname(__FILE__) . '/sql/uninstall.php');

        return parent::uninstall();
    }

    public function hookBackOfficeHeader()
    {
        $this->context->controller->addCSS($this->_path . 'views/css/icon.css');

        if (Tools::getValue('controller') === 'CleverReachAdmin') {
            // add module css, javascript
            $this->context->controller->addJS($this->_path . 'views/js/cleverreach.js');
            $this->context->controller->addCSS($this->_path . 'views/css/module.css');
        }
    }

    /**
     * Hook when new subscriber is registered
     */
    public function hookDisplayBlockNewsletterBottom()
    {
        // if not connected to CleverReach skip execution or not submitNewsletter event
        if (!$this->model->isConnected() || !Tools::isSubmit('submitNewsletter')) {
            return;
        }

        if (Tools::getValue('action') == 0) {
            // subscribe
            $this->data->subscribeUser(Tools::getValue('email'));
        } elseif (Tools::getValue('action') == 1) {
            // unsubscribe
            $this->data->subscribeUser(Tools::getValue('email'), true);
        }
    }

    /**
     * A method that hooks into product page load and sets crmailing
     * value for that particular product if the crmailing value is set
     *
     * @return void
     */
    public function hookDisplayFooterProduct()
    {
        // acquire GET parameter crmailing
        $campaignParameterValue = Tools::getValue('crmailing');

        // do nothing if the parameter doesn't exist
        if (empty($campaignParameterValue)) {
            return;
        }

        // extract product ID
        $productId = (int)Tools::getValue('id_product');

        // read data from cookie if it already exists
        $data = json_decode($this->context->cookie->campaignData, true);
        $data[$productId] = $campaignParameterValue;

        // finally, save json encoded data to cookie
        $this->context->cookie->campaignData = json_encode($data);
    }

    /**
     * Hook for adding new customer
     *
     * @param $params
     */
    public function hookActionObjectCustomerAddAfter($params)
    {
        if ($this->model->isConnected()) {
            $this->data->updateCustomer($params['object'], $this->dataModel, true);
        }
    }

    /**
     * Hook for updating existing customer. This hook will be triggered whenever the customer is updated, also covers
     * subscription changes in block newsletter (version < 1.7.0.0) and newsletter subscription (version >= 1.7.0.0)
     * modules.
     *
     * @param array $params
     */
    public function hookActionObjectCustomerUpdateAfter($params)
    {
        if ($this->model->isConnected()) {
            $this->data->updateCustomer($params['object'], $this->dataModel);
        }
    }

    /**
     * Hook on new shop add event
     *
     * @param array $params
     */
    public function hookActionObjectShopAddAfter($params)
    {
        if ($this->model->isConnected()) {
            $this->startImportProcess($params['object']->id_shop_group);
        }
    }

    /**
     * Hook on shop deleted event
     *
     * @param array $params
     */
    public function hookActionObjectShopDeleteAfter($params)
    {
        if ($this->model->isConnected()) {
            $this->startImportProcess($params['object']->id_shop_group);
        }
    }

    /**
     *  Hook for new order creation
     *
     * @param $params
     */
    public function hookDisplayOrderConfirmation($params)
    {
        if ($this->model->isConnected()) {
            $paramName = _PS_VERSION_ >= '1.7.0.0' ? 'order' : 'objOrder';
            $this->data->sendOrder($params[$paramName], $params['cookie']);
        }
    }

    /**
     * Register additional admin ajax controllers
     *
     * @return bool
     */
    private function createAjaxControllers()
    {
        // add back office config controller
        $tab = new Tab();
        $tab->active = 1;
        $tab->name[(int)Configuration::get('PS_LANG_DEFAULT')] = $this->l('CleverReach');
        $tab->class_name = 'CleverReachConfig';
        $tab->module = $this->name;
        $tab->id_parent = -1;
        $tab->add();

        //add back office import controller
        $tab = new Tab();
        $tab->active = 1;
        $tab->name[(int)Configuration::get('PS_LANG_DEFAULT')] = $this->l('CleverReach');
        $tab->class_name = 'CleverReachImport';
        $tab->module = $this->name;
        $tab->id_parent = -1;
        $tab->add();

        return true;
    }

    /**
     * Start background import process
     *
     * @param $shopGroupId
     */
    private function startImportProcess($shopGroupId)
    {
        $shopIds = $this->dataModel->getShopIdsForGroup($shopGroupId);

        $count = 0;
        // check if guest subscribers needs to be imported
        $count += $this->dataModel->getGuestSubscriberCount($shopIds);
        // check if customers needs to be imported
        $count += $this->dataModel->getCustomerCount($shopIds);

        // set import configurations
        $this->model->setImportLocked(1);
        $this->model->setImportProgress(0);
        $this->model->setTotalCustomersForImport($count);
        $this->model->setImportStartTime(time());

        // start background process
        $this->backgroundProcessHelper->setPassword($this->model->getProductEndpointPassword())
            ->setShopGroupId($shopGroupId)
            ->setUrl($this->context->link->getModuleLink('cleverreach', 'import'))
            ->setOffset(0)
            ->setLimit($this->model->getBatchSize())
            ->setGroupId(1)
            ->startBackgroundProcess();
    }
}
