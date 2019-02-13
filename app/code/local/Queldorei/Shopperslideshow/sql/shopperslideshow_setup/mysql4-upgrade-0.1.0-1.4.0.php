<?php
/**
 * @version   1.0 12.0.2012
 * @author    queldorei http://www.queldorei.com <mail@queldorei.com>
 * @copyright Copyright (C) 2010 - 2012 queldorei
 */

$installer = $this;
/* @var $installer Mage_Catalog_Model_Resource_Eav_Mysql4_Setup */
$installer->startSetup();
/* flexslider */
$installer->setConfigData('shopperslideshow/config/slider', 'flexslider');
$installer->setConfigData('shopperslideshow/flexslider/height', '');
$installer->setConfigData('shopperslideshow/flexslider/animation', 'slide');
$installer->setConfigData('shopperslideshow/flexslider/slideshow', 'true');
$installer->setConfigData('shopperslideshow/flexslider/animation_loop', 'true');
$installer->setConfigData('shopperslideshow/flexslider/mousewheel', 'false');
$installer->setConfigData('shopperslideshow/flexslider/smoothheight', 'false');
$installer->setConfigData('shopperslideshow/flexslider/slideshow_speed', '7000');
$installer->setConfigData('shopperslideshow/flexslider/animation_speed', '400');
$installer->setConfigData('shopperslideshow/flexslider/control_nav', 'false');
$installer->setConfigData('shopperslideshow/flexslider/direction_nav', 'true');
$installer->setConfigData('shopperslideshow/flexslider/timeline', 'true');
/* revolution slider */

/**
 * Drop 'slides_store' table
 */
$conn = $installer->getConnection();

/**
 * Create table for stores
 */

/**
 * Assign 'all store views' to existing slides
 */


$installer->endSetup();