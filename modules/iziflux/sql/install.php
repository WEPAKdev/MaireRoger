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



$sql = array();

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'order_iziflux` (
              `id_order_iziflux` int(11) NOT NULL AUTO_INCREMENT,
			  `id_order` int(11) NOT NULL,
			  `id_iziflux_order` int(11) NOT NULL,
			  `id_product` int(11) NOT NULL,
			  `reference_product_market` varchar(255) character set utf8 NOT NULL,
			  `transaction` varchar(255) character set utf8 NOT NULL,
			  `market_place` varchar(255) character set utf8 NOT NULL,
			  `carrier_name` varchar(255) character set utf8 NOT NULL,
			  `quantity` int(11) NOT NULL,
			  `status` tinyint(4) NOT NULL,
			  `date_add` datetime NOT NULL,
               PRIMARY KEY(`id_order_iziflux`)
			)';

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'order_iziflux_file` (
			`id` INT NOT NULL AUTO_INCREMENT,
			`file` TEXT NOT NULL ,
			`date_add` DATETIME NOT NULL,
            PRIMARY KEY(`id`)
		)';

$sql[] = 'ALTER TABLE `' . _DB_PREFIX_ .
     'order_iziflux` CHANGE `id_iziflux_order` `id_iziflux_order`
         VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ';

foreach ($sql as $query) {
    if (Db::getInstance()->execute($query) == false) {
        return false;
    }
}
