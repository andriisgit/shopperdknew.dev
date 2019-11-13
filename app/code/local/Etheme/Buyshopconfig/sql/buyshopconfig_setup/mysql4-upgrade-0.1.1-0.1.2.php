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

$setup->addAttribute('catalog_category', 'bs_top_html', array(
    'group'         => 'Megamenu',
    'input'         => 'textarea',
    'type'          => 'text',
    'label'         => 'Html block above menu',
    'backend'       => '',
    'visible'       => 1,
    'required'      => 0,
    'is_wysiwyg_enabled' => 1,
    'visible_on_front' => 1,
    'note'=>'This filed is compatible only with top-level category',
    'is_html_allowed_on_front' => 1,
    'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
));

$setup->addAttribute('catalog_category', 'bs_btm_html', array(
    'group'         => 'Megamenu',
    'input'         => 'textarea',
    'type'          => 'text',
    'label'         => 'Html block under menu',
    'backend'       => '',
    'visible'       => 1,
    'required'      => 0,
    'is_wysiwyg_enabled' => 1,
    'visible_on_front' => 1,
    'note'=>'This filed is compatible only with top-level category',
    'is_html_allowed_on_front' => 1,
    'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
));

$setup->addAttribute('catalog_category', 'bs_count_columns', array(
    'group'         => 'Megamenu',
    'input'         => 'text',
    'type'          => 'text',
    'label'         => 'Count of columns',
    'backend'       => '',
    'visible'       => 1,
    'required'      => 0,
    'is_wysiwyg_enabled' => 1,
    'visible_on_front' => 1,
    'note'=>'This filed is compatible only with top-level category',
    'is_html_allowed_on_front' => 1,
    'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
));

$setup->addAttribute('catalog_category', 'bs_category_lable', array(
    'group'         => 'Megamenu',
    'input'         => 'text',
    'type'          => 'text',
    'label'         => 'Category lable, for ex. "Hot!"',
    'backend'       => '',
    'visible'       => 1,
    'required'      => 0,
    'is_wysiwyg_enabled' => 1,
    'visible_on_front' => 1,
    'note'=>'This filed is compatible only with 2nd-level category',
    'is_html_allowed_on_front' => 1,
    'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
));





$eavConfig = Mage::getSingleton('eav/config');

$attribute_2 = $eavConfig->getAttribute('catalog_category', 'bs_top_html');
$attribute_2->setData('is_wysiwyg_enabled', 1);
$attribute_2->save();

$attribute_3 = $eavConfig->getAttribute('catalog_category', 'bs_btm_html');
$attribute_3->setData('is_wysiwyg_enabled', 1);
$attribute_3->save();



$installer->endSetup();