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

class BackgroundProcess
{

    /**
     * Indicates that import is already started
     */
    const IMPORT_LOCKED = 0;

    /**
     * Indicates that there is no customers that need to be imported
     */
    const NOTHING_TO_IMPORT = 1;

    /**
     * Indicates that import was started
     */
    const IMPORT_STARTED = 2;

    /**
     * Indicates that has been some error in import process
     */
    const IMPORT_ERROR = 10;
    /**
     * Front-end controller URL that need to be executed via CURL
     *
     * @var string
     */
    private $url;

    /**
     * Protection from front-end execution without permissions
     *
     * @var string
     */
    private $password;

    /**
     * Total count of customers that needs to be imported
     *
     * @var int
     */
    private $count;

    /**
     * Current number of proceeded customers / subscribers
     *
     * @var int
     */
    private $current;

    /**
     * Indicates whether to continue previous process or to try to start from the beginning
     *
     * @var bool
     */
    private $continue = false;

    /**
     * Indicates start number of current import
     *
     * @var int
     */
    private $offset = 0;

    /**
     * Indicates batch size of import
     *
     * @var int
     */
    private $limit = 100;

    /**
     * Indicates which customer group needs to import
     *
     * @var int
     */
    private $groupId;

    /**
     * Indicates which customer group needs to import
     *
     * @var int
     */
    private $shopGroupId;

    /**
     * Sets background process or continues previous one
     *
     * @return boolean Return true if import process is successfully started, otherwise false
     */
    public function startBackgroundProcess()
    {
        if (is_null($this->password) || is_null($this->url)) {
            return false;
        }

        // send the encrypted pass
        $password = md5($this->password);
        $params = array(
            'password' => $password,
            'continue' => $this->continue,
            'startFrom' => $this->offset,
            'limit' => $this->limit,
            'groupId' => $this->groupId,
            'shopGroupId' => $this->shopGroupId,
        );

        $url = $this->url . '?' . http_build_query($params);

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 1);

        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }

    /**
     * Returns current state in percentage
     *
     * @return bool|int
     */
    public function getCurrentState()
    {
        if (is_null($this->current) || is_null($this->count)) {
            return false;
        }

        if ($this->count === 0) {
            return 100;
        }

        return (int)(($this->current / $this->count) * 100);
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param string $url
     *
     * @return $this
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param string $password
     *
     * @return $this
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @return int
     */
    public function getCount()
    {
        return $this->count;
    }

    /**
     * @param int $count
     *
     * @return $this
     */
    public function setCount($count)
    {
        $this->count = $count;

        return $this;
    }

    /**
     * @return int
     */
    public function getCurrent()
    {
        return $this->current;
    }

    /**
     * @param int $current
     *
     * @return $this
     */
    public function setCurrent($current)
    {
        $this->current = $current;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isContinue()
    {
        return $this->continue;
    }

    /**
     * @param boolean $continue
     *
     * @return $this
     */
    public function setContinue($continue)
    {
        $this->continue = $continue;

        return $this;
    }

    /**
     * @return int
     */
    public function getOffset()
    {
        return $this->offset;
    }

    /**
     * @param int $offset
     *
     * @return $this
     */
    public function setOffset($offset)
    {
        $this->offset = $offset;

        return $this;
    }

    /**
     * @return int
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * @param int $limit
     *
     * @return $this
     */
    public function setLimit($limit)
    {
        $this->limit = $limit;

        return $this;
    }

    /**
     * @return int
     */
    public function getGroupId()
    {
        return $this->groupId;
    }

    /**
     * @param int $groupId
     *
     * @return $this
     */
    public function setGroupId($groupId)
    {
        $this->groupId = $groupId;

        return $this;
    }

    /**
     * @return int
     */
    public function getShopGroupId()
    {
        return $this->shopGroupId;
    }

    /**
     * @param int $shopGroupId
     *
     * @return $this
     */
    public function setShopGroupId($shopGroupId)
    {
        $this->shopGroupId = $shopGroupId;

        return $this;
    }
}
