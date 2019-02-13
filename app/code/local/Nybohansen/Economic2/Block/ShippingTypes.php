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

class Nybohansen_Economic2_Block_ShippingTypes extends Mage_Core_Block_Html_Select
{
    /**
     * 
     * Array of shipping types
     * @var array
     */
    private $_shippingTypes;

	/**
	 * 
	 * Fetches all shipping types and returns array
	 */
	protected function _getShippingTypes(){
    	$allShippingMethods = Mage::getSingleton('shipping/config')->getAllCarriers();
    	if(is_null($this->_shippingTypes)){
			foreach ($allShippingMethods as $key => $method){
				$label = ($method->getConfigData('title') ? $method->getConfigData('title') : $method->getConfigData('name'));
				if($label){
					$this->_shippingTypes[$key] = $label;
				}
			}
    	}

        // get matrixrate shipping methods
        if(class_exists('Webshopapps_Matrixrate_Model_Mysql4_Carrier_Matrixrate_Collection', false)){
            $matrixrates = new Webshopapps_Matrixrate_Model_Mysql4_Carrier_Matrixrate_Collection();
            if($matrixrates){
                $matrixrates->load();
                foreach ( $matrixrates as $rate ) {
                    //echo var_export ($rate->getData(), true );
                    $this->_shippingTypes["matrixrate_matrixrate_".$rate->getPk()] = "Matrixrate ".$rate->getDeliveryType().",".$rate->getConditionName().", " . $rate->getConditionFromValue()."-".$rate->getConditionToValue();
                }
            }
        }
    	return $this->_shippingTypes;
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
    	$this->_getShippingTypes();
        
    	if (!$this->getOptions()) {
    		foreach ($this->_getShippingTypes() as $key => $label) {
                $this->addOption($key, $label);
            }
        }
        
        return parent::_toHtml();
    }
}
