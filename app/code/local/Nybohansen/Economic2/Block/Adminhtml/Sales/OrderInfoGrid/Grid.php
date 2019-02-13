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

class Nybohansen_Economic2_Block_Adminhtml_Sales_OrderInfoGrid_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {

        parent::__construct();

        // Set some defaults for our grid
        $this->setDefaultSort('rate_id');
        $this->setId('nybohansen_economic2_orderinfogrid_grid');
        $this->setDefaultSort('created_at');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setEmptyText($this->__('No orders found.'));

    }

    /**
     * Retrieve collection class
     *
     * @return string
     */
    protected function _getCollectionClass()
    {
        return 'sales/order_grid_collection';
    }

    protected function _prepareCollection()
    {

        $resource = Mage::getSingleton('core/resource');
        $collection = Mage::getResourceModel($this->_getCollectionClass());
        $tableName = $resource->getTableName('economic2/orderStatus');
//        $collection->getSelect()->joinLeft($tableName, $tableName.".magento_order_id = main_table.increment_id", $tableName.".*");
        $collection->getSelect()->joinLeft(array('eco'=>$tableName),'eco.magento_order_id=main_table.increment_id',array('eco.economic_order_id', 'eco.economic_current_invoice_id', 'eco.integrity_check', 'eco.economic_debtor_id', 'eco.economic_invoice_id', 'eco.credit_order_id', 'eco.credit_invoice_id'))->group(array('main_table.increment_id')); // New
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }



    protected function _prepareColumns()
    {
        $this->addColumn('real_order_id', array(
            'header'=> Mage::helper('sales')->__('Order #'),
            'width' => '80px',
            'type'  => 'text',
            'index' => 'increment_id',
        ));

        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('store_id', array(
                'header'    => Mage::helper('sales')->__('Purchased From (Store)'),
                'index'     => 'store_id',
                'type'      => 'store',
                'store_view'=> true,
                'display_deleted' => true,
            ));
        }

        $this->addColumn('created_at', array(
            'header' => Mage::helper('sales')->__('Purchased On'),
            'index' => 'created_at',
            'type' => 'datetime',
            'width' => '100px',
        ));

        $this->addColumn('billing_name', array(
            'header' => Mage::helper('sales')->__('Bill to Name'),
            'index' => 'billing_name',
        ));


        $this->addColumn('grand_total', array(
            'header' => Mage::helper('sales')->__('G.T. (Purchased)'),
            'index' => 'grand_total',
            'type'  => 'currency',
            'currency' => 'order_currency_code',
        ));

        $this->addColumn('status', array(
            'header' => Mage::helper('sales')->__('Status'),
            'index' => 'status',
            'type'  => 'options',
            'width' => '70px',
            'options' => Mage::getSingleton('sales/order_config')->getStatuses(),
        ));

        $this->addColumn('Synced', array(
            'header'        => $this->__('Transfered'),
            'align'     => 'left',
            'type'      => 'options',
            'options'   => array(1 => $this->__('Yes'), 0 => $this->__('No')),
            'renderer'  => 'economic2/adminhtml_sales_orderInfoGrid_renderer_synced',
            'filter_condition_callback' => array($this, '__filterSynced'),
            'width'     => '190px'
        ));

        $this->addColumn('Integrity', array(
            'index'     => 'eco.integrity_check',
            'header'    => $this->__('Integrity check'),
            'align'     => 'left',
            'type'      => 'options',
            'options'   => array(1 => $this->__('Passed'), 0 => $this->__('Failed')),
            'renderer'  => 'economic2/adminhtml_sales_orderInfoGrid_renderer_integrity',
            'filter_condition_callback' => array($this, '__filterIntegrity'),
            'width'     => '190px'
        ));


        $this->addColumn('EconomicDebtorNumber', array(
            'index'         => 'eco.economic_debtor_id',
            'renderer'  => 'economic2/adminhtml_sales_orderInfoGrid_renderer_debtor',
            'header'        => $this->__('e-conomic:</br> debtor number')));

        $this->addColumn('EconomicOrderNumber', array(
            'index'         => 'eco.economic_order_id',
            'renderer'  => 'economic2/adminhtml_sales_orderInfoGrid_renderer_order',
            'filter_condition_callback' => array($this, '__filterOrderNumber'),
            'header'        => $this->__('e-conomic:</br> order number')));

        $this->addColumn('EconomicInvoiceNumber', array(
            'index'         => 'eco.economic_invoice_id',
            'renderer'  => 'economic2/adminhtml_sales_orderInfoGrid_renderer_invoice',
            'filter_condition_callback' => array($this, '__filterInvoiceNumber'),
            'header'        => $this->__('e-conomic:</br> invoice number')
            ));

        return $this;

    }

    public function getRowUrl($row)
    {
        if (Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/view')) {
            return $this->getUrl('*/sales_order/view', array('order_id' => $row->getId()));
        }
        return false;
    }

    protected function __filterSynced($collection, $column){
        $value = $column->getFilter()->getValue();
        if($value){
            $collection->addFieldToFilter('eco.economic_order_id', array("neq"=>''));
        }else{
            $collection->addFieldToFilter('eco.economic_order_id', array("eq"=>''));
        }
        $this->setCollection($collection);
    }

    protected function __filterIntegrity($collection, $column){
        $value = $column->getFilter()->getValue();
        $collection->addFieldToFilter('eco.integrity_check', array("eq"=> $value));
        $this->setCollection($collection);
    }

    protected function __filterOrderNumber($collection, $column){
        $value = $column->getFilter()->getValue();
        $collection->getSelect()->where("eco.economic_order_id = '".$value."' or eco.credit_order_id  = '".$value."'");
        $this->setCollection($collection);
    }

    protected function __filterInvoiceNumber($collection, $column){
        $value = $column->getFilter()->getValue();
        $collection->getSelect()->where("eco.economic_invoice_id = '".$value."' or eco.credit_invoice_id  = '".$value."'");
        $this->setCollection($collection);
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('economic_order_info_order_id');
        $this->getMassactionBlock()->setFormFieldName('order_ids');

        $this->getMassactionBlock()->addItem('Send to e-conomic as order',
                                              array('label' => Mage::helper('economic2')->__('Send to e-conomic as order'),
                                              'url'   => Mage::app()->getStore()->getUrl('*/sales_orderInfoGrid/massSendOrdersToEconomic', array('_current'=>true))
        ));

        $this->getMassactionBlock()->addItem('Send to e-conomic as current invoice',
                                              array('label' => Mage::helper('economic2')->__('Send to e-conomic as current invoice'),
                                              'url'   => Mage::app()->getStore()->getUrl('*/sales_orderInfoGrid/massSendOrdersToEconomicAsCurrentInvoice', array('_current'=>true))
        ));

        $this->getMassactionBlock()->addItem('Send to e-conomic and book invoice',
                                              array('label' => Mage::helper('economic2')->__('Send to e-conomic as invoice and book'),
                                              'url'   => Mage::app()->getStore()->getUrl('*/sales_orderInfoGrid/massSendOrdersToEconomicAsCurrentInvoiceAndBook', array('_current'=>true))
        ));

        return $this;
    }

}