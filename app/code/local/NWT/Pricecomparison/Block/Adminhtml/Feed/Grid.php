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
 * Feed admin grid block
 *
 */
class NWT_Pricecomparison_Block_Adminhtml_Feed_Grid extends Mage_Adminhtml_Block_Widget_Grid{
    /**
     * constructor
     * @access public
     * @return void
     */
    public function __construct(){
        parent::__construct();
        $this->setId('feedGrid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }
    /**
     * prepare collection
     * @access protected
     * @return NWT_Pricecomparison_Block_Adminhtml_Feed_Grid
     */
    protected function _prepareCollection(){
        $collection = Mage::getModel('pricecomparison/feed')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
    /**
     * prepare grid collection
     * @access protected
     * @return NWT_Pricecomparison_Block_Adminhtml_Feed_Grid
     */
    protected function _prepareColumns(){
        $this->addColumn('entity_id', array(
            'header'    => Mage::helper('pricecomparison')->__('Id'),
            'index'        => 'entity_id',
            'type'        => 'number'
        ));
        $this->addColumn('name', array(
            'header'=> Mage::helper('pricecomparison')->__('Name'),
            'index' => 'name',
            'type'         => 'text',

        ));
        $this->addColumn('store_id', array(
            'header'=> Mage::helper('pricecomparison')->__('Store'),
            'index' => 'store_id',
            'type'        => 'options',
            'options'    => Mage::getResourceSingleton('core/store_collection')->toOptionHash(),

        ));
        $this->addColumn('feed_type', array(
            'header'=> Mage::helper('pricecomparison')->__('Feed Type'),
            'index' => 'feed_type',
            'type'        => 'options',
            'options'    => Mage::getSingleton('pricecomparison/source_feed')->toOptionHash()

        ));

        $this->addColumn('filename', array(
            'header'=> Mage::helper('pricecomparison')->__('Filename'),
            'index' => 'filename',
            //'type'         => 'text',
            'renderer'  => 'NWT_Pricecomparison_Block_Adminhtml_Feed_Grid_Filename',
        ));

        $this->addColumn('status', array(
            'header'    => Mage::helper('pricecomparison')->__('Status'),
            'index'        => 'status',
            'type'        => 'options',
            'options'    => array(
                '1' => Mage::helper('pricecomparison')->__('Enabled'),
                '0' => Mage::helper('pricecomparison')->__('Disabled'),
            )
        ));
        $this->addColumn('created_at', array(
            'header'    => Mage::helper('pricecomparison')->__('Created at'),
            'index'     => 'created_at',
            'width'     => '120px',
            'type'      => 'datetime',
        ));
        $this->addColumn('updated_at', array(
            'header'    => Mage::helper('pricecomparison')->__('Updated at'),
            'index'     => 'updated_at',
            'width'     => '120px',
            'type'      => 'datetime',
        ));
        $this->addColumn('action',
            array(
                'header'=>  Mage::helper('pricecomparison')->__('Action'),
                'width' => '100',
                'type'  => 'action',
                'getter'=> 'getId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('pricecomparison')->__('Generate NOW'),
                        'url'   => array('base'=> '*/*/generate'),
                        'field' => 'id'
                    ),
                    array(
                        'caption'   => Mage::helper('pricecomparison')->__('Edit'),
                        'url'   => array('base'=> '*/*/edit'),
                        'field' => 'id'
                    ),
                ),
                'filter'=> false,
                'is_system'    => true,
                'sortable'  => false,
        ));
//         $this->addExportType('*/*/exportCsv', Mage::helper('pricecomparison')->__('CSV'));
//         $this->addExportType('*/*/exportExcel', Mage::helper('pricecomparison')->__('Excel'));
//         $this->addExportType('*/*/exportXml', Mage::helper('pricecomparison')->__('XML'));
        return parent::_prepareColumns();
    }
    /**
     * prepare mass action
     * @access protected
     * @return NWT_Pricecomparison_Block_Adminhtml_Feed_Grid
     */
    protected function _prepareMassaction(){
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('feed');

        $this->getMassactionBlock()->addItem('generate', array(
            'label'=> Mage::helper('pricecomparison')->__('Generate'),
            'url'  => $this->getUrl('*/*/massGenerate')
        ));
        $this->getMassactionBlock()->addItem('delete', array(
            'label'=> Mage::helper('pricecomparison')->__('Delete'),
            'url'  => $this->getUrl('*/*/massDelete'),
            'confirm'  => Mage::helper('pricecomparison')->__('Are you sure?')
        ));
        $this->getMassactionBlock()->addItem('status', array(
            'label'=> Mage::helper('pricecomparison')->__('Change status'),
            'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
            'additional' => array(
                'status' => array(
                        'name' => 'status',
                        'type' => 'select',
                        'class' => 'required-entry',
                        'label' => Mage::helper('pricecomparison')->__('Status'),
                        'values' => array(
                                '1' => Mage::helper('pricecomparison')->__('Enabled'),
                                '0' => Mage::helper('pricecomparison')->__('Disabled'),
                        )
                )
            )
        ));

        return $this;
    }
    /**
     * get the row url
     * @access public
     * @param NWT_Pricecomparison_Model_Feed
     * @return string
     */
    public function getRowUrl($row){
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }
    /**
     * get the grid url
     * @access public
     * @return string
     */
    public function getGridUrl(){
        return $this->getUrl('*/*/grid', array('_current'=>true));
    }
}