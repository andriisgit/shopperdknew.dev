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

class Nybohansen_Pacsoft_Block_Adminhtml_ShippingInfoBox extends Mage_Adminhtml_Block_Template
{

    public $_pacsoftOrder;

    public function __construct()
    {
        parent::__construct();
        $this->_pacsoftOrder = $this->getOrder();
        if(!$this->_pacsoftOrder->getIsVirtual()){
            //Only show shipping box, if the order is not completely virtual
            $this->setTemplate('pacsoft/shipppingInfoBox.phtml');
        }
    }

    public function getServices(){
        return Mage::helper('pacsoft/rates')->getServices();
    }

    public function chosenService(){
        return $this->_pacsoftOrder->getService();
    }

    public function getCreateLabelButton()
    {
        return $this->getLayout()
            ->createBlock('adminhtml/widget_button')
            ->setData(array(
            'label'   => Mage::helper('sales')->__('Create Shipping Label'),
            'onclick' => 'pacsoftPrintLabel();',
        ))->toHtml();
    }

    public function getCreateReturnLabelButton()
    {
        return $this->getLayout()
            ->createBlock('adminhtml/widget_button')
            ->setData(array(
            'label'   => Mage::helper('sales')->__('Create Return Shipping Label'),
            'onclick' => 'pacsoftPrintLabel(true);',
        ))->toHtml();
    }

    public function getOrderId(){
        return Mage::app()->getRequest()->getParam('order_id');
    }

    public function getAddonsHtml(){
        $block = Mage::getSingleton('core/layout')->createBlock('pacsoft/adminhtml_orderAddons');
        $block->setData('serviceCode', $this->chosenService());
        $block->setData('orderId', $this->_pacsoftOrder->getId());
        return $block->toHtml();
    }

    public function getOrder(){
        return Mage::getModel('pacsoft/pacsoftOrder', $this->getOrderId());
    }

}