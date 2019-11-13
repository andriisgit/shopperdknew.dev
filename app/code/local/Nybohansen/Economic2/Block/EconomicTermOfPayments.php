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

class Nybohansen_Economic2_Block_EconomicTermOfPayments extends Mage_Core_Block_Html_Select{
    
    /**
     * Economic items cache
     *
     * @var array
     */
    private $_economicTermOfPayments;


    /**
     * 
     * Fetches all e-conomic items and returns array
     */
	protected function _getEconomicTermOfPayments(){
    	$allEconomicTermOfPayments = Mage::getModel('economic2/system_config_source_termOfPaymentList');
    	/* @var $allEconomicTermOfPayments Nybohansen_Economic2_Model_ItemList */
    	if(is_null($this->_economicTermOfPayments)){
    		$items = $allEconomicTermOfPayments->toOptionArray();
    		foreach ($items as $item) {
    			$this->_economicTermOfPayments[$item['value']] = htmlspecialchars($item['label'], ENT_NOQUOTES | ENT_QUOTES);
    		}
    	}
    	return $this->_economicTermOfPayments;
    }
    
	public function setInputName($value)
    {
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
            foreach ($this->_getEconomicTermOfPayments() as $key => $label) {
                $this->addOption($key, $label);
            }
        }
        
        return parent::_toHtml();
    }
}
