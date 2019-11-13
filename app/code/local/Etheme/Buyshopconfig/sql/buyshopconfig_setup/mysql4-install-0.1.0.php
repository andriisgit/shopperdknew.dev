<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Alesioo
 * Date: 12.12.12
 * Time: 16:24
 * To change this template use File | Settings | File Templates.
 */ 
/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
$installer->startSetup();

/**
 * Adding Different Attributes
 */

// adding attribute group
$setup->addAttributeGroup('catalog_product', 'Default', 'Video', 1000);

$setup->addAttribute('catalog_product', 'videobox', array(
    'group'         => 'Video',
    'input'         => 'textarea',
    'type'          => 'text',
    'label'         => 'Youtube video url',
    'backend'       => '',
    'visible'       => 1,
    'required'      => 0,
    'user_defined' => 1,
    'searchable' => 0,
    'filterable' => 0,
    'comparable'    => 0,
    'visible_on_front' => 1,
    'visible_in_advanced_search'  => 0,
    'is_html_allowed_on_front' => 0,
    'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
));


$setup->addAttribute('catalog_product', 'customtab', array(
    'group'         => 'Custom Tab',
    'input'         => 'textarea',
    'type'          => 'text',
    'label'         => 'Custom Tab',
    'backend'       => '',
    'visible'       => 1,
    'required'      => 0,
    'user_defined' => 1,
    'searchable' => 0,
    'filterable' => 0,
    'comparable'    => 0,
    'visible_on_front' => 1,
    'visible_in_advanced_search'  => 0,
    'is_html_allowed_on_front' => 0,
    'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
));

$setup->addAttribute('catalog_category', 'menutopdescription1', array(
    'group'         => 'General',
    'input'         => 'textarea',
    'type'          => 'text',
    'label'         => 'Topmenu description',
    'backend'       => '',
    'visible'       => 1,
    'required'      => 0,
    'is_wysiwyg_enabled' => 1,
    'visible_on_front' => 1,
    'note'=>'Category description for top menu dropdown',
    'is_html_allowed_on_front' => 1,
    'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
));

$setup->addAttribute('customer', 'twitterid', array(
    'label'		=> 'TwitterID',
    'type'		=> 'varchar',
    'input'		=> 'text',
    'visible'	=> true,
    'required'	=> 0,
    'position'	=> 1,
));

$eavConfig = Mage::getSingleton('eav/config');
$attribute = $eavConfig->getAttribute('customer', 'twitterid');
$attribute->save();

$attribute_1 = $eavConfig->getAttribute('catalog_category', 'menutopdescription1');
$attribute_1->setData('is_wysiwyg_enabled', 1);
$attribute_1->save();

$installer->endSetup();