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

$setup->addAttribute('catalog_product', 'customtabtitle', array(
    'group'         => 'Custom Tab',
    'input'         => 'text',
    'type'          => 'text',
    'label'         => 'Custom Tab Title',
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

$setup->addAttribute('catalog_product', 'shortparams', array(
    'group'         => 'Description',
    'input'         => 'textarea',
    'type'          => 'text',
    'label'         => 'Short Params for product rollover',
    'backend'       => '',
    'visible'       => 1,
    'required'      => 0,
    'is_wysiwyg_enabled' => 1,
    'visible_on_front' => 1,
    'is_html_allowed_on_front' => 1,
    'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
));

$eavConfig = Mage::getSingleton('eav/config');

$attribute_2 = $eavConfig->getAttribute('catalog_product', 'customtab');
$attribute_2->setData('is_wysiwyg_enabled', 1);
$attribute_2->save();

$attribute_3 = $eavConfig->getAttribute('catalog_product', 'shortparams');
$attribute_3->setData('is_wysiwyg_enabled', 1);
$attribute_3->save();

$installer->endSetup();