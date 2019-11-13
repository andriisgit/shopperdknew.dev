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

class Nybohansen_Economic2_Block_PaymentTypes extends Mage_Core_Block_Html_Select
{
    /**
     * 
     * Array of payment types
     * @var array
     */
    private $_paymentTypes;
    
	/**
	 * 
	 * Fetches all payment types and returns array
	 */
    protected function _getpaymentTypes(){
        $storeId = Mage::app()->getRequest()->getParam('store');
        $allpaymentMethods = Mage::getSingleton('payment/config')->getActiveMethods($storeId);
        if(is_null($this->_paymentTypes)){
            foreach ($allpaymentMethods as $paymentCode => $paymentCode){
                $label = Mage::getStoreConfig('payment/'.$paymentCode.'/title', $storeId);
                if($label){
                    $this->_paymentTypes[$paymentCode] = $label;

                    if($paymentCode == 'quickpaypayment_payment'){
                        $quickpayCards = Mage::getModel('economic2/system_config_source_cardTypeList')->QuickpayCards();
                        foreach ($quickpayCards as $quickpayCard){
                            $this->_paymentTypes[$paymentCode .';'.$quickpayCard['value']] = "           ".$quickpayCard['label'];
                        }
                    }elseif($paymentCode == 'epay_standard'){
                        $epayCards = Mage::getModel('economic2/system_config_source_cardTypeList')->EpayCards();
                        foreach ($epayCards as $epayCard){
                            $this->_paymentTypes[$paymentCode .';'.$epayCard['value']] = "           ".$epayCard['label'];
                        }
                    }
                }
            }
        }
        return $this->_paymentTypes;
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
    	$this->_getpaymentTypes();
        
    	if (!$this->getOptions()) {
    		foreach ($this->_getpaymentTypes() as $key => $label) {
                $this->addOption($key, $label);
            }
        }
        
        return parent::_toHtml();
    }
}
