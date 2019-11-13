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

require_once('Mage/Adminhtml/controllers/CustomerController.php');
                      
class Nybohansen_Economic2_Adminhtml_EconomicCustomerController extends Mage_Adminhtml_CustomerController
{
	
    protected function _construct()
    {
    	parent::_construct();
    }
    
    /**
     * Send the orders to e-conomic
     */
    public function massSendCustomersToEconomicAction()
    {
    	
    	$responsIds = '';
    	
    	$customerIds = $this->getRequest()->getPost('customer', array());
    	    	
    	foreach ($customerIds as $customerId) {
//    		$economicOrder = Mage::getSingleton('economic2/economicOrder');
            /** @var $economicOrder Nybohansen_Economic2_Model_EconomicOrder  */
            $customer = Mage::getModel('customer/customer')->load($customerId);
//    		$economicOrder->create_debtor_from_customer($customer);

            $economicCustomer = Nybohansen_Economic2_Model_EconomicDebtor::fromMagentoCustomer($customer);
            $economicCustomer->sendToEconomic();

 			$responsIds .= $customer->getEmail().', ';
			
    	}
    	trim($responsIds, ', ');

    	$this->_getSession()->addSuccess($this->__('Customers [%s] sent to e-conomic', $responsIds));
        $this->_redirect('adminhtml/customer/index', array('_current' => true));
    	 
		
    }


}
