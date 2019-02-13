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

class Nybohansen_Economic2_Block_CustomerGroups extends Mage_Core_Block_Html_Select{
    
    /**
     * Magento customer groups cache
     *
     * @var array
     */
    private $_customerGroups;


    /**
     * 
     * Fetches all customer groups and returns array
     */
	protected function _getMagentoCustomerGroups(){

        if(is_null($this->_customerGroups)){

            $this->_customerGroups = array();

            $customer_group = new Mage_Customer_Model_Group();
            $allGroups  = $customer_group->getCollection()->toOptionHash();
            foreach($allGroups as $key=>$allGroup){
                $this->_customerGroups[$key]= htmlspecialchars($allGroup, ENT_NOQUOTES | ENT_QUOTES);
            }
    	}
        return $this->_customerGroups;
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
            foreach ($this->_getMagentoCustomerGroups() as $key => $label) {
                $this->addOption($key, $label);
            }
        }
        
        return parent::_toHtml();
    }
}
