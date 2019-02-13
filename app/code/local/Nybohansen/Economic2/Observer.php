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

class Nybohansen_Economic2_Observer extends Mage_Adminhtml_Controller_Action{

	
	/**
	* @var Nybohansen_Economic2_Model_EconomicOrder
	*/
	private $economicOrder;
	
	public function __construct(){
		$this->economicOrder = Mage::getSingleton('economic2/economicOrder');
	}
		
	public function sales_order_address_save_after($observer){
        if(!$this->economicOrder->save_flag){
            $address = $observer->getEvent()->getAddress();
            $order = Mage::getModel('sales/order')->load($address->getParent_id());
            $this->economicOrder->change_address($order, $address);
        }
	}


    public function sales_order_creditmemo_save_after($observer){
        if ($this->economicOrder->save_flag) {
            $this->economicOrder->save_flag = false;
        }else{
            $creditmemo = $observer->getEvent()->getCreditmemo();
            $storeId = $creditmemo->getStoreId();
            if($this->IsModuleActiveOnStore($storeId)){
                if(mage::getStoreConfig('economic2_options/orderConfig/sync_credit_memo', $storeId)){

                    $order = Mage::getModel('sales/order')->loadByIncrementId($creditmemo->getOrder()->getData('increment_id'));
                    $creditMemoIncrementId = $creditmemo->getData('increment_id');

                    if(mage::getStoreConfig('economic2_options/orderConfig/sync_credit_as_invoice', $storeId)){
                        $this->economicOrder->create_invoice($order, $creditMemoIncrementId);
                    }else{
                        $this->economicOrder->create_order($order, $creditMemoIncrementId);
                    }
                }
            }

        }
    }


	public function sales_order_save_after($observer){
        if ($this->economicOrder->save_flag) {
			$this->economicOrder->save_flag = false;
		}else{
            $order = $observer->getEvent()->getOrder();
            $storeId = $order->getStoreId();

            if($this->IsModuleActiveOnStore($storeId)){
                if($order->getStatus()){
                    $relation = Mage::getModel('economic2/orderStatus')->load($order->getIncrementId(), 'magento_order_id');
                    if($order->getStatus() == mage::getStoreConfig('economic2_options/orderConfig/create_order_status', $storeId)) {
                        if (!$relation->getEconomic_order_id()) {
                            //Only create order if the webservice is available and we haven't created the order before
                            $this->economicOrder->create_order($order);
                        }
                    }

                    if($order->getStatus() == mage::getStoreConfig('economic2_options/orderConfig/delete_order_status', $storeId)) {
                        //We can only delete orders we have created
                        if ($relation['economic_order_id']) {
                            $this->economicOrder->cancel_order($order);
                        }
                    }

                    if($order->getStatus() == mage::getStoreConfig('economic2_options/orderConfig/create_invoice_status', $storeId)) {
                        //Check if invoice has already been created
                        if (!$relation->getEconomic_current_invoice_id()) {
                            $this->economicOrder->create_invoice($order);
                        }
                    }
                }
			}			
		}

	}
	
	public function catalog_product_save_after($observer){
        //Called when product is created or updated
        $product = $observer->getEvent()->getProduct();
        $storeId = $product->getStoreId();
        if($this->IsModuleActiveOnStore($storeId)){
			if(mage::getStoreConfig('economic2_options/productConfig/product_creation', $storeId) == 0 ){
				//Create/update product in e-conomic
				$this->economicOrder->create_product($product, true);
			}
		}
	}
	
	public function catalog_product_delete_after_done($observer){
		//Called when product is deleted
        $product = $observer->getEvent()->getProduct();
        $storeId = $product->getStoreId();
        if($this->IsModuleActiveOnStore($storeId)){
			if(mage::getStoreConfig('economic2_options/productConfig/product_deletion', $storeId)){
				//Delete product from e-conomic
				$this->economicOrder->delete_product($product);
			}
		}
	}

    public function customer_save_after($observer){
        $magentoCustomer = $observer->getEvent()->getCustomer();
        $storeId = $magentoCustomer->getStore()->getId();

        if($this->IsModuleActiveOnStore($storeId)){
            if(mage::getStoreConfig('economic2_options/debtorConfig/debtor_create_new', $storeId) == 1){
                $relation = Mage::getModel('economic2/customerId')->load($magentoCustomer->getId(), 'magento_customer_id');
                if($relation->economic_customer_id){
                    //We should only update customer information if there is a one-to-one relation
                    $customer = Nybohansen_Economic2_Model_EconomicDebtor::fromMagentoCustomer($magentoCustomer);
                    $customer->sendToEconomic();
                }
            }
        }

    }

    public function customer_address_save_after($observer){
        //this is only called from frontend, when user edits the addresses
        $magentoCustomerAddress = $observer->getEvent()->getCustomerAddress();
        $storeId = $magentoCustomerAddress->getStoreId();
        if($this->IsModuleActiveOnStore($storeId)){
            if(mage::getStoreConfig('economic2_options/debtorConfig/debtor_create_new', $storeId) == 1){
                //We should only update customer information if there is a one-to-one relation, and the customer
                //already has been transferred to e-conomic
                if(isset($magentoCustomerAddress['customer_id'])){
                    $relation = Mage::getModel('economic2/customerId')->load($magentoCustomerAddress['customer_id'], 'magento_customer_id');
                    if($relation->economic_customer_id){
                        //Only save the address to the debtor, if the address is really adited from My Account page on frontend
                        $deliveryLocation = Nybohansen_Economic2_Model_EconomicDeliveryLocation::fromMagentoCustomerAddress($magentoCustomerAddress);
                        $deliveryLocation->sendToEconomic();
                    }
                }
            }
        }
    }

    public function customer_address_delete_after($observer){
        $magentoCustomer = $observer->getEvent()->getCustomer();
        $storeId = $magentoCustomer->getStore()->getId();

        if($this->IsModuleActiveOnStore($storeId)){
            if(mage::getStoreConfig('economic2_options/debtorConfig/debtor_create_new', $storeId) == 1){
                //We should only update customer information if there is a one-to-one relation
                $deliveryLocation = Nybohansen_Economic2_Model_EconomicDeliveryLocation::fromMagentoCustomerAddress($observer->getEvent()->getCustomerAddress());
                $deliveryLocation->deleteFromEconomic();
            }
        }
    }


	//Adds the combo-box entries that contains the mass actions
	public function addMassAction($observer)
	{

		$block = $observer->getEvent()->getBlock();

        $storeId = Mage::app()->getRequest()->getParam('store');

        if($this->IsModuleActiveOnStore($storeId)){

            if(preg_match('/_Widget_Grid_Massaction$/', get_class($block))){

                if ($block->getRequest()->getControllerName() == 'sales_order' || $block->getRequest()->getControllerName() == 'adminhtml_sales_order')
                {
                        $block->addItem($this->__('Send orders to e-conomic'),
                                        array('label' => Mage::helper('economic2')->__('Send orders to e-conomic'),
                                              'url'   => Mage::app()->getStore()->getUrl('adminhtml/sales_economicOrder/massSendOrdersToEconomic', array('_current'=>true))));
                }

                //Add entry in the catalog grid mass action
                if ($block->getRequest()->getControllerName() == 'catalog_product' || $block->getRequest()->getControllerName() == 'adminhtml_product'){
                        $block->addItem($this->__('Send products to e-conomic'),
                                        array('label' => Mage::helper('economic2')->__('Send products to e-conomic'),
                                              'url'   => Mage::app()->getStore()->getUrl('adminhtml/catalog_economicProduct/massSendProductToEconomic', array('_current'=>true))));

                        $block->addItem($this->__('Update products from e-conomic'),
                                        array('label' => Mage::helper('economic2')->__('Update products from e-conomic'),
                                              'url'   => Mage::app()->getStore()->getUrl('adminhtml/catalog_economicProduct/massUpdateProductsFromEconomic', array('_current'=>true))));
                }
                //Add entry in the customer grid mass action
                if ($block->getRequest()->getControllerName() == 'customer'){
                    if(mage::getStoreConfig('economic2_options/debtorConfig/debtor_create_new', $storeId) == 1){
                        //We should only update customer information if there is a one-to-one relation between customers in
                        //Magento and debtors in e-conomic
                        $block->addItem($this->__('Send customers to e-conomic'),
                                        array('label' => Mage::helper('economic2')->__('Send customers to e-conomic'),
                                              'url'   => Mage::app()->getStore()->getUrl('adminhtml/economicCustomer/massSendCustomersToEconomic', array('_current'=>true))));
                    }
                }
            }

            if(get_class($block) =='Mage_Adminhtml_Block_Sales_Order_View' && $block->getRequest()->getControllerName() == 'sales_order')
            {
                $block->addButton('sender_order_to_economic', array(
                                  'label'     => $this->__('Send order to e-conomic'),
                                  'onclick'   => 'setLocation(\'' . $block->getUrl('adminhtml/sales_economicOrder/sendOrderToEconomic') . '\')',
                                  'class'     => 'go'));
            }

            if(get_class($block) =='Mage_Adminhtml_Block_Sales_Order_Creditmemo_View' && $block->getRequest()->getControllerName() == 'sales_order_creditmemo')
            {
                $creditMemoId = $block->getRequest()->getParam('creditmemo_id');
                $block->addButton('sender_credit_memo_to_economic', array(
                                'label'     => $this->__('Send credit memo to e-conomic'),
                                'onclick'   => 'setLocation(\'' . $block->getUrl('adminhtml/sales_economicOrder/sendCreditMemoToEconomic', array('creditmemo_id' => $creditMemoId)). '\')',
                                'class'     => 'go'));
            }

		}
		
	}

    private function IsModuleActiveOnStore($storeId){

        if($storeId === 'undefined'){
            return false;
        }

        return mage::getStoreConfig('economic2_options/moduleInfo/moduleStatus', $storeId);
    }

	
}

?>