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
class NWT_Pricecomparison_Block_Adminhtml_Feed_Edit_Tab_Attributes extends Mage_Adminhtml_Block_Widget_Form{    


    /**
     * prepare the form
     * @access protected
     * @return self
     */
    protected function _prepareForm() {

        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('feed_');
        $form->setFieldNameSuffix('feed');

        $feed = Mage::helper('pricecomparison')->getCurrentFeed($this);


        $this->setForm($form);
        $fieldset = $form->addFieldset('feed_config', array('legend'=>Mage::helper('pricecomparison')->__('Fields mapping (based on [%s])',$feed->getFeedType())));

        $configField = $fieldset->addField('attributes', 'text', array(
            'label' => Mage::helper('pricecomparison')->__('Name'),
            'name'  => 'attributes',
            'required'  => true,
            'class' => 'required-entry',
        ));

        // Setting custom renderer for content field to remove label column
        $renderer =$this->getLayout()->createBlock('pricecomparison/adminhtml_form_field_attributes');
        $configField->setRenderer($renderer);
        

        $form->setValues($feed->getData());

        $fieldset = $form->addFieldset('feed_columns', array('legend'=>Mage::helper('pricecomparison')->__('Columns and Filter(s)'),'class'=>'fieldset-wide'));

        $fieldset->addField('help_label','label',array(
            'label'=> Mage::helper('pricecomparison')->__('Columns'),
            'after_element_html'=>implode("<br />",array(
                Mage::helper('pricecomparison')->__('<strong>Label</strong>: If none specified, attribute code will be used'),
                Mage::helper('pricecomparison')->__('<strong>Empty</strong>: Default value if selected attribute does not have.'),
                Mage::helper('pricecomparison')->__('<strong>Parameters</strong>: Used for specified filter (if any). Use comma to enter multiple parameters. Ex. <em>Yes,No</em> or <em>In Stock, Out of Stock</em>.<br>&nbsp;&nbsp;&nbsp;&nbsp;You could also use it to set <strong>Category separator</strong>(default is <em>/</em>)<br>&nbsp;&nbsp;&nbsp;&nbsp;If you want to list all categories (path), just add 2 separators into the parameter column<br>&nbsp;&nbsp;&nbsp; (ex. <em>/,|</em> whill give you Cat1 / Cat12 | Cat2 / Cat21 / Cat 2111)'),
                Mage::helper('pricecomparison')->__('<strong>Max. Length</strong>: Maximum length (for text fields)'),
                Mage::helper('pricecomparison')->__('<strong>One line</strong>: Remove line breaks (for multiple lines text fields)'),
                Mage::helper('pricecomparison')->__('<strong>Parameters</strong>: Used for specified filter (if any). Use comma to enter multiple parameters. Ex. <em>Yes,No</em> or <em>In Stock, Out of Stock</em>'),
                Mage::helper('pricecomparison')->__('<strong>Wrap</strong>:Use <strong>$val</strong> for attribute value (ex. <em>$val USD</em>). You may also use a subset of <a href="http://www.magentocommerce.com/wiki/3_-_store_setup_and_management/cms/markup_tags" target="blank">Magento CMS directives</a> (store, media, skin url). Ex: <em>{{store direct_url="$val"}}</em>. ')
            ))
        ));


        //$fieldset = $form->addFieldset('feed_filters', array('legend'=>Mage::helper('pricecomparison')->__('Columns and filter(s)'),'style'=>'width:45%;float:right;'));

        $filters = Mage::getSingleton('pricecomparison/source_filter')->getFilters();
        foreach($filters as $filter=>$values) {
            $fieldset->addField('help_'.$filter, 'label', array(
                'label' => $values['label'],
                'after_element_html' => $values['help']//.'<br /><small><em>'.$values['model'].'::'.'filter_'.$filter.'</em></small>'
            ));
        }


        return parent::_prepareForm();
    }
}