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
 * @package    Nybohansen_Pacsoft
 * @copyright  Copyright (c) 2014 Nybohansen ApS
 * @license    LICENSE.txt
 */

class Nybohansen_Pacsoft_Block_Adminhtml_Rates_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Init class
     */
    public function __construct()
    {
        parent::__construct();

        $this->setId('pacsoft_rates_form');
        $this->setTitle($this->__('Rate Information'));
    }


    /**
     * Setup form fields for inserts/updates
     *
     * return Mage_Adminhtml_Block_Widget_Form
     */
    protected function _prepareForm()
    {

        $helper = Mage::helper('pacsoft/rates');

        $rateId = $this->getRequest()->getParam('rate_id');
        $model = Mage::registry('pacsoft_rate')->load($rateId);


        $form = new Varien_Data_Form(array(
            'id'        => 'edit_form',
            'action'    => $this->getUrl('*/*/save', array('rate_id' => $rateId)),
            'method'    => 'post'
        ));

        $fieldset = $form->addFieldset('base_fieldset', array(
            'legend'    => $this->__('Rate Information'),
            'class'     => 'fieldset-wide',
        ));

        if (!Mage::app()->isSingleStoreMode()) {
            $fieldset->addField('store_id', 'multiselect', array(
                'name' => 'stores[]',
                'label' => $this->__('Store View'),
                'title' => $this->__('Store View'),
                'required' => true,
                'value' => $model->getStoresAsArray(),
                'values' => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true),
            ));
        }else {
            $fieldset->addField('store_id', 'hidden', array(
                'name' => 'stores[]',
                'value' => Mage::app()->getStore(true)->getId()
            ));
        }

        $fieldset->addField('country', 'multiselect', array(
            'name'  => 'country',
            'label'     => $this->__('Country'),
            'required'  => true,
            'value' => $model->getCountry(),
            'values'    => Mage::getModel('adminhtml/system_config_source_country')->toOptionArray(),
        ));


        $fieldset->addField('region', 'text', array(
            'name'      => 'region',
            'label'     => $this->__('Region'),
            'title'     => $this->__('Region'),
            'value'     => $model->getRegion(),
        ));

        $fieldset->addField('city', 'text', array(
            'name'      => 'city',
            'label'     => $this->__('City'),
            'title'     => $this->__('City'),
            'value'     => $model->getCity(),
        ));

        $fieldset->addField('zip_range', 'text', array(
            'name'      => 'zip_range',
            'label'     => $this->__('Zip range'),
            'title'     => $this->__('Zip range'),
            'value'     => $model->getZipRange(),
        ));

        $fieldset->addField('function', 'select', array(
            'name'      => 'function',
            'label'     => $this->__('Function'),
            'values'    => $helper->getConditionCodes(),
            'required'  => true,
            'value'     => $model->getFunction(),
        ));

        $fieldset->addField('condition_range', 'text', array(
            'name'      => 'condition_range',
            'label'     => $this->__('Condition range'),
            'title'     => $this->__('Condition range'),
            'value'     => $model->getConditionRange(),
        ));

        $event = $fieldset->addField('shipment_type', 'select', array(
                                        'name'      => 'shipment_type',
                                        'label'     => $this->__('Shipment type'),
                                        'values'    => $helper->getServices(),
                                        'value'     => $model->getShipmentType(),
                                        'required'  => true,
                                        'onchange'  => 'loadAddOns(this)'
        ));

        $event->setAfterElementHtml("<script type=\"text/javascript\">
                                        function loadAddOns(selectElement){
                                            var reloadurl = '". Mage::helper('adminhtml')->getUrl('adminhtml/pacsoftRates/getAddonsForService', array('' => ''))."';
                                            new Ajax.Request(reloadurl, {
                                                method: 'POST',
                                                parameters: { serviceCode: selectElement.value,
                                                              rateId: '".$rateId."'},
                                                onComplete: function(transport) {
                                                    $('addons').update(transport.responseText.evalJSON());
                                                }
                                            });
                                        }
                                    </script>");


        $addonsBlock = Mage::getSingleton('core/layout')->createBlock('pacsoft/adminhtml_addons');
        $addonsBlock->setData('serviceCode', $model->getShipmentType());
        $addonsBlock->setData('rateId', $rateId);


        $fieldset->addField('addons', 'note', array(
            'label'     => $this->__('Addons'),
            'text' => $addonsBlock->toHtml()
        ));


        $fieldset->addField('price', 'text', array(
            'name'      => 'price',
            'label'     => $this->__('Price'),
            'title'     => $this->__('Price'),
            'required'  => true,
            'value'     => $model->getPrice(),
        ));

        $fieldset->addField('title', 'text', array(
            'name'      => 'title',
            'label'     => $this->__('Title'),
            'title'     => $this->__('Title'),
            'required'  => true,
            'value'     => $model->getTitle(),
        ));

        $fieldset->addField('sort_order', 'text', array(
            'name'      => 'sort_order',
            'label'     => $this->__('Sort order'),
            'title'     => $this->__('Sort order'),
            'value'     => $model->getSortOrder(),
        ));

        $fieldset->addField('status', 'select', array(
            'name'      => 'status',
            'label'     => $this->__('Status'),
            'values' => array('1' => $this->__('Enabled'), '0' => $this->__('Disabled')),
            'value' => $model->getStatus(),
            'required'  => true,
        ));



        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}