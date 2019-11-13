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
 * @package    Nybohansen_Economic2
 * @copyright  Copyright (c) 2014 Nybohansen ApS
 * @license    LICENSE.txt
 */

require_once('Mage/Adminhtml/Controller/Action.php');

class Nybohansen_Economic2_Adminhtml_Sales_OrderInfoGridController extends Mage_Adminhtml_Controller_Action
{

    /**
     * Initialize action
     *
     * Here, we set the breadcrumbs and the active menu
     *
     * @return Mage_Adminhtml_Controller_Action
     */
    protected function _initAction()
    {
        $this->loadLayout()
        // Make the active menu match the menu config nodes (without 'children' inbetween)
            ->_setActiveMenu('sales/order')
            ->_addBreadcrumb($this->__('Sales'), $this->__('Sales'))
            ->_addBreadcrumb($this->__('e-conomic orders'), $this->__('e-conomic orders'));
        return $this;
    }


    public function indexAction()
    {
        // Let's call our initAction method which will set some basic params for each action
        $this->_initAction()->renderLayout();
    }

    /**
     * Order grid
     */
    public function gridAction()
    {
        $this->loadLayout(false);
        $this->renderLayout();
    }

    public function downloadOrderPdfAction(){
        $economic_order_id = $this->getRequest()->getParam('economic_order_id');
        $storeId = $this->getRequest()->getParam('storeid');

        /**
         * @var $eapi Nybohansen_Economic2_Model_Eapi
         */
        $eapi = Mage::getModel('economic2/eapi');
        $connection_result = $eapi->connect($storeId);
        $economic_order_handle = $eapi->order_get_by_number($economic_order_id);
        $content = $eapi->order_get_pdf($economic_order_handle);
        return $this->_prepareDownloadResponse('order_'.$economic_order_id.'.pdf', $content, 'application/pdf');

    }

    public function downloadCurrentInvoicePdfAction(){
        $economic_current_invoice_id = $this->getRequest()->getParam('economic_current_invoice_id');
        $storeId = $this->getRequest()->getParam('storeid');

        /**
         * @var $eapi Nybohansen_Economic2_Model_Eapi
         */
        $eapi = Mage::getModel('economic2/eapi');
        $connection_result = $eapi->connect($storeId);

        $content = $eapi->current_invoice_get_pdf(array('Id' => $economic_current_invoice_id));
        return $this->_prepareDownloadResponse('current_invoice_'.$economic_current_invoice_id.'.pdf', $content, 'application/pdf');
    }

    public function downloadInvoicePdfAction(){
        $economic_invoice_id = $this->getRequest()->getParam('economic_invoice_id');
        $storeId = $this->getRequest()->getParam('storeid');

        /**
         * @var $eapi Nybohansen_Economic2_Model_Eapi
         */
        $eapi = Mage::getModel('economic2/eapi');
        $connection_result = $eapi->connect($storeId);

        $content = $eapi->invoice_get_pdf($eapi->invoice_find_by_number($economic_invoice_id));
        return $this->_prepareDownloadResponse('invoice_'.$economic_invoice_id.'.pdf', $content, 'application/pdf');
    }

    public function massSendOrdersToEconomicAction(){
        $responsIds = '';
        $orderIds = $this->getRequest()->getPost('order_ids', array());

        foreach ($orderIds as $orderId) {

            $economicOrder = Mage::getSingleton('economic2/economicOrder');
            $order = Mage::getModel('sales/order')->load($orderId);

            if($economicOrder->create_order($order)){
                $responsIds .= $order->getIncrementId().', ';
            }
        }
        trim($responsIds, ', ');

        $this->_getSession()->addSuccess($this->__('Orders [%s] sent to e-conomic as order', $responsIds));

        $this->_redirect('adminhtml/sales_orderInfoGrid/index', array('_current' => true));
    }

    public function massSendOrdersToEconomicAsCurrentInvoiceAction(){
        $responsIds = '';
        $orderIds = $this->getRequest()->getPost('order_ids', array());

        foreach ($orderIds as $orderId) {

            $economicOrder = Mage::getSingleton('economic2/economicOrder');
            $order = Mage::getModel('sales/order')->load($orderId);

            if($economicOrder->create_invoice($order)){
                $responsIds .= $order->getIncrementId().', ';
            }
        }
        trim($responsIds, ', ');

        $this->_getSession()->addSuccess($this->__('Orders [%s] sent to e-conomic as current invoice', $responsIds));

        $this->_redirect('adminhtml/sales_orderInfoGrid/index', array('_current' => true));
    }

    public function massSendOrdersToEconomicAsCurrentInvoiceAndBookAction(){
        $successIds = array();
        $warningIds = array();
        $orderIds = $this->getRequest()->getPost('order_ids', array());

        foreach ($orderIds as $orderId) {

            $economicOrder = Mage::getSingleton('economic2/economicOrder');
            $order = Mage::getModel('sales/order')->load($orderId);

            if($economicOrder->create_invoice($order, false, true)){
                $successIds[] = $order->getIncrementId();
            }else{
                $warningIds[] = $order->getIncrementId();
            }
        }

        $this->_getSession()->addSuccess($this->__('Orders [%s] sent to e-conomic and booked', implode(',', $successIds)));
        $this->_getSession()->addError($this->__('Orders [%s] could not be sent to e-conomic and booked', implode(',', $warningIds)));

        $this->_redirect('adminhtml/sales_orderInfoGrid/index', array('_current' => true));
    }

}