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

class Nybohansen_Economic2_Helper_Store extends Mage_Core_Helper_Abstract
{

    public function getStoreId()
    {
        $website = Mage::app()->getRequest()->getParam('website');
        $store   = Mage::app()->getRequest()->getParam('store');

        $store_id = 0;
        if (strlen($store)) // store level
        {
            $store_id = Mage::getModel('core/store')->load($store)->getId();
        }
        elseif (strlen($website)) // website level
        {
            $website_id = Mage::getModel('core/website')->load($website)->getId();
            $store_id = Mage::app()->getWebsite($website_id)->getDefaultStore()->getId();
        }

        return $store_id;

    }

    public function getStoreInfo(){

        $website = Mage::app()->getRequest()->getParam('website');
        $store   = Mage::app()->getRequest()->getParam('store');

        $scope = 'default';
        $store_id = 0;
        if (strlen($store)) // store level
        {
            $store_id = Mage::getModel('core/store')->load($store)->getId();
            $scope = 'stores';
        }
        elseif (strlen($website)) // website level
        {
            $website_id = Mage::getModel('core/website')->load($website)->getId();
            $store_id = Mage::app()->getWebsite($website_id)->getDefaultStore()->getId();
            $scope = 'websites';
        }


        return array('id' => $store_id, 'scope' => $scope, 'store' => $store, 'website' => $website);
    }
}


