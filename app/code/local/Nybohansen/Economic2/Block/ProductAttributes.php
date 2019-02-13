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

class Nybohansen_Economic2_Block_ProductAttributes extends Mage_Core_Block_Html_Select{
    
    /**
     * Economic items cache
     *
     * @var array
     */
    private $_attributes;


    /**
     * 
     * Fetches all magento attributes and returns array
     */
	protected function _getMagentoAttributes(){
    	if(is_null($this->_attributes)){
    		
    		$collection = Mage::getResourceModel('eav/entity_attribute_collection')
            					->setEntityTypeFilter( Mage::getModel('eav/entity')->setType('catalog_product')->getTypeId() )
            					->addFieldToFilter('is_visible', '1');
        	
			$this->_attributes = array();
        	foreach($collection->getItems() as $col) {
            	$this->_attributes[$col->getAttributeCode()] = htmlspecialchars($col->getFrontendLabel(), ENT_NOQUOTES | ENT_QUOTES);
        	}
        	$this->_attributes['qty'] = Mage::helper('cataloginventory')->__('Qty');
    	}
        asort($this->_attributes);
        return $this->_attributes;
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
            foreach ($this->_getMagentoAttributes() as $key => $label) {
                $this->addOption($key, $label);
            }
        }
        
        return parent::_toHtml();
    }
}
