<?php
/**
 * NWT_Pricecomparison extension
 *
 * @category    NWT
 * @package     NWT_Pricecomparison
 * @copyright   Copyright (c) 2014 Nordic Web Team ( http://nordicwebteam.se/ )
 * @license     NWT Commercial License (NWTCL 1.0)
 * @author      Emil [carco] Sirbu (emil.sirbu@gmail.com)
 *
 */
/**
 * Feed config form tab
 */
class NWT_Pricecomparison_Block_Adminhtml_Form_Field_Attributes extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract{ 

    protected $_attributeRenderer;
    protected $_filterRenderer;
    protected $_yesnoRenderer;

    /**
    * Get select block for attribute
    *
    * @return NWT_Pricecomparison_Block_Adminhtml_Form_Field_Select
    */
    protected function _getAttributeRenderer() 
    {
        if (!$this->_attributeRenderer) {

            $this->_attributeRenderer = $this->getLayout()
                    ->createBlock('pricecomparison/adminhtml_form_field_select')
                    ->setExtraParams('style="width:200px"')
                    ->setOptions(Mage::getSingleton('pricecomparison/source_attribute')->toOptionArray())
                    ->setIsRenderToJsTemplate(true)
            ;

        }
        return $this->_attributeRenderer;
    }


    /**
    * Get select block for filter
    *
    * @return NWT_Pricecomparison_Block_Adminhtml_Form_Field_Select
    */
    protected function _getFilterRenderer() 
    {
        if (!$this->_filterRenderer) {

            $this->_filterRenderer = $this->getLayout()
                    ->createBlock('pricecomparison/adminhtml_form_field_select')
                    ->setExtraParams('style="width:150px"')
                    ->setOptions(Mage::getSingleton('pricecomparison/source_filter')->toOptionArray())
                    ->setIsRenderToJsTemplate(true)
            ;
        }
        return $this->_filterRenderer;
    }


    /**
    * Get yes/no filter
    *
    * @return NWT_Pricecomparison_Block_Adminhtml_Form_Field_Select
    */
    protected function _getYesNoRenderer() 
    {
        if (!$this->_yesnoRenderer) {

            $this->_yesnoRenderer = $this->getLayout()
                    ->createBlock('pricecomparison/adminhtml_form_field_select')
                    ->setExtraParams('style="width:60px"')
                    ->setOptions(array(array('label'=>'','value'=>''),array('label'=>'Yes','value'=>1)))
                    ->setIsRenderToJsTemplate(true)
            ;
        }
        return $this->_yesnoRenderer;
    }


 
    protected function _prepareToRender() 
    {



        $this->addColumn('label', array(
            'label' => Mage::helper('pricecomparison')->__('Label (optional)'),
            'required'=>true,
            'style'=>'width:120px'
        ));

        $this->addColumn('attribute', array(
            'label' => Mage::helper('pricecomparison')->__('Attribute'),
            'renderer' => $this->_getAttributeRenderer(),
        ));

        $this->addColumn('default', array(
            'label' => Mage::helper('pricecomparison')->__('Empty value'),
            'style'=>'width:80px'
        ));


        $this->addColumn('filter', array(
            'label' => Mage::helper('pricecomparison')->__('Filter'),
            'renderer' => $this->_getFilterRenderer(),
        ));

        $this->addColumn('params', array(
            'label' => Mage::helper('pricecomparison')->__('Parameters'),
            'style'=>'width:100px'
        ));

        $this->addColumn('max', array(
            'label' => Mage::helper('pricecomparison')->__('Max. length'),
            'style'=>'width:60px'
        ));

        $this->addColumn('oneline', array(
            'label' => Mage::helper('pricecomparison')->__('One line'),
             'renderer' => $this->_getYesNoRenderer(),
        ));



        $this->addColumn('wrap', array(
            'label' => Mage::helper('pricecomparison')->__('Wrap (use $val for attribute value)'),
        ));


        // Disables "Add after" button
        //$this->_addAfter = false; 
        $this->_addButtonLabel = Mage::helper('pricecomparison')->__('Add Field');
    }


    /**
     * Prepare existing row data object
     *
     * @param Varien_Object
     */
    protected function _prepareArrayRow(Varien_Object $row)
    {
        $row->setData(
            'option_extra_attr_' . $this->_getAttributeRenderer()->calcOptionHash($row->getData('attribute')),
            'selected="selected"'
        );
        $row->setData(
            'option_extra_attr_' . $this->_getFilterRenderer()->calcOptionHash($row->getData('filter')),
            'selected="selected"'
        );
        $row->setData(
            'option_extra_attr_' . $this->_getYesNoRenderer()->calcOptionHash($row->getData('oneline')),
            'selected="selected"'
        );

    }


    public function render(Varien_Data_Form_Element_Abstract $element) {
        
        //remove fucking label, I want 100% width
        //@see Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract::render
    
        $id = $element->getHtmlId();
        $dontNeed  = '<td class="label"><label for="'.$id.'">'.$element->getLabel().'</label></td>';
        return str_replace($dontNeed,'',parent::render($element));
    }

}  