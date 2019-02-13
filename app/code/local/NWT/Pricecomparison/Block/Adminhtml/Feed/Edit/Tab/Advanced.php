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
 * Feed advanced edit form tab
 */
class NWT_Pricecomparison_Block_Adminhtml_Feed_Edit_Tab_Advanced extends Mage_Adminhtml_Block_Widget_Form
{    
    /**
     * prepare the form
     * @access protected
     * @return Pricecomparison_Feed_Block_Adminhtml_Feed_Edit_Tab_Advanced
     */
    protected function _prepareForm()
    {

        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('feed_');
        $form->setFieldNameSuffix('feed');
        $this->setForm($form);

        $feed = Mage::helper('pricecomparison')->getCurrentFeed($this);

        $fieldset = $form->addFieldset('feed_list', array('legend'=>Mage::helper('pricecomparison')->__('List products')));

        



        $fieldset->addField('list_outofstock', 'select', array(
            'label' => Mage::helper('pricecomparison')->__('List Out of Stock products'),
            'name'  => 'list_outofstock',
            'required'  => true,
            'class' => 'required-entry',
            'values'=> array(
                array(
                    'value' => 0,
                    'label' => Mage::helper('pricecomparison')->__('No'),
                ),
                array(
                    'value' => 1,
                    'label' => Mage::helper('pricecomparison')->__('Yes'),
                ),
            ),
        ));

        $fieldset->addField('list_disabled', 'select', array(
            'label' => Mage::helper('pricecomparison')->__('List Disabled products'),
            'name'  => 'list_disabled',
            'required'  => true,
            'class' => 'required-entry',
            'values'=> array(
                array(
                    'value' => 0,
                    'label' => Mage::helper('pricecomparison')->__('No'),
                ),
                array(
                    'value' => 1,
                    'label' => Mage::helper('pricecomparison')->__('Yes'),
                ),
            ),
        ));

       
        $opts = Mage_Catalog_Model_Product_Visibility::getAllOptions();
        unset($opts[0]);
        $fieldset->addField('list_visibility', 'multiselect', array(
            'label' => Mage::helper('pricecomparison')->__('List products with visibility set to'),
            'name'  => 'list_visibility',
            'required'  => true,
            'class' => 'required-entry',
            'values'=>  $opts,
            'note'=>'<span class="notice">'.Mage::helper('pricecomparison')->__('Note that, by default, the product url will not works for Disabled or Not Visible Individual products, magento will respond with "404 Not found"').'</span>'
        ));






        $fieldset->addField('list_types', 'multiselect', array(
            'label' => Mage::helper('pricecomparison')->__('List only this products'),
            'name'  => 'list_types',
            'required'  => true,
            'class' => 'required-entry',
            'values'=> Mage_Catalog_Model_Product_Type::getOptions(),
            'note'=>'<span class="notice">'.Mage::helper('pricecomparison')->__('For Grouped/Bundle product, the Store Price column will be populated with Minimal Price').'</span>'
        ));


        $fieldset = $form->addFieldset('feed_csv', array('legend'=>Mage::helper('pricecomparison')->__('CSV options')));

        $fieldset->addField('csv_header', 'select', array(
            'label' => Mage::helper('pricecomparison')->__('Add column names'),
            'name'  => 'csv_header',
            'required'  => true,
            'class' => 'required-entry',
            'values'=> array(
                array(
                    'value' => 1,
                    'label' => Mage::helper('pricecomparison')->__('Yes'),
                ),
                array(
                    'value' => 0,
                    'label' => Mage::helper('pricecomparison')->__('No'),
                ),
            ),
        ));


        $fieldset->addField('csv_enclosure', 'select', array(
            'label' => Mage::helper('pricecomparison')->__('Fields enclosed by'),
            'name'  => 'csv_enclosure',
            'required'  => true,
            'class' => 'required-entry',
            'values'=> array(
                array(
                    'value' => ord('"'),
                    'label' => Mage::helper('pricecomparison')->__('Double Quote'),
                ),
                array(
                    'value' => ord("'"),
                    'label' => Mage::helper('pricecomparison')->__("Single Quote"),
                )
            ),
        ));

        $fieldset->addField('csv_separator', 'select', array(
            'label' => Mage::helper('pricecomparison')->__('Fields separated by'),
            'name'  => 'csv_separator',
            'required'  => true,
            'class' => 'required-entry',
            'values'=> array(
                array(
                    'value' => ord("|"),
                    'label' => Mage::helper('pricecomparison')->__("| (pipe)"),
                ),
                array(
                    'value' => ord(','),
                    'label' => Mage::helper('pricecomparison')->__(', (comma)'),
                ),
                array(
                    'value' => ord(";"),
                    'label' => Mage::helper('pricecomparison')->__("; (semicolon)"),
                ),
                array(
                    'value' => ord("\t"),
                    'label' => Mage::helper('pricecomparison')->__("Tab"),
                ),
            ),
        ));



        $form->setValues($feed->getData());

        return parent::_prepareForm();
    }
}
