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
 * Feed edit form tab
 */
class NWT_Pricecomparison_Block_Adminhtml_Feed_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form{    
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

        $feed = Mage::helper('pricecomparison')->getCurrentFeed($this);


        $fieldset = $form->addFieldset('feed_form', array('legend'=>Mage::helper('pricecomparison')->__('Feed')));


        $fieldset->addField('name', 'text', array(
            'label' => Mage::helper('pricecomparison')->__('Name'),
            'name'  => 'name',
            'required'  => true,
            'class' => 'required-entry',

        ));

        $fieldset->addField('store_id', 'select', array(
            'label' => Mage::helper('pricecomparison')->__('Store'),
            'name'  => 'store_id',
            'required'  => true,
            'class' => 'required-entry',
            'values'   => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(),
        ));

        if(!$feed->getId()) {
            $fieldset->addField('feed_type', 'hidden',array('name'=>'feed_type'));
        }

        $fieldset->addField('filename', 'text', array(
            'label' => Mage::helper('pricecomparison')->__('Filename'),
            'name'  => 'filename',
            'required'  => true,
            'class' => 'required-entry',

        ));

        $fieldset->addField('status', 'select', array(
            'label' => Mage::helper('pricecomparison')->__('Status'),
            'name'  => 'status',
            'values'=> array(
                array(
                    'value' => 1,
                    'label' => Mage::helper('pricecomparison')->__('Enabled (will be autogenerated)'),
                ),
                array(
                    'value' => 0,
                    'label' => Mage::helper('pricecomparison')->__('Disabled'),
                ),
            ),
        ));

        $form->setValues($feed->getData());

        return parent::_prepareForm();
    }
}