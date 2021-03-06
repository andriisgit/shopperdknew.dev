<?php
class Excellence_AjaxWishlist_IndexController extends Mage_Core_Controller_Front_Action
{

	public function compareAction(){
		$response = array();

		if ($productId = (int) $this->getRequest()->getParam('product')) {
			$product = Mage::getModel('catalog/product')
			->setStoreId(Mage::app()->getStore()->getId())
			->load($productId);

			if ($product->getId()/* && !$product->isSuper()*/) {
				Mage::getSingleton('catalog/product_compare_list')->addProduct($product);
				$response['status'] = 'SUCCESS';
				$response['message'] = $this->__('The product %s has been added to comparison list.', Mage::helper('core')->escapeHtml($product->getName()));
				Mage::register('referrer_url', $this->_getRefererUrl());
				Mage::helper('catalog/product_compare')->calculate();
				Mage::dispatchEvent('catalog_product_compare_add_product', array('product'=>$product));
				$this->loadLayout();
				$sidebar_block = $this->getLayout()->getBlock('catalog.compare.sidebar');
				$sidebar_block->setTemplate('catalog/product/compare/sidebar.phtml');
				$sidebar = $sidebar_block->toHtml();
				$response['sidebar'] = $sidebar;
                $top_block = $this->getLayout()->getBlock('catalog.compare.top');
                $top_block->setTemplate('catalog/product/compare/top.phtml');
				$top = $top_block->toHtml();
				$response['top_block'] = $top;
			}
		}

		$this->_sendJson($response);
		return;
	}

	/**
	 * send json respond
	 *
	 * @param array $response - response data
	 */
	private function _sendJson( $response )
	{
		$this->getResponse()->setHeader('Content-type', 'application/json');
		$this->getResponse()->setBody( (string) $this->getRequest()->getParam('callback') . '(' . Mage::helper('core')->jsonEncode($response) . ')' );
	}


	protected function _getWishlist()
	{
		$wishlist = Mage::registry('wishlist');
		if ($wishlist) {
			return $wishlist;
		}

		try {
			$wishlist = Mage::getModel('wishlist/wishlist')
			->loadByCustomer(Mage::getSingleton('customer/session')->getCustomer(), true);
			Mage::register('wishlist', $wishlist);
		} catch (Mage_Core_Exception $e) {
			Mage::getSingleton('wishlist/session')->addError($e->getMessage());
		} catch (Exception $e) {
			Mage::getSingleton('wishlist/session')->addException($e,
			Mage::helper('wishlist')->__('Cannot create wishlist.')
			);
			return false;
		}

		return $wishlist;
	}

	public function addAction()
	{

		$response = array();
		if (!Mage::getStoreConfigFlag('wishlist/general/active')) {
			$response['status'] = 'ERROR';
			$response['message'] = $this->__('Wishlist Has Been Disabled By Admin');
		}
		if(!Mage::getSingleton('customer/session')->isLoggedIn()){
			$response['status'] = 'ERROR';
			$response['message'] = $this->__('Please Login First');
		}

		if(empty($response)){
			$session = Mage::getSingleton('customer/session');
			$wishlist = $this->_getWishlist();
			if (!$wishlist) {
				$response['status'] = 'ERROR';
				$response['message'] = $this->__('Unable to Create Wishlist');
			}else{

				$productId = (int) $this->getRequest()->getParam('product');
				if (!$productId) {
					$response['status'] = 'ERROR';
					$response['message'] = $this->__('Product Not Found');
				}else{

					$product = Mage::getModel('catalog/product')->load($productId);
					if (!$product->getId() || !$product->isVisibleInCatalog()) {
						$response['status'] = 'ERROR';
						$response['message'] = $this->__('Cannot specify product.');
					}else{

						try {
							$requestParams = $this->getRequest()->getParams();
							$buyRequest = new Varien_Object($requestParams);

							$result = $wishlist->addNewItem($product, $buyRequest);
							if (is_string($result)) {
								Mage::throwException($result);
							}
							$wishlist->save();

							Mage::dispatchEvent(
                				'wishlist_add_product',
							array(
			                    'wishlist'  => $wishlist,
			                    'product'   => $product,
			                    'item'      => $result
							)
							);

							Mage::helper('wishlist')->calculate();

							$message = $this->__('%1$s has been added to your wishlist.', $product->getName());
							$response['status'] = 'SUCCESS';
							$response['message'] = $message;

							Mage::unregister('wishlist');

							$this->loadLayout();
							$toplink = $this->getLayout()->getBlock('top.links')->toHtml();
							$sidebar_block = $this->getLayout()->getBlock('wishlist_sidebar');
							$sidebar = $sidebar_block->toHtml();
							$response['toplink'] = $toplink;
							$response['sidebar'] = $sidebar;
						}
						catch (Mage_Core_Exception $e) {
							$response['status'] = 'ERROR';
							$response['message'] = $this->__('An error occurred while adding item to wishlist: %s', $e->getMessage());
						}
						catch (Exception $e) {
							mage::log($e->getMessage());
							$response['status'] = 'ERROR';
							$response['message'] = $this->__('An error occurred while adding item to wishlist.');
						}
					}
				}
			}

		}

		$this->_sendJson($response);
		return;
	}

	/**
	 * Action to accept new configuration for a wishlist item
	 */
	public function updateItemOptionsAction()
	{

		$response = array();
		if (!Mage::getStoreConfigFlag('wishlist/general/active')) {
			$response['status'] = 'ERROR';
			$response['message'] = $this->__('Wishlist Has Been Disabled By Admin');
			$this->_sendJson($response);
			return;
		}

		$session = Mage::getSingleton('customer/session');
		$productId = (int) $this->getRequest()->getParam('product');
		if (!$productId) {
			$response['status'] = 'ERROR';
			$response['message'] = $this->__('Cannot specify product.');
			$this->_sendJson($response);
			return;
		}

		$product = Mage::getModel('catalog/product')->load($productId);
		if (!$product->getId() || !$product->isVisibleInCatalog()) {
			$response['status'] = 'ERROR';
			$response['message'] = $this->__('Cannot specify product.');
			$this->_sendJson($response);
			return;
		}

		try {
			$id = (int) $this->getRequest()->getParam('id');
			/* @var Mage_Wishlist_Model_Item */
			$item = Mage::getModel('wishlist/item');
			$item->load($id);
			$wishlist = $this->_getWishlist($item->getWishlistId());
			if (!$wishlist) {
				$response['status'] = 'ERROR';
				$response['message'] = $this->__('An error occurred while updating wishlist.');
				$this->_sendJson($response);
				return;
			}

			$buyRequest = new Varien_Object($this->getRequest()->getParams());

			$wishlist->updateItem($id, $buyRequest)
				->save();

			Mage::helper('wishlist')->calculate();
			Mage::dispatchEvent('wishlist_update_item', array(
					'wishlist' => $wishlist, 'product' => $product, 'item' => $wishlist->getItem($id))
			);

			Mage::helper('wishlist')->calculate();

			$message = $this->__('%1$s has been updated in your wishlist.', $product->getName());

			$response['status'] = 'SUCCESS';
			$response['message'] = $message;

			$this->loadLayout();
			$toplink = $this->getLayout()->getBlock('top.links')->toHtml();
			$sidebar_block = $this->getLayout()->getBlock('wishlist_sidebar');
			$sidebar = $sidebar_block->toHtml();
			$response['toplink'] = $toplink;
			$response['sidebar'] = $sidebar;

		} catch (Mage_Core_Exception $e) {
			$response['status'] = 'ERROR';
			$response['message'] = $this->__($e->getMessage());
		} catch (Exception $e) {
			$response['status'] = 'ERROR';
			$response['message'] = $this->__('An error occurred while updating wishlist.');
			Mage::logException($e);
		}

		$this->_sendJson($response);
		return;

	}

}