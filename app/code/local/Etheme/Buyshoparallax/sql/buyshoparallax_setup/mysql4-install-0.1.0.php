<?php
/**
 * @version   1.0 14.08.2012
 * @author    TonyEcommerce http://www.TonyEcommerce.com <support@TonyEcommerce.com>
 * @copyright Copyright (c) 2012 TonyEcommerce
 */
$installer = $this;
$installer->startSetup();

$installer->run("

DROP TABLE IF EXISTS `{$this->getTable('buyshoparallax')}`;
CREATE TABLE `{$this->getTable('buyshoparallax')}` (
  `slide_id` int(11) unsigned NOT NULL auto_increment,
  `slide_html_code` text NOT NULL,
  `link` varchar(255) NOT NULL default '',
  `status` int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`slide_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `{$this->getTable('buyshoparallax')}` (`slide_id`, `slide_html_code`, `link`, `status`) VALUES (1, '', 'http://themeforest.net/item/buyshop-premium-responsive-retina-magento-theme/4287671', 1);
INSERT INTO `{$this->getTable('buyshoparallax')}` (`slide_id`, `slide_html_code`, `link`, `status`) VALUES (2, '', 'http://themeforest.net/item/buyshop-premium-responsive-retina-magento-theme/4287671', 1);
INSERT INTO `{$this->getTable('buyshoparallax')}` (`slide_id`, `slide_html_code`, `link`, `status`) VALUES (3, '', 'http://themeforest.net/item/buyshop-premium-responsive-retina-magento-theme/4287671', 1);
INSERT INTO `{$this->getTable('buyshoparallax')}` (`slide_id`, `slide_html_code`, `link`, `status`) VALUES (4, '', 'http://themeforest.net/item/buyshop-premium-responsive-retina-magento-theme/4287671', 1);

");
$installer->endSetup();