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

class Nybohansen_Pacsoft_Block_Adminhtml_Rates_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /* @var $_helper Nybohansen_Pacsoft_Helper_Rates */
    private $_helper;

    public function __construct()
    {
        $this->_helper = Mage::helper('pacsoft/rates');

        parent::__construct();

        // Set some defaults for our grid
        $this->setDefaultSort('rate_id');
        $this->setId('nybohansen_pacsoft_rates_grid');
        $this->setDefaultDir('asc');
        $this->setSaveParametersInSession(true);
        $this->_exportPageSize = 10000;

        $this->setEmptyText($this->__('No shipping rates found.'));

    }


    protected function _prepareColumns()
    {
        $helper = Mage::helper('pacsoft/rates');

        // Add the columns that should appear in the grid
        $this->addColumn('rate_id',
            array(
                'header'=> $this->__('ID'),
                'align' =>'right',
                'width' => '35px',
                'index' => 'rate_id',
                'filter_index' => 'main_table.rate_id'
            )
        );

        if (!Mage::app()->isSingleStoreMode()) {

            $this->addColumn('store_id', array(
                'header'        => $this->__('Store View'),
                'index'         => 'store_id',
                'type'          => 'store',
                'store_all'     => true,
                'store_view'    => true,
                'sortable'      => true,
                'renderer'  => 'pacsoft/adminhtml_rates_renderer_store',
                'filter_condition_callback' => array($this, '__filterStoreCondition'),
                'filter_index' => 'main_table.store_id'
            ));
        }

        $this->addColumn('country',
            array(
                'header'    => $this->__('Country'),
                'index'     => 'country',
                'type'      => 'country',
                'width'     => '150px',
                'renderer'  => 'pacsoft/adminhtml_rates_renderer_country',
                'filter_condition_callback' => array($this, '__filterCountryCondition')
            )
        );

        $this->addColumn('region',
            array(
                'header'=> $this->__('Region'),
                'index' => 'region',
                'width'     => '75px'
            )
        );

        $this->addColumn('city',
            array(
                'header'=> $this->__('City'),
                'index' => 'city',
                'width'     => '75px'
            )
        );

        $this->addColumn('zip_range',
            array(
                'header'=> $this->__('Zip range'),
                'index' => 'zip_range',
                'width'     => '75px',
                'filter_condition_callback' => array($this, '__filterZipCondition'),
            )
        );

        $this->addColumn('function', array(
            'header'=> $this->__('Function'),
            'align'     => 'left',
            'index'     => 'function',
            'type'      => 'options',
            'options'   => $helper->getConditionCodes(),
            'width'     => '190px'
        ));

        $this->addColumn('condition_range',
            array(
                'header'=> $this->__('Condition range'),
                'index' => 'condition_range',
                'width'     => '75px',
                'filter_condition_callback' => array($this, '__filterConditionRange'),
            )
        );

        $this->addColumn('shipment_type', array('header'=> $this->__('Shipment type'),
                                              'align'     => 'left',
                                              'width'     => '80px',
                                              'index'     => 'shipment_type',
                                              'type'      => 'options',
                                              'options'   => $helper->getServices(),
        ));

        $this->addColumn('addons', array('header'=> $this->__('Addons'),
            'align'     => 'left',
            'width'     => '80px',
            'index'     => 'addons',
            'type'      => 'options',
            'options'   => $helper->getAddons(),
            'renderer'  => 'pacsoft/adminhtml_rates_renderer_addon',
            'filter_condition_callback' => array($this, '__filterAddonCondition')
        ));

        $this->addColumn('price', array(
            'header'        => $this->__('Price'),
            'align'         => 'left',
            'index'         => 'price',
            'type'          => 'price',
            'currency_code' => $this->_helper->getStore()->getBaseCurrency()->getCode(),
            'default'       => '0.00',
            'width'     => '75px'
        ));

        $this->addColumn('title',
            array(
                'header'=> $this->__('Title'),
                'index' => 'title',
                'width'     => '75px'
            )
        );

        $this->addColumn('sort_order',
            array(
                'header'=> $this->__('Sort order'),
                'index' => 'sort_order',
                'width'     => '30px'
            )
        );

        $this->addColumn('status', array(
            'header'=> $this->__('Status'),
            'align'     => 'left',
            'width'     => '80px',
            'index'     => 'status',
            'type'      => 'options',
            'options'   => array('1' => $this->__('Enabled'), '0' => $this->__('Disabled')),
        ));

        return parent::_prepareColumns();
    }


    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('rate_id');
        $this->getMassactionBlock()->setFormFieldName('rate_id');
        $this->getMassactionBlock()->addItem('delete', array(
            'label'       => $this->__('Delete'),
            'url'         => $this->getUrl('*/*/massDelete'),
            'confirm'     => $this->__('Are you sure?')
        ));
        return $this;
    }

    public function getRowUrl($row)
    {
        // This is where our row data will link to
        return $this->getUrl('*/*/edit', array('rate_id' => $row->getId()));
    }

    protected function _prepareCollection()
    {
        // Get and set our collection for the grid
        $rates = Mage::getModel('pacsoft/rates')->getCollection();

        $this->setCollection($rates);

        return parent::_prepareCollection();
    }

    protected function __filterStoreCondition($collection, $column){

        if (!$value = $column->getFilter()->getValue()) {
            return;
        }

        $collection->addStoreFilter($value);

        $this->setCollection($collection);
    }

    protected function __filterCountryCondition($collection, $column){
        if (!$value = $column->getFilter()->getValue()) {
            return;
        }

        $collection->addCountryFilter($value);
        $this->setCollection($collection);

    }


    protected function __filterZipCondition($collection, $column){

        if (!$value = $column->getFilter()->getValue()) {
            return;
        }

        $collection->addZipFilter($value);

        $this->setCollection($collection);
    }

    protected function __filterConditionRange($collection, $column){

        if (!$value = $column->getFilter()->getValue()) {
            return;
        }

        $collection->addConditionRangeFilter($value);

        $this->setCollection($collection);
    }

    protected function __filterAddonCondition($collection, $column){

        if (!$value = $column->getFilter()->getValue()) {
            return;
        }

        $collection->addAddonsFilter($value);

        $this->setCollection($collection);
    }


}