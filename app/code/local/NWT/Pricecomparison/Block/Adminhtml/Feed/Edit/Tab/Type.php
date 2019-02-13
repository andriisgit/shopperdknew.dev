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
 * Feed edit form tab, select type
 */
class NWT_Pricecomparison_Block_Adminhtml_Feed_Edit_Tab_Type extends Mage_Adminhtml_Block_Widget_Form {


    /**
     * prepare the form
     * @access protected
     * @return Pricecomparison_Feed_Block_Adminhtml_Feed_Edit_Tab_Form
     */
    protected function _prepareForm(){

        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('feed_');
        $form->setFieldNameSuffix('feed');
        $this->setForm($form);


        $fieldset = $form->addFieldset('feed_form', array('legend'=>Mage::helper('pricecomparison')->__('Feed')));


        $current_feed = Mage::registry('current_feed');
        $edit = $current_feed && $current_feed->getId();


        $fieldset->addField('feed_type', 'select', array(
            'label' => Mage::helper('pricecomparison')->__('Feed Type'),
            'name'  => 'feed_type',
            'required'  => true,
            'class' => 'required-entry',
            'values'   => Mage::getSingleton('pricecomparison/source_feed')->toOptionArray()
        ));

        $fieldset->addField('store_id', 'select', array(
            'label' => Mage::helper('pricecomparison')->__('Store'),
            'name'  => 'store_id',
            'required'  => true,
            'class' => 'required-entry',
            'values'   => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm()
        ));

        $button = $this->getLayout()->createBlock('adminhtml/widget_button')->setData(array(
                    'label'     => $this->__('Continue'),
                    'onclick'   => "setLocation('".$this->getContinueUrl()."'.replace('{{type}}',$('feed_feed_type').value).replace('{{store}}',$('feed_store_id').value))",
                    'class'     => 'save'
        ));

        $fieldset->addField('continue', 'note', array(
            'text' => $button->toHtml()
        ));

        return parent::_prepareForm();
    }

    protected function getContinueUrl() {
        return $this->getUrl('*/*/new', array('_current'  => true,'type'      => '{{type}}','store' => '{{store}}'));
    }



}