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
$installer->startSetup();

Mage::getConfig()->saveConfig('cms/wysiwyg/enabled', 'hidden');

$installer->endSetup();
