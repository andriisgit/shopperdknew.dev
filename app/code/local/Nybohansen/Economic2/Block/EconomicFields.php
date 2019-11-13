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

class Nybohansen_Economic2_Block_EconomicFields extends Mage_Core_Block_Html_Select{
    
    /**
     * Economic items cache
     *
     * @var array
     */
    private $_fields;


    /**
     * 
     * Fetches all e-conomic items and returns array
     */
	protected function _getEconomicFields(){
    	if(is_null($this->_fields)){
        	$this->_fields = array();
        	$this->_fields['Name'] = Mage::helper('economic2')->__('Name');
        	$this->_fields['BarCode'] = Mage::helper('economic2')->__('Barcode');
        	$this->_fields['CostPrice'] = Mage::helper('economic2')->__('Cost Price');
        	$this->_fields['Description'] = Mage::helper('economic2')->__('Description');
        	$this->_fields['InStock'] = Mage::helper('economic2')->__('Items in stock');
        	$this->_fields['IsAccessible'] = Mage::helper('economic2')->__('Accessible');
        	$this->_fields['SalesPrice'] = Mage::helper('economic2')->__('Sales price');
        	$this->_fields['RecommendedPrice'] = Mage::helper('economic2')->__('Recomended price');
        	$this->_fields['Available'] = Mage::helper('economic2')->__('Items available for sale');
    	}
        return $this->_fields;
    }
    
	public function setInputName($value){
        return $this->setName($value);
    }
    
    
    /**
     * Render block HTML
     *
     * @return string
     */
    public function _toHtml()
    {
    	if (!$this->getOptions()) {
            foreach ($this->_getEconomicFields() as $key => $label) {
                $this->addOption($key, $label);
            }
        }
        return parent::_toHtml();
    }
}
