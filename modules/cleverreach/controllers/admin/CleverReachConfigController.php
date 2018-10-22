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

class CleverReachConfigController extends AdminController
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
        $this->model = new ConfigModel();
        $this->apiClient = new CleverReachApiClient();

        parent::__construct();
    }

    /**
     * Initialize content
     */
    public function initContent()
    {
        parent::initContent();
    }

    /**
     * Get configuration action
     *
     */
    public function displayAjaxGetConfig()
    {
        $groups = array();
        $errorMessage = '';

        if ($this->model->isConnected()) {
            try {
                $groups = $this->apiClient->getFormattedGroups($this->model->getAccessToken());
            } catch (\Exception $e) {
                Logger::addLog(
                    'Authorize to CleverReach problem for client ' . $this->clientId . ' with message ' .
                    $e->getMessage()
                );
                $errorMessage = $this->l('Error') . ' ' . $e->getMessage();
            }
        }

        $data = $this->model->getAllConfigs($groups);

        if (!empty($errorMessage)) {
            $data['error_message'] = $errorMessage;
        }

        CleverReachUtility::dieJson($data);
    }

    /**
     * Save configurations action
     */
    public function displayAjaxSaveConfig()
    {
        $batchSize = Tools::getValue('batchSize');
        $productSearch = Tools::getValue('productSearch');
        $debugMode = Tools::getValue('debugMode');
        $groupMappings = Tools::getValue('groupMappings');

        if (!is_numeric($batchSize) || $batchSize < 50 || $batchSize > 250) {
            CleverReachUtility::dieJson(
                array(
                    'status' => ConfigModel::INCORRECT_BATCH,
                    'message' => $this->l('Batch size must be between 50 and 250'),
                )
            );
        }

        // if product search is enabled, register endpoint on CleverReach
        if ($productSearch) {
            $password = $this->model->getProductEndpointPassword();

            if (!$password) {
                $password = md5(time());
                $this->model->setProductEndpointPassword($password);
            }

            $this->apiClient->registerEndpoint(
                $this->model->getAccessToken(),
                $this->context->link->getModuleLink('cleverreach', 'search'),
                'PrestaShop (' . $this->context->shop->name . ') - Product search',
                $password
            );
        }

        $groupMapping = json_decode($groupMappings, true);

        if ($groupMapping[1]['optInForm'] != 0) {
            if (_PS_VERSION_ >= '1.7.0.0' && Module::isEnabled('ps_emailsubscription')) {
                if (Module::isEnabled('ps_emailsubscription')) {
                    Configuration::updateValue('NW_VERIFICATION_EMAIL', '0');
                }
            } else {
                Configuration::updateValue('PS_CUSTOMER_OPTIN', '0');
            }
        }

        $this->model->setBatchSize($batchSize);
        $this->model->setDebugMode($debugMode);
        $this->model->setProductSearch($productSearch);
        $this->model->setGroupMappings($groupMappings);

        CleverReachUtility::dieJson(
            array(
                'status' => ConfigModel::CONFIGURATION_SET,
                'message' => $this->l('Configuration saved successfully'),
            )
        );
    }

    /**
     * Check configuration action
     */
    public function displayAjaxCheckConfig()
    {
        $connected = $this->model->isConnected();
        $accessToken = $this->model->getAccessToken();

        if (empty($connected)) {
            CleverReachUtility::dieJson(
                array(
                    'status' => 0,
                    'message' => $this->l('Unsuccessful connection'),
                )
            );
        }

        try {
            $groups = $this->apiClient->getFormattedGroups($accessToken);
        } catch (\Exception $e) {
            // error occurred, return appropriate status
            CleverReachUtility::dieJson(
                array(
                    'status' => ConfigModel::UNSUCCESSFUL_CONNECTION,
                    'message' => $e->getMessage(),
                )
            );
        }

        CleverReachUtility::dieJson(
            array(
                'status' => ConfigModel::SUCCESSFUL_CONNECTION,
                'message' => $this->l('Successful connection'),
                'groups' => $groups,
            )
        );
    }

    /**
     * Reset configurations action
     *
     */
    public function displayAjaxResetConfig()
    {
        $this->model->resetConfiguration();

        CleverReachUtility::dieJson(array('status' => true));
    }
}
