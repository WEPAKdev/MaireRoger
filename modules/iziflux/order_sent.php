<?php
/**
 *  2016 Profileo
 *
 *  @author    Profileo <contact@profileo.com>
 *  @copyright 2016 Profileo
 *  @license   Profileo
 *  @link  http://www.profileo.com
 */

include(dirname(__FILE__) . '/../../config/config.inc.php');
include(dirname(__FILE__) . '/../../init.php');

// Load utility classes
include_once(dirname(__FILE__) . '/utils/IzifluxTools.php');
include_once(dirname(__FILE__) . '/utils/utilsOrderSent.php');

// load static load()
IzifluxTools::load();

// Set permissions
IzifluxTools::chmodRecursive(_PS_ROOT_DIR_ . '/modules/iziflux/');

OrderSent::ExecuteOrderSent();
