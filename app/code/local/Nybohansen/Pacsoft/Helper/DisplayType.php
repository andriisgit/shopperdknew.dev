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
 * @package    Nybohansen_Pacsoft
 * @copyright  Copyright (c) 2014 Nybohansen ApS
 * @license    LICENSE.txt
 */

class Nybohansen_Pacsoft_Helper_DisplayType extends Mage_Core_Helper_Abstract
{

    public function getDisplayType(){

        $storeId = Mage::app()->getStore()->getStoreId();
        if(Mage::app()->getRequest()->getParam('displayType')){
            return 'admin';
        }

        if($this->isMobileDevice() && Mage::getStoreConfig('carriers/pacsoft/useFallbackOnMobile', $storeId)){
            //Force select box
            return 'select';
        }

        return Mage::getStoreConfig('carriers/pacsoft/parcelShopPresentation', $storeId);

    }

    private function isMobileDevice(){

        $userAgent = $_SERVER['HTTP_USER_AGENT'];
        //Detect special conditions devices
        $iPod    = stripos($userAgent,"iPod");
        $iPhone  = stripos($userAgent,"iPhone");
        $iPad    = stripos($userAgent,"iPad");
        $Android = stripos($userAgent,"Android");
        $webOS   = stripos($userAgent,"webOS");

        if( $iPod || $iPhone || $iPad || $Android || $webOS){
            return true;
        }

        return false;
    }

}