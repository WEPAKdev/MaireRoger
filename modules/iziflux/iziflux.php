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

class Iziflux extends Module
{
    public function __construct()
    {
        $this->name = 'iziflux';
        $this->tab = 'market_place';
        $this->version = '2.0.18';
        $this->author = 'Profileo';
        $this->need_instance = 0;
        $this->module_key = 'f3bccfbce1d341abea9b2a9965e18ad1';
        $this->ps_versions_compliancy = array('min' => '1.5', 'max' => _PS_VERSION_);
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Iziflux');
        $this->description = $this->l('Export your products to more than 500 shopping engines and marketplaces');

        $this->confirmUninstall = $this->l('Are you sure you want to remove this module?');
    }

    public function install()
    {
        include(dirname(__FILE__) . '/sql/install.php');

        return parent::install()
            && $this->registerHook('backOfficeHeader')
            && $this->registerHook('displayTop')
            && $this->registerHook('displayFooter');
    }

    public function uninstall()
    {
        return parent::uninstall();
    }

    /**
     * Function getContent()
     *
     * Load the configuration form
     */
    public function getContent()
    {
        // If values have been submitted in the form, process.
        if (Tools::isSubmit('submitBlockSettings')) {
            $this->postProcess();
        }

        return $this->renderForm();
    }

    /**
     * Function renderForm()
     *
     * Create the form that will be displayed in the configuration of your module.
     */
    protected function renderForm()
    {
        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submit' . Tools::ucfirst($this->name);
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false) .
            '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFormValues(), /* Add values for your inputs */
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id
        );

        $fields_form = array();

        $img_path = Tools::getShopDomain(true) . __PS_BASE_URI__ . 'modules/' . $this->name . '/views/img/iziflux.jpg';

        $this->context->smarty->assign('img_path', $img_path);
        $about_description =
            $this->context->smarty->fetch($this->local_path . 'views/templates/admin/description-info.tpl');

        $fields_form[0]['form'] = array(
            'legend' => array(
                'title' => $this->l('About'),
                'icon' => 'icon-cogs'
            ),
            'input' => array(
                array(
                    'type' => 'free',
                    'name' => 'about_help',
                    'desc' => $about_description,
                    'form_group_class' => 'fixed-width-izi',
                    'class' => 'description'
                )
            )
        );

        $numberOfOrders = Db::getInstance()->getRow('SELECT count(*) as totalOrders
            FROM `'. _DB_PREFIX_ .'order_iziflux`');

        $iziflux_activate_url = 'http://www.iziflux.com/activation-iziflux-prestashop';
        $activateButtonHtml = '<p><a class="edit btn btn-default" href="'.$iziflux_activate_url.'" target="_blank">';
        $activateButtonHtml .= '<i class="icon-user left"></i>&nbsp; '.$this->l('Activate').'</a></p>';
        if ($numberOfOrders['totalOrders'] == 0) {
            $fields_form[1]['form'] = array(
                'legend' => array(
                    'title' => $this->l('Activate your Iziflux License'),
                    'icon' => 'icon-cogs'
                ),
                'description' => '',
                'input' => array(
                    array(
                        'type' => 'free',
                        'name' => 'activate_help',
                        'desc' => $activateButtonHtml,
                        'form_group_class' => ''
                    )
                ),
            );
        }

        $fields_form[2]['form'] = array(
            'legend' => array(
                'title' => $this->l('Settings'),
                'icon' => 'icon-cogs'
            ),
            'input' => array(
                array(
                    'col' => 2,
                    'type' => 'text',
                    'desc' => $this->l('Ex : 01,02,23,86, [0-9]{2}'),
                    'name' => 'servername',
                    'label' => $this->l('Iziflux server name for statistics :')
                ),
                array(
                    'col' => 2,
                    'type' => 'text',
                    'desc' => $this->l('Account Name: abed, [ az ]'),
                    'name' => 'account',
                    'label' => $this->l('Iziflux account :')
                ),
                array(
                    'col' => 2,
                    'type' => 'text',
                    'name' => 'delay_replenishing',
                    'label' => $this->l('Replenishment times in days')
                ),
                array(
                    'col' => 2,
                    'type' => 'text',
                    'name' => 'delay_delivery',
                    'label' => $this->l('Standard delivery time')
                ),
                array(
                    'col' => 2,
                    'type' => 'select',
                    'name' => 'delay_unit',
                    'label' => $this->l('Unit delivery'),
                    'options' => array(
                        'query' => array(
                            array(
                                'id' => 'H',
                                'name' => 'Heures'
                            ),
                            array(
                                'id' => 'D',
                                'name' => 'Jours'
                            ),
                            array(
                                'id' => 'W',
                                'name' => 'Semaines'
                            ),
                            array(
                                'id' => 'M',
                                'name' => 'Mois'
                            )
                        ),
                        'id' => 'id',
                        'name' => 'name'
                    )
                ),
                array(
                    'col' => 2,
                    'type' => 'select',
                    'name' => 'default_carrier',
                    'label' => $this->l('Default carrier'),
                    'options' => array(
                        'query' => $this->getAllPrestashopCarriers(),
                        'id' => 'id',
                        'name' => 'name'
                    )
                ),
                array(
                    'col' => 2,
                    'type' => 'select',
                    'name' => 'status_order[]',
                    'multiple' => true,
                    'label' => $this->l('Order state to send to Iziflux'),
                    'options' => array(
                        'query' => $this->getAllPrestashopActiveStates(),
                        'id' => 'id',
                        'name' => 'name'
                    ),
                    'size' => 15,
                    'class' => 'fixed-width-xxl'
                ),
                array(
                    'col' => 2,
                    'type' => 'text',
                    'name' => 'url',
                    'label' => $this->l('Order URL Integration'),
                    'desc' => $this->l('Url for batch files Ex : Iziflux -07, Iziflux -08, ...')
                )
            ),
            'submit' => array(
                'title' => $this->l('Save'),
                'name' => 'submitBlockSettings'
            )
        );

        $iziflux_desc = $this->l('Please forward this url to iziflux.com');
        $iziflux_url = '';

        $fields_form[3]['form'] = array(
            'legend' => array(
                'title' => $this->l('Iziflux Url'),
                'icon' => 'icon-cogs'
            ),
            'description' => '<a name="cronjobs_urls"></a>' . $iziflux_desc,
            'input' => array(
                array(
                    'type' => 'free',
                    'name' => 'cron_help',
                    'desc' => '
									<p>' . $iziflux_url . '</p>'
                )
            )
        );

        return $helper->generateForm($fields_form);
    }

    /**
     * Function getAllPrestashopCarriers
     *
     * Load all active carriers
     */
    protected function getAllPrestashopCarriers()
    {

        // Get all active carriers
        $carriers_info = Carrier::getCarriers($this->context->language->id, true, false);

        // Initialise $order_state_available array
        $carriers_info_available = array();

        foreach ($carriers_info as $carrier) {
            $carriers_info_available[] = array(
                'id' => $carrier['id_carrier'],
                'name' => $carrier['name']
            );
        }

        return $carriers_info_available;
    }

    /**
     * Function getAllPrestashopActiveStates
     *
     * Load all order states
     */
    protected function getAllPrestashopActiveStates()
    {
        // Get all active states.
        $order_states = OrderState::getOrderStates($this->context->language->id);

        // Initialise $order_state_available array
        $order_state_available = array();

        foreach ($order_states as $order_state) {
            $order_state_available[] = array(
                'id' => $order_state['id_order_state'],
                'name' => $order_state['name']
            );
        }

        return $order_state_available;
    }

    /**
     * Function postProcess()
     *
     * Save form data.
     */
    protected function postProcess()
    {
        $servername = Tools::getValue('servername');
        $account = Tools::getValue('account');
        Configuration::updateValue('IZIFLUX_SERVERNAME', $servername);
        Configuration::updateValue('IZIFLUX_ACCOUNT', $account);
        Configuration::updateValue('IZIFLUX_ORDER_STATE', implode(',', Tools::getValue('status_order')));
        Configuration::updateValue('IZIFLUX_DELAY_UNIT', Tools::getValue('delay_unit'));
        Configuration::updateValue('IZIFLUX_DELAY_DELIVERY', Tools::getValue('delay_delivery'));
        Configuration::updateValue('IZIFLUX_DELAY_REPLENISHING', Tools::getValue('delay_replenishing'));
        Configuration::updateValue('IZIFLUX_DEFAULT_CARRIER', Tools::getValue('default_carrier'));
        Configuration::updateValue('IZIFLUX_ORDER_INTEGRATION_URL', Tools::getValue('url'));
    }

    public function hookDisplayTop($params)
    {
        // if (Tools::getValue('step') == 3 && Tools::getIsset($params['cart'])
        // && Configuration::get('IZIFLUX_SERVERNAME') && Configuration::get('IZIFLUX_ACCOUNT')) {
        if (preg_match("/payment|systempay|confirmation/i", $_SERVER['REQUEST_URI']) && isset($params['cart'])
            && Configuration::get('IZIFLUX_SERVERNAME') && Configuration::get('IZIFLUX_ACCOUNT')) {
            $currency = Currency::getCurrency($params['cart']->id_currency);
            $products = $params['cart']->getProducts();
            $orderStr = array();
            $cmdId = $params['cart']->id;
            $amountWT = number_format(
                Tools::convertPrice(
                    $params['cart']->getOrderTotal(true, 3),
                    $currency
                ),
                2,
                '.',
                ''
            );
            $amountWOT =
                number_format(Tools::convertPrice($params['cart']->getOrderTotal(false, 3), $currency), 2, '.', '');
            $port = number_format(Tools::convertPrice($params['cart']->getOrderShippingCost(), $currency), 2, '.', '');
            $isNewClient = (int) (Order::getCustomerNbOrders($params['cart']->id_customer) == 0);
            $productCount = count($products);

            $orderStr[] = 'client=' . Configuration::get('IZIFLUX_ACCOUNT');
            $orderStr[] = '&idCommande=' . $cmdId;
            $orderStr[] = '&montantComTtc=' . $amountWT;
            $orderStr[] = '&montantComHt=' . $amountWOT;
            $orderStr[] = '&fraisDePort=' . $port;
            $orderStr[] = '&nbCom=1';
            $orderStr[] = '&nbContact=' . $isNewClient;
            $orderStr[] = '&nbArticle=' . $productCount;
            $counter = 1;
            foreach ($products as $product) {
                $productReference = $product['reference'];
                $productName = $product['name'];
                if (key_exists('attributes', $product)
                    && $product['attributes'] != null
                    && trim($product['attributes'] != '')) {
                    $productName .= ' ' + $product['attributes'];
                }
                $productCategory = $product['category'];
                $productPrice = number_format(Tools::convertPrice($product['price_wt'], $currency), 2, '.', '');
                $productQuantity = $product['cart_quantity'];
                $orderStr[] = '&ref-' . $counter . '=' . urlencode($productReference);
                $orderStr[] = '&nom-' . $counter . '=' . urlencode($productName);
                $orderStr[] = '&gamme-' . $counter . '=' . urlencode($productCategory);
                $orderStr[] = '&prix-' . $counter . '=' . $productPrice;
                $orderStr[] = '&nb-' . $counter . '=' . $productQuantity;
                $counter ++;
            }
            $html = (Configuration::get('PS_SSL_ENABLED') ?
                    'https://' : 'http://') . 'www.stat' . Configuration::get('IZIFLUX_SERVERNAME') .
                    '-iziflux.com/statCbIx/1/img...' . implode("", $orderStr);
            $html = '<span style="display:none;"><img src="' . $html . '" width="1" height="1"></span>';
            return $html;
        }
    }

    public function hookDisplayFooter()
    {
        return $this->display(__FILE__, 'views/templates/hook/front/google.tpl');
    }

    public function hookBackOfficeHeader()
    {
        // Add the CSS & JavaScript files you want to be loaded in the BO.
        if (Tools::getValue('module_name') == $this->name) {
            $this->context->controller->addJS($this->_path . 'views/js/back.js');
        }
    }

    public function getConfigFormValues()
    {
        $data = array();

        $data['servername'] = configuration::get('IZIFLUX_SERVERNAME');
        $data['account'] = configuration::get('IZIFLUX_ACCOUNT');
        $data['status_order[]'] = explode(',', configuration::get('IZIFLUX_ORDER_STATE'));
        $data['delay_unit'] = configuration::get('IZIFLUX_DELAY_UNIT');
        $data['delay_delivery'] = configuration::get('IZIFLUX_DELAY_DELIVERY');
        $data['delay_replenishing'] = configuration::get('IZIFLUX_DELAY_REPLENISHING');
        $data['default_carrier'] = configuration::get('IZIFLUX_DEFAULT_CARRIER');
        $data['url'] = configuration::get('IZIFLUX_ORDER_INTEGRATION_URL');

        $data['site_name'] = configuration::get('PS_SHOP_NAME');

        $data['about_help'] = '';
        $data['cron_help'] = '';
        $data['activate_help'] = '';

        return $data;
    }
}
