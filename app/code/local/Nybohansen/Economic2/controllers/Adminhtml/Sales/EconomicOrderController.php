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

require_once('Mage/Adminhtml/controllers/Sales/OrderController.php');

class Nybohansen_Economic2_Adminhtml_Sales_EconomicOrderController extends Mage_Adminhtml_Sales_OrderController
{
	
    protected function _construct()
    {
    	parent::_construct();
    }
    
    /**
     * Send the orders to e-conomic
     */
    public function massSendOrdersToEconomicAction()
    {

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
    	
    	
    	$this->_getSession()->addSuccess($this->__('Orders [%s] sent to e-conomic', $responsIds));

        $this->_redirect('adminhtml/sales_order/index', array('_current' => true));
		
    }

    /**
     * Send the orders to e-conomic
     */
    public function sendOrderToEconomicAction()
    {
        $orderId = $this->getRequest()->getParam('order_id');

        $economicOrder = Mage::getSingleton('economic2/economicOrder');
        $order = Mage::getModel('sales/order')->load($orderId);


        if($economicOrder->create_order($order)){
            $this->_getSession()->addSuccess($this->__('Order [%s] sent to e-conomic', $order->getIncrementId()));
        }else{
            $this->_getSession()->addSuccess($this->__('An error occurred, order [%s] could not be sent to e-conomic', $order->getIncrementId()));
        }
        $this->_redirect('adminhtml/sales_order/view', array('order_id' => $order->getId()));

    }

    /**
     * Send the orders to e-conomic
     */
    public function sendCreditMemoToEconomicAction()
    {
        $creditMemoId = $this->getRequest()->getParam('creditmemo_id');
        $creditmemo = Mage::getModel('sales/order_creditmemo')->load($creditMemoId);
        $order = Mage::getModel('sales/order')->loadByIncrementId($creditmemo->getOrder()->getData('increment_id'));
        $economicOrder = Mage::getSingleton('economic2/economicOrder');

        if($economicOrder->create_order($order, $creditmemo->getIncrementId())){
            $this->_getSession()->addSuccess($this->__('Creditmemo [%s] sent to e-conomic', $creditmemo->getIncrementId()));
        }else{
            $this->_getSession()->addSuccess($this->__('An error occurred, creditmemo [%s] could not be sent to e-conomic', $creditmemo->getIncrementId()));
        }

        $this->_redirect('adminhtml/sales_order_creditmemo/view', array('creditmemo_id' => $creditmemo->getId()));

    }


}
