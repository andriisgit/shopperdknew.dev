<?php
/**
 * @version   1.0 14.08.2012
 * @author    TonyEcommerce http://www.TonyEcommerce.com <support@TonyEcommerce.com>
 * @copyright Copyright (c) 2012 TonyEcommerce
 */
$installer = $this;
$installer->startSetup();
$installer->run("

DROP TABLE IF EXISTS `{$this->getTable('buyshopflex')}`;
CREATE TABLE `{$this->getTable('buyshopflex')}` (
  `slide_id` int(11) unsigned NOT NULL auto_increment,
  `image` varchar(255) NOT NULL default '',
  `link` varchar(255) NOT NULL default '',
  `status` smallint(6) NOT NULL default '0',
  PRIMARY KEY (`slide_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `{$this->getTable('buyshopflex')}` (`slide_id`, `image`, `link`, `status`) VALUES (1, 'etheme/buyshop/buyshopflex/slider1.jpg', 'http://themeforest.net/item/buyshop-premium-responsive-retina-magento-theme/4287671', 1);
INSERT INTO `{$this->getTable('buyshopflex')}` (`slide_id`, `image`, `link`, `status`) VALUES (2, 'etheme/buyshop/buyshopflex/slider2.jpg', 'http://themeforest.net/item/buyshop-premium-responsive-retina-magento-theme/4287671', 1);
INSERT INTO `{$this->getTable('buyshopflex')}` (`slide_id`, `image`, `link`, `status`) VALUES (3, 'etheme/buyshop/buyshopflex/slider3.jpg', 'http://themeforest.net/item/buyshop-premium-responsive-retina-magento-theme/4287671', 1);
INSERT INTO `{$this->getTable('buyshopflex')}` (`slide_id`, `image`, `link`, `status`) VALUES (4, 'etheme/buyshop/buyshopflex/slider4.jpg', 'http://themeforest.net/item/buyshop-premium-responsive-retina-magento-theme/4287671', 1);
INSERT INTO `{$this->getTable('buyshopflex')}` (`slide_id`, `image`, `link`, `status`) VALUES (5, 'etheme/buyshop/buyshopflex/slider5.jpg', 'http://themeforest.net/item/buyshop-premium-responsive-retina-magento-theme/4287671', 1);

");
$installer->endSetup();

