<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Alesioo
 * Date: 07.03.13
 * Time: 12:03
 * To change this template use File | Settings | File Templates.
 */
require_once 'Mage/Catalog/controllers/Product/CompareController.php';
class Etheme_Buyshopconfig_Product_CompareController extends Mage_Catalog_Product_CompareController {

    public function removeAction()
    {

        if ($productId = (int) $this->getRequest()->getParam('product')) {
            $product = Mage::getModel('catalog/product')
                ->setStoreId(Mage::app()->getStore()->getId())
                ->load($productId);

            if($product->getId()) {
                /** @var $item Mage_Catalog_Model_Product_Compare_Item */
                $item = Mage::getModel('catalog/product_compare_item');
                if(Mage::getSingleton('customer/session')->isLoggedIn()) {
                    $item->addCustomerData(Mage::getSingleton('customer/session')->getCustomer());
                } elseif ($this->_customerId) {
                    $item->addCustomerData(
                        Mage::getModel('customer/customer')->load($this->_customerId)
                    );
                } else {
                    $item->addVisitorId(Mage::getSingleton('log/visitor')->getId());
                }

                $item->loadByProduct($product);

                if($item->getId()) {
                    $item->delete();
                    Mage::getSingleton('catalog/session')->addSuccess(
                        $this->__('The product %s has been removed from comparison list.', $product->getName())
                    );
                    Mage::dispatchEvent('catalog_product_compare_remove_product', array('product'=>$item));
                    Mage::helper('catalog/product_compare')->calculate();
                }
            }
        }

        Mage::app()->getResponse()->setRedirect($_SERVER['HTTP_REFERER']);
    }


    /**
     * Delete shoping cart item action
     */
    public function deleteAction()
    {
        $id = (int) $this->getRequest()->getParam('id');
        if ($id) {
            try {
                $this->_getCart()->removeItem($id)
                    ->save();
            } catch (Exception $e)
            {
                $this->_getSession()->addError($this->__('Cannot remove the item.'));
                Mage::logException($e);
            }
        }
        $this->_goBack();
    }


    public function clearAction()
    {
        $items = Mage::getResourceModel('catalog/product_compare_item_collection');

        if (Mage::getSingleton('customer/session')->isLoggedIn()) {
            $items->setCustomerId(Mage::getSingleton('customer/session')->getCustomerId());
        } elseif ($this->_customerId) {
            $items->setCustomerId($this->_customerId);
        } else {
            $items->setVisitorId(Mage::getSingleton('log/visitor')->getId());
        }

        /** @var $session Mage_Catalog_Model_Session */
        $session = Mage::getSingleton('catalog/session');

        try {
            $items->clear();
            $session->addSuccess($this->__('The comparison list was cleared.'));
            Mage::helper('catalog/product_compare')->calculate();
        } catch (Mage_Core_Exception $e) {
            $session->addError($e->getMessage());
        } catch (Exception $e) {
            $session->addException($e, $this->__('An error occurred while clearing comparison list.'));
        }
        
        //$this->_redirectReferer();
        Mage::app()->getResponse()->setRedirect($_SERVER['HTTP_REFERER']);
    }
}