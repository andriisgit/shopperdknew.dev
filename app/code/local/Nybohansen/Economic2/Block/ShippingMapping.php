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

class Nybohansen_Economic2_Block_ShippingMapping extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
{
 	
	/**
     * @var Mage_CatalogInventory_Block_Adminhtml_Form_Field_Customergroup
     */
    protected $_shippingTypeRenderer;
    
    /**
     * @var Mage_CatalogInventory_Block_Adminhtml_Form_Field_Customergroup
     */
    protected $_economicItemRenderer;

    /**
     * Retrieve group column renderer
     *
     * @return Mage_CatalogInventory_Block_Adminhtml_Form_Field_Customergroup
     */
    protected function _getShippingTypeRenderer()
    {
    	if (!$this->_shippingTypeRenderer) {
        	$this->_shippingTypeRenderer = $this->getLayout()->createBlock(
                'economic2/shippingTypes', '',
                array('is_render_to_js_template' => true)
            );
            $this->_shippingTypeRenderer->setClass('shipping_type_select');
            $this->_shippingTypeRenderer->setExtraParams('style="width:120px"');
        }
        return $this->_shippingTypeRenderer;
    }

    /**
     * Retrieve group column renderer
     *
     * @return Mage_CatalogInventory_Block_Adminhtml_Form_Field_Customergroup
     */
    protected function _getEconomicItemRenderer()
    {
        if (!$this->_economicItemRenderer) {
            $this->_economicItemRenderer = $this->getLayout()->createBlock(
                'economic2/economicItems', '',
                array('is_render_to_js_template' => true)
            );
            $this->_economicItemRenderer->setClass('economic_item_select');
            $this->_economicItemRenderer->setExtraParams('style="width:120px"');
        }
        return $this->_economicItemRenderer;
    }
    
    
    /**
     * Prepare to render
     */
    protected function _prepareToRender()
    {
    	$this->addColumn('shipping_type_id', array(
            'label' => Mage::helper('economic2')->__('Shipping type'),
            'renderer' => $this->_getShippingTypeRenderer(),
        ));
        $this->addColumn('economic_item_id', array(
            'label' => Mage::helper('economic2')->__('e-conomic item id'),
            'style' => 'width:120px'
//            'renderer' => $this->_getEconomicItemRenderer(),
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
        			    	'option_extra_attr_' . $this->_getShippingTypeRenderer()->calcOptionHash($row->getData('shipping_type_id')),
        	    			'selected="selected"'
        				 );	        	
        
//	    	$row->setData(
//        			    	'option_extra_attr_' . $this->_getEconomicItemRenderer()->calcOptionHash($row->getData('economic_item_id')),
//        	    			'selected="selected"'
//        				 );
    }    
}
