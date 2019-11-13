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

require_once('Mage/Adminhtml/controllers/Catalog/ProductController.php');

class Nybohansen_Economic2_Adminhtml_Catalog_EconomicProductController extends Mage_Adminhtml_Catalog_ProductController
{

    const MAX_NUM_PRODUCTS_SIMULTANEOUSLY = 10;

    protected function _construct()
    {
    	parent::_construct();
    }
    
    /**
     * Send the products to e-conomic
     */
    public function massSendProductToEconomicAction()
    {
    	$responsIds = '';
    	
    	$productIds = $this->getRequest()->getPost('product', array());

        if(count($productIds) > self::MAX_NUM_PRODUCTS_SIMULTANEOUSLY){
            $this->_getSession()->addError($this->__('Maximum number of products, to be sent to e-conomic simultaneously is [%s]', self::MAX_NUM_PRODUCTS_SIMULTANEOUSLY));
            $this->_redirect('adminhtml/catalog_product/index', array('_current' => true));
            return;
        }

    	foreach ($productIds as $productId) {
    		$economicOrder = Mage::getSingleton('economic2/economicOrder');
            /** @var $economicOrder Nybohansen_Economic2_Model_EconomicOrder  */

            $product = Mage::getModel('catalog/product')->load($productId);

    		$economicOrder->create_product($product, true, Mage::app()->getRequest()->getParam('store'));
			$responsIds .= $product->getName().', ';
			
    	}
    	trim($responsIds, ', ');
    	
    	
    	$this->_getSession()->addSuccess($this->__('Products [%s] sent to e-conomic', $responsIds));
        $this->_redirect('adminhtml/catalog_product/index', array('_current' => true));
    	 		
    }

    /**
    * Updates the products from e-conomic
    */
    public function massUpdateProductsFromEconomicAction()
    {

    	$productIds = $this->getRequest()->getPost('product', array());

        if(count($productIds) > self::MAX_NUM_PRODUCTS_SIMULTANEOUSLY){
            $this->_getSession()->addError($this->__('Maximum number of products, to be sent to e-conomic simultaneously is [%s]', self::MAX_NUM_PRODUCTS_SIMULTANEOUSLY));
            $this->_redirect('adminhtml/catalog_product/index', array('_current' => true));
            return;
        }

    	$economicOrder = Mage::getSingleton('economic2/economicOrder');
        /** @var $economicOrder Nybohansen_Economic2_Model_EconomicOrder  */
    	$result = $economicOrder->update_products_from_economic($productIds, Mage::app()->getRequest()->getParam('store'));

        trim($result, ', ');

    	$this->_getSession()->addSuccess($this->__('Products ['.$result.'] updated from e-conomic'));
    	$this->_redirect('adminhtml/catalog_product/index', array('_current' => true));

    }
    

}
