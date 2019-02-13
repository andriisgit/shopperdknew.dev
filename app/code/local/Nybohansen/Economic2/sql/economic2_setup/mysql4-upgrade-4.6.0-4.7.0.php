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

define('ECONOMIC_TABLE_NAME5', 'economic2_order_status');

$installer = $this;
$installer->startSetup();

$installer->run("ALTER TABLE {$this->getTable(ECONOMIC_TABLE_NAME5)}
                 ADD `credit_order_id` TEXT NOT NULL ,
                 ADD `credit_invoice_id` TEXT NOT NULL");

$installer->endSetup();

