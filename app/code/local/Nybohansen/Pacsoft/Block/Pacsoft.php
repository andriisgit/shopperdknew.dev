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

class Nybohansen_Pacsoft_Block_Pacsoft extends Mage_Checkout_Block_Onepage_Shipping_Method_Available
{
	
	private $_addressId;
	private $_countryCode;
	
    public function __construct(){
        $this->setTemplate('pacsoft/pacsoft.phtml');
    }
    
    public function setCountryCode($countryCode){
    	$this->_countryCode = $countryCode;
    }
    
	public function getCountryCode(){
    	return $this->_countryCode;
    }
    
    public function setAddressId($addressId){
    	$this->_addressId = $addressId;
    }
    
	public function getAddressId(){
    	return $this->_addressId;
    }

    public function showParcelShopChoice(){
        $rate = Mage::getModel('pacsoft/rates')->load($this->getRateId());
        return $rate->servicePointAddonActive();
    }

    public function showDeliveryNote(){
        $rate = Mage::getModel('pacsoft/rates')->load($this->getRateId());
        return $rate->showDeliveryNote();
    }

    public function getRateId(){
        $tmp = explode('_', $this->getRate()->getCode());
        return end($tmp);
    }

    public function getSelectedZip(){
        $parcelShopData = Mage::getSingleton('checkout/session')->getPacsoftShippingData();
        if(isset($parcelShopData[$this->getAddressId()]['zip'])){
            return $parcelShopData[$this->getAddressId()]['zip'];
        }else{
            return $this->getAddress()->getPostcode();
        }
    }

    public function getSelectedPickupId(){
        $parcelShopData = Mage::getSingleton('checkout/session')->getPacsoftShippingData();
        if(isset($parcelShopData[$this->getAddressId()]['parcelshopId'])){
            return $parcelShopData[$this->getAddressId()]['parcelshopId'];
        }
    }

    public function getSelectedPickupDescription(){
        $parcelShopData = Mage::getSingleton('checkout/session')->getPacsoftShippingData();
        if(isset($parcelShopData[$this->getAddressId()]['shipping_description'])){
            return $parcelShopData[$this->getAddressId()]['shipping_description'];
        }
    }

    //Called when page is reloaded and select box is chosen
    public function getParcelShopsAsSelectBox(){
        $block = $this->getLayout()->createBlock('pacsoft/servicePoints')
                                   ->setData('zip', $this->getSelectedZip())
                                   ->setData('selectedPickupId', $this->getSelectedPickupId())
                                   ->setData('countryCode', $this->getCountryCode())
                                   ->setData('formCode', $this->getFormCode());

        echo $block->toHtml();
    }

    //Called when page is reloaded and map is chosen
    public function getChosenParcelShop(){
        echo '';
    }

    public function getDeliveryNote(){
        $parcelShopData = Mage::getSingleton('checkout/session')->getPacsoftShippingData();
        if(isset($parcelShopData[$this->getAddressId()]['deliveryNote'])){
            return $parcelShopData[$this->getAddressId()]['deliveryNote'];
        }else{
            return '';
        }
    }

    public function parcelShoppresentationType(){
        /** @var $displayType Nybohansen_Pacsoft_Helper_DisplayType */
        $displayType = Mage::helper('pacsoft/DisplayType');
        return $displayType->getDisplayType();
    }

    public function enableMapOverlay(){
        return $this->parcelShoppresentationType() == 'map';
    }

    public function getFormCode(){
        return 'pacsoft_'+$this->getAddressId().'_'.$this->getRateId();
    }
}

