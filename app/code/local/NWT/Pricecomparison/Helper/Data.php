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
 * Pricecomparison default helper
 *
 */
class NWT_Pricecomparison_Helper_Data extends Mage_Core_Helper_Abstract{


    public function getFeedPath($filename) {
        return Mage::getBaseDir().DS.'feeds'.DS .$filename;
    }

    public function getFeedUrl($filename) {
        return Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB).'feeds/'.$filename;
    }


    /**
     * Retrieve current feed object  (used in almost all adminhtml blocks)
     *
     * @return NWT_Pricecomparison_Model_Feed
     */
    public function getCurrentFeed($block)
    {
        if(!($block instanceof Varien_Object)) {
            Mage::throwException($this->__('invalid call'));
        }
        if (!($block->getData('feed') instanceof NWT_Pricecomparison_Model_Feed)) {
            $current_feed = Mage::registry('current_feed');
            if (!($current_feed instanceof NWT_Pricecomparison_Model_Feed)) {
                Mage::throwException(Mage::helper('pricecomparison')->__('current_feed is missing'));
            }
            $block->setData('feed', $current_feed);
        }
        return $block->getData('feed');
    }
}
