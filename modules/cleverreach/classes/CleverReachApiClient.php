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

class CleverReachApiClient
{

    const CLIENT_ID = 'tP6E0EIiTz';
    const CLIENT_SECRET = 'uUgmTSWaFhxhECNX7leVbIKimKX52REx';
    const LOGIN_URL = 'https://rest.cleverreach.com/oauth/authorize.php';
    /**
     * @var string
     */
    private $url = 'https://rest.cleverreach.com/v2';

    /**
     * @var string
     */
    private $postFormat = 'json';

    /**
     * @var string
     */
    private $returnFormat = 'json';

    /**
     * @var bool
     */
    private $authMode = false;

    /**
     * @var bool|\stdClass
     */
    private $authModeSettings = false;

    /**
     * @var bool|\stdClass
     */
    private $debugValues = false;

    /**
     * @var bool
     */
    private $checkHeader = true;

    /**
     * @var bool
     */
    private $throwExceptions = true;

    /**
     * @var bool
     */
    private $header = false;

    /**
     * @var bool
     */
    private $error = false;

    /**
     * @var array
     */
    private $curlHeaders;

    /**
     * @var int
     */
    public $responseCode;

    /**
     * @var string
     */
    private $tokenUrl = 'https://rest.cleverreach.com/oauth/token.php';

    /**
     * CleverReachApi constructor.
     *
     * @param string $token
     * @param string $mode
     * @param string $url
     */
    public function __construct($token = '', $mode = 'bearer', $url = 'https://rest.cleverreach.com/v2')
    {
        $this->url = rtrim($url, '/');
        $this->authModeSettings = new \stdClass;
        $this->debugValues = new \stdClass;
        $this->curlHeaders = array();
        $this->responseCode = 0;
        $this->setAuthMode($mode, $token);
    }

    /**
     * Set Auth Mode
     *
     * @param string $mode
     * @param mixed $value
     */
    public function setAuthMode($mode = 'none', $value = false)
    {
        switch ($mode) {
            case 'jwt':
                $this->authMode = 'jwt';
                $this->authModeSettings->token = $value;
                break;
            case 'bearer':
                $this->authMode = 'bearer';
                $this->authModeSettings->token = $value;
                break;
            case 'webauth':
                $this->authMode = 'webauth';
                $this->authModeSettings->login = $value->login;
                $this->authModeSettings->password = $value->password;
                break;
        }
    }

    /**
     * Delete
     *
     * @param $path
     * @param bool $data
     *
     * @return mixed|null
     * @throws \Exception
     */
    public function delete($path, $data = false)
    {
        return $this->get($path, $data, 'delete');
    }

    /**
     * Get
     *
     * @param $path
     * @param bool $data
     * @param string $mode
     *
     * @return mixed|null
     * @throws \Exception
     */
    public function get($path, $data = false, $mode = 'get')
    {
        $this->resetDebug();
        if (is_string($data) && !json_decode($data)) {
            throw new \Exception('Data is string but no JSON');
        }

        $url = sprintf("%s?%s", $this->url . $path, ($data ? http_build_query($data) : ''));
        $this->debug('url', $url);

        $curl = curl_init($url);
        $this->setupCurl($curl);

        switch ($mode) {
            case 'delete':
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, Tools::strtoupper($mode));
                $this->debug('mode', Tools::strtoupper($mode));
                break;

            default:
                $this->debug('mode', 'GET');
                break;
        }

        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $curl_response = curl_exec($curl);
        $headers = curl_getinfo($curl);
        curl_close($curl);

        $this->debugEndTimer();

        return $this->returnResult($curl_response, $headers);
    }

    /**
     * Micro time float
     *
     * @return float
     */
    public function microTimeFloat()
    {
        list($usec, $sec) = explode(' ', microtime());

        return ((float)$usec + (float)$sec);
    }

    /**
     * Put
     *
     * @param $path
     * @param bool $data
     *
     * @return mixed|null
     * @throws \Exception
     */
    public function put($path, $data = false)
    {
        return $this->post($path, $data, 'put');
    }

    /**
     * Post
     *
     * @param $path
     * @param $data
     * @param string $mode
     *
     * @return mixed|null
     * @throws \Exception
     */
    public function post($path, $data = array(), $mode = 'post')
    {
        $this->resetDebug();
        $this->debug("url", $this->url . $path);

        if (is_string($data) && !json_decode($data)) {
            throw new \Exception('Data is string but no JSON');
        }

        $curlPostData = ($this->postFormat == 'json' ? json_encode($data) : $data);
        $curl = curl_init($this->url . $path);
        $this->setupCurl($curl);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        switch ($mode) {
            case 'put':
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PUT');
                break;

            default:
                curl_setopt($curl, CURLOPT_POST, true);
                break;
        }

        $this->debug('mode', Tools::strtoupper($mode));

        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $curlPostData);
        $response = curl_exec($curl);
        $headers = curl_getinfo($curl);
        curl_close($curl);
        $this->debugEndTimer();

        return $this->returnResult($response, $headers);
    }

    /**
     * Returns an associative array with fields: access_token, expires_in, token_type, scope.
     * If curl cannot be executed, exception is thrown with appropriate message
     *
     * @param string $code
     * @param string $redirectUrl
     * @return mixed
     * @throws \Exception
     */
    public function getAccessToken($code, $redirectUrl)
    {
        if (function_exists('curl_init') == false) {
            header('HTTP/1.1 500', true, 500);
            throw new \Exception('Cannot initialize cUrl session. Is cUrl enabled for your PHP installation?');
        }

        // Assemble POST parameters for the request.
        $post_fields = '&grant_type=authorization_code&client_id=' . self::CLIENT_ID . '&client_secret='
            . self::CLIENT_SECRET . '&code=' . $code . '&redirect_uri=' . urlencode($redirectUrl);
        $cURL = curl_init($this->tokenUrl);
        curl_setopt($cURL, CURLOPT_POST, true);
        curl_setopt($cURL, CURLOPT_ENCODING, 1);
        curl_setopt($cURL, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($cURL, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($cURL, CURLOPT_POSTFIELDS, $post_fields);

        $response = curl_exec($cURL);
        if ($response == false) {
            throw new \Exception('curl_exec() failed. Error: ' . curl_error($cURL));
        }

        return json_decode($response, true);
    }

    /**
     * Returns login URL
     *
     * @param string $redirectUrl
     * @param string $registrationData base64 encoded array of user data for registration
     * @return string
     */
    public function getLoginUrl($redirectUrl, $registrationData)
    {
        return self::LOGIN_URL . '?response_type=code&client_id=' . self::CLIENT_ID . '&redirect_uri=' .
            urlencode($redirectUrl) . '&registration_data=' . $registrationData;
    }

    /**
     * @return boolean
     */
    public function isThrowExceptions()
    {
        return $this->throwExceptions;
    }

    /**
     * @param boolean $throwExceptions
     * @return $this
     */
    public function setThrowExceptions($throwExceptions)
    {
        $this->throwExceptions = $throwExceptions;

        return $this;
    }

    /**
     * Returns customer groups formatted for configuration page
     *
     * @param $accessToken
     * @return array
     * @throws \Exception
     */
    public function getFormattedGroups($accessToken)
    {
        $formattedGroups = array();
        $this->setAuthMode('bearer', $accessToken);

        $groups = $this->get('/groups.json');
        foreach ($groups as $group) {
            $formattedGroups[] = array(
                'id' => $group['id'],
                'name' => $group['name'],
                'forms' => $this->get('/groups.json/' . $group['id'] . '/forms'),
            );
        }

        return $formattedGroups;
    }

    /**
     * Registers product endpoint on CleverReach
     *
     * @param $accessToken
     * @param $url
     * @param $name
     * @param $password
     */
    public function registerEndpoint($accessToken, $url, $name, $password)
    {
        $this->setAuthMode('bearer', $accessToken);

        $this->setThrowExceptions(false);
        $this->post(
            '/mycontent',
            array(
                'name' => $name,
                'url' => $url,
                'password' => $password,
            )
        );

        $this->setThrowExceptions(true);
    }

    /**
     *  reset Debug
     */
    private function resetDebug()
    {
        $this->debugValues = new \stdClass;
        $this->error = false;
        $this->debugStartTimer();
    }

    /**
     * debug Start Timer
     */
    private function debugStartTimer()
    {
        $this->debugValues->time = $this->microTimeFloat();
    }

    /**
     * debug
     * @param $key
     * @param $value
     */
    private function debug($key, $value)
    {
        $this->debugValues->$key = $value;
    }

    /**
     * setup Curl
     * @param $curl
     */
    private function setupCurl(&$curl)
    {

        $header = array();

        switch ($this->postFormat) {
            case 'json':
                $header['content'] = 'Content-Type: application/json';
                break;
            default:
                $header['content'] = 'Content-Type: application/x-www-form-urlencoded; charset=UTF-8';
                break;
        }

        switch ($this->authMode) {
            case 'webauth':
                curl_setopt(
                    $curl,
                    CURLOPT_USERPWD,
                    $this->authModeSettings->login . ":" . $this->authModeSettings->password
                );
                break;
            case 'jwt':
                $header['token'] = 'X-ACCESS-TOKEN: ' . $this->authModeSettings->token;
                // $header['token'] = 'Authorization: Bearer ' . $this->authModeSettings->token;
                break;
            case 'bearer':
                $header['token'] = 'Authorization: Bearer ' . $this->authModeSettings->token;
                break;
        }

        $this->debugValues->header = $header;
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
    }

    /**
     * Debug End Timer
     */
    private function debugEndTimer()
    {
        $this->debugValues->time = $this->microTimeFloat() - $this->debugValues->time;
    }

    /**
     * Returns Result
     *
     * @param $in
     * @param bool $header
     *
     * @return mixed|null
     * @throws \Exception
     */
    private function returnResult($in, $header = false)
    {
        $this->header = $header;

        if ($this->checkHeader && isset($header['http_code'])) {
            if ($header['http_code'] < 200 || $header['http_code'] >= 300) {
                //error!?
                $this->error = $in;
                $message = var_export($in, true);
                $tmp = json_decode($in, true);

                if ($tmp && isset($tmp['error']['message'])) {
                    $message = $tmp['error']['message'];
                }

                if ($this->throwExceptions) {
                    throw new \Exception('' . $header['http_code'] . ';' . $message, $header['http_code']);
                }

                $in = null;
            }
        }

        switch ($this->returnFormat) {
            case 'json':
                return json_decode($in, true);
            default:
                return $in;
        }
    }
}
