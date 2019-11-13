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

class Nybohansen_Economic2_Block_PaymentMapping extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
{
 	
	/**
     * @var Mage_CatalogInventory_Block_Adminhtml_Form_Field_Customergroup
     */
    protected $_paymentTypeRenderer;
    
    /**
     * @var Mage_CatalogInventory_Block_Adminhtml_Form_Field_Customergroup
     */
    protected $_economicTermOfPaymentRenderer;

    /**
     * Retrieve group column renderer
     *
     * @return Mage_CatalogInventory_Block_Adminhtml_Form_Field_Customergroup
     */
    protected function _getShippingTypeRenderer()
    {
    	if (!$this->_paymentTypeRenderer) {
        	$this->_paymentTypeRenderer = $this->getLayout()->createBlock(
                'economic2/paymentTypes', '',
                array('is_render_to_js_template' => true)
            );
            $this->_paymentTypeRenderer->setClass('payment_type_select');
            $this->_paymentTypeRenderer->setExtraParams('style="width:200px"');
        }
        return $this->_paymentTypeRenderer;
    }

    /**
     * Retrieve group column renderer
     *
     * @return Mage_CatalogInventory_Block_Adminhtml_Form_Field_Customergroup
     */
    protected function _getEconomicTermOfPaymentRenderer()
    {
        if (!$this->_economicTermOfPaymentRenderer) {
            $this->_economicTermOfPaymentRenderer = $this->getLayout()->createBlock(
                'economic2/economicTermOfPayments', '',
                array('is_render_to_js_template' => true)
            );
            $this->_economicTermOfPaymentRenderer->setClass('economic_term_of_payment_select');
            $this->_economicTermOfPaymentRenderer->setExtraParams('style="width:120px"');
        }
        return $this->_economicTermOfPaymentRenderer;
    }
    
    
    /**
     * Prepare to render
     */
    protected function _prepareToRender()
    {
    	$this->addColumn('payment_type_id', array(
            'label' => Mage::helper('economic2')->__('Payment type'),
            'renderer' => $this->_getShippingTypeRenderer(),
        ));
        $this->addColumn('economic_term_of_payment_id', array(
            'label' => Mage::helper('economic2')->__('e-conomic term of payment'),
            'renderer' => $this->_getEconomicTermOfPaymentRenderer(),
        ));
        $this->_addAfter = false;
        $this->_addButtonLabel = Mage::helper('cataloginventory')->__('Add new mapping');
    }

    /**
     * Prepare existing row data object
     *
     * @param Varien_Object
     */
    protected function _prepareArrayRow(Varien_Object $row)
    {     	
	    	$row->setData(
        			    	'option_extra_attr_' . $this->_getShippingTypeRenderer()->calcOptionHash($row->getData('payment_type_id')),
        	    			'selected="selected"'
        				 );	        	
        
	    	$row->setData(
        			    	'option_extra_attr_' . $this->_getEconomicTermOfPaymentRenderer()->calcOptionHash($row->getData('economic_term_of_payment_id')),
        	    			'selected="selected"'
        				 );        	
    }    
}
