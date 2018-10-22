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
include_once _PS_MODULE_DIR_ . 'cleverreach/classes/CleverReachUtility.php';

class CleverReachAuthModuleFrontController extends ModuleFrontController
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
        $this->apiClient = new CleverReachApiClient();
        $this->model = new ConfigModel();

        parent::__construct();
    }

    public function initContent()
    {
        $code = Tools::getValue('code');
        if (empty($code)) {
            CleverReachUtility::dieJson(
                array(
                    'status' => false,
                    'message' => $this->module->l('Wrong parameters'),
                )
            );
        }

        $url = $this->context->link->getModuleLink(
            'cleverreach',
            'auth',
            array(),
            null,
            null,
            Configuration::get('PS_SHOP_DEFAULT')
        );
        $result = $this->apiClient->getAccessToken($code, $url);

        if (isset($result['error'])) {
            CleverReachUtility::dieJson(
                array(
                    'status' => $result['error'],
                    'message' => $this->module->l('Unsuccessful connection'),
                )
            );
        }

        $this->model->setAccessToken($result['access_token']);
        $this->model->setConnected(1);

        if (_PS_VERSION_ >= '1.7.0.0') {
            $this->setTemplate('module:cleverreach/views/templates/front/windowClose.tpl');
        } else {
            $this->setTemplate('windowClose.tpl');
        }
    }
}
