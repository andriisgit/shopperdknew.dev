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

class Nybohansen_Economic2_Block_FieldMappingFromEToM extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
{
 	
	/**
     * @var Mage_CatalogInventory_Block_Adminhtml_Form_Field_Customergroup
     */
    protected $_economicFieldRenderer;
    
    /**
     * @var Mage_CatalogInventory_Block_Adminhtml_Form_Field_Customergroup
     */
    protected $_magentoFieldRenderer;

    /**
     * Retrieve group column renderer
     *
     * @return Mage_CatalogInventory_Block_Adminhtml_Form_Field_Customergroup
     */
    protected function _getEconomicFieldRenderer()
    {
    	if (!$this->_economicFieldRenderer) {
        	$this->_economicFieldRenderer = $this->getLayout()->createBlock(
                'economic2/EconomicFields', '',
                array('is_render_to_js_template' => true)
            );
            $this->_economicFieldRenderer->setClass('shipping_type_select');
            $this->_economicFieldRenderer->setExtraParams('style="width:120px"');
        }
        return $this->_economicFieldRenderer;
    }

    /**
     * Retrieve group column renderer
     *
     * @return Mage_CatalogInventory_Block_Adminhtml_Form_Field_Customergroup
     */
    protected function _getMagentoFieldRenderer()
    {
        if (!$this->_magentoFieldRenderer) {
            $this->_magentoFieldRenderer = $this->getLayout()->createBlock(
                'economic2/productAttributes', '',
                array('is_render_to_js_template' => true)
            );
            $this->_magentoFieldRenderer->setClass('economic_item_select');
            $this->_magentoFieldRenderer->setExtraParams('style="width:120px"');
        }
        return $this->_magentoFieldRenderer;
    }
    
    
    /**
     * Prepare to render
     */
    protected function _prepareToRender()
    {
        $this->addColumn('economic_field', array(
            'label' => Mage::helper('economic2')->__('e-conomic field'),
            'renderer' => $this->_getEconomicFieldRenderer(),
        ));
        $this->addColumn('magento_field', array(
            'label' => Mage::helper('economic2')->__('Magento field'),
            'renderer' => $this->_getMagentoFieldRenderer(),
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
        			    	'option_extra_attr_' . $this->_getEconomicFieldRenderer()->calcOptionHash($row->getData('economic_field')),
        	    			'selected="selected"'
        				 );	        	
        
	    	$row->setData(
        			    	'option_extra_attr_' . $this->_getMagentoFieldRenderer()->calcOptionHash($row->getData('magento_field')),
        	    			'selected="selected"'
        				 );
        	        	
    }

    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element){
		//JS to handle the toogle of the mapping array
    	$js = "<script type='text/javascript'>
		    		function toogleMapping(){        
		    			var row = $('row_economic2_options_productConfig_field_mapping_from_e_to_m');
		    			if($('economic2_options_productConfig_product_update_from_economic').value == 1){
		    				row.show();
    					}else{
    						row.hide();
    					}
		    		}    
		    		Event.observe('economic2_options_productConfig_product_update_from_economic', 'change', toogleMapping)			
		    		toogleMapping();
			   </script>";
    	return parent::_getElementHtml($element).$js;
    }
    
}
