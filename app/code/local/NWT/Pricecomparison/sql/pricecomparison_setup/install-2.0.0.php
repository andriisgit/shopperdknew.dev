<?php 
/**
 * NWT_Pricecomparison extension
 *
 * @category    NWT
 * @package     NWT_Pricecomparison
 * @copyright   Copyright (c) 2014 Nordic Web Team ( http://nordicwebteam.se/ )
 * @license     NWT Commercial License (NWTCL 1.0)
 * @author      Emil [carco] Sirbu (emil.sirbu@gmail.com)
 *
 */

/**
 * Pricecomparison module update script
 *
 */


$this->startSetup();
$this->run("
CREATE TABLE IF NOT EXISTS `{$this->getTable('pricecomparison/feed')}` (
  `entity_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Feed ID',
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT 'Feed Name',
  `store_id` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT 'Store Id',
  `feed_type` varchar(32) DEFAULT NULL COMMENT 'Feed Type (used for first set of attributes)',
  `status` tinyint(1) DEFAULT '1' COMMENT 'Status (1 - Autogenerate)',
  `filename` varchar(255) NOT NULL DEFAULT '' COMMENT 'Output feed file name',
  `attributes` text COMMENT 'Feed fields (serialized)',
  `list_outofstock` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT 'Products selection - List Out ot Stock',
  `list_disabled` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT 'Products selection - List Disabled Products',
  `list_visibility` varchar(255) NOT NULL DEFAULT '2,4' COMMENT 'Products selection - visibility',
  `list_types` varchar(255) NOT NULL DEFAULT 'simple' COMMENT 'Products selection - product types',
  `csv_header` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT 'CSV - Add columns name',
  `csv_enclosure` tinyint(3) unsigned NOT NULL DEFAULT '34' COMMENT 'CSV - Enclosure (ord)',
  `csv_separator` tinyint(3) unsigned NOT NULL DEFAULT '59' COMMENT 'CSV  - Separator (ord)',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `start` datetime DEFAULT NULL COMMENT 'Generate - started at',
  `end` datetime DEFAULT NULL COMMENT 'Generate - end at',
  `errors` tinyint(1) unsigned DEFAULT NULL COMMENT 'Generate - was errors',
  `errors_msg` varchar(255) DEFAULT NULL COMMENT 'Generate - error message',
  `log` text COMMENT 'Generate - log',
   PRIMARY KEY (`entity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");


$setup = Mage::getResourceModel('catalog/setup', 'catalog_setup');

$setup->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'pc_delivery_time', array(
    'label' => 'Delivery Time',
    'type' => 'text',
    'required' => 0
));

$setup->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'pc_delivery_cost', array(
    'label' => 'Delivery Cost',
    'type' => 'text',
    'required' => 0
));

$setup->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'pc_manufacturer_sku', array(
    'label' => 'Manufacturer SKU',
    'type' => 'text',
    'required' => 0
));

$setup->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'pc_eanorupc_prod', array(
    'label' => 'EAN or UPC',
    'type' => 'text',
    'required' => 0
));

$setup->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'pc_isbn_prod', array(
    'label' => 'ISBN number',
    'type' => 'text',
    'required' => 0
));

$setup->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'pc_reitaler_message', array(
    'label' => 'Retailer Message',
    'input'         => 'textarea',
    'type'          => 'text',
    'note'=>'The text inside this "Retailer Message" must have maximum length of 125 characters ! If the text is longer this will be shortened to 125 characters.',
    'required' => 0
));

//add attirbutes to Ship 2 Anywhere attribute group to each set
$sets = $setup->getAllAttributeSetIds(Mage_Catalog_Model_Product::ENTITY);
$attrs = array(
    $setup->getAttributeId('catalog_product', 'pc_delivery_time'),
    $setup->getAttributeId('catalog_product', 'pc_delivery_cost'),
    $setup->getAttributeId('catalog_product', 'pc_manufacturer_sku'),
    $setup->getAttributeId('catalog_product', 'pc_eanorupc_prod'),
    $setup->getAttributeId('catalog_product', 'pc_isbn_prod'),
    $setup->getAttributeId('catalog_product', 'pc_reitaler_message')
);

foreach ($sets as $_setId) {
    $setup->addAttributeGroup(Mage_Catalog_Model_Product::ENTITY, $_setId, 'Price comparison', 70);
    foreach ($attrs as $_attr) {
        $setup->addAttributeToSet(
            'catalog_product',
            $_setId,
            $setup->getAttributeGroupId(Mage_Catalog_Model_Product::ENTITY, $_setId, 'Price comparison'),
            $_attr
        );
    }
}

$this->endSetup();
