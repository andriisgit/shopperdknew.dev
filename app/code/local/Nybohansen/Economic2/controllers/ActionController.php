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

class Nybohansen_Economic2_ActionController extends Mage_Core_Controller_Front_Action {        
	
	/**
	 * @var Nybohansen_Economic2_Model_Eapi
	 */
	private $eapi;
	
	private $mapping;
	
	private $economic_fields = array('Name', 'BarCode', 'CostPrice', 'Description', 'InStock', 'IsAccessible', 'SalesPrice', 'RecommendedPrice', 'Available'); 

	
	public function testconnectionAction() {

		$eapi = Mage::getModel('economic2/eapi');
		$eapi->show_debug(false);

		if($this->getRequest()->getParam('token')){
			$token = $this->getRequest()->getParam('token');
			$connection_result = $eapi->connectWithToken($token);
		}else{
			$agreement_number = $this->getRequest()->getParam('agreement_number');
			$username = $this->getRequest()->getParam('username');
			$password = $this->getRequest()->getParam('password');
			$connection_result = $eapi->connectWithCredentials($agreement_number, $username, $password);
		}

		if($connection_result){
            $this->getResponse()->setBody('true');
		}else{
            $this->getResponse()->setBody('false');
		}

	} 
	
	
	public function tokenAction(){
		$token = $this->getRequest()->getParam('token');
		$eapi = Mage::getModel('economic2/eapi');

        $storeId = $this->getRequest()->getParam('storeId');
		$scope = $this->getRequest()->getParam('scope');
		$store = $this->getRequest()->getParam('store');
		$website = $this->getRequest()->getParam('website');

		if($eapi->connectWithToken($token)){

            //Saving recieved token, since we can log in...
			Mage::getConfig()->saveConfig('economic2_options/accountInfo/apiToken', $token, $scope, $storeId);
            Mage::getConfig()->saveConfig('economic2_options/accountInfo/use_token_based_login', 1, $scope, $storeId);
            Mage::getConfig()->reinit();
			Mage::app()->reinitStores();

            $storeCode = Mage::getModel('core/store')->load($storeId)->getCode();

			if($scope == 'websites'){
				$url = Mage::helper("adminhtml")->getUrl('adminhtml/system_config/edit', array('section' => 'economic2_options',
																					 		   'key' => $this->getRequest()->getParam('code2'),
																							   'website' => $website));
			}elseif($scope == 'stores'){
				$url = Mage::helper("adminhtml")->getUrl('adminhtml/system_config/edit', array('section' => 'economic2_options',
																							   'key' => $this->getRequest()->getParam('code2'),
																							   'website' => $website,
					    	            												       'store' => $store));
			}else{
				$url = Mage::helper("adminhtml")->getUrl('adminhtml/system_config/edit', array('section' => 'economic2_options', 'key' => $this->getRequest()->getParam('code2')));
			}

			//Redirect to magento economic configuration
            $this->_redirectUrl($url);
		}else{
			echo 'Sorry, wrong token recieved from e-conomic!';
		}
		
	}
	
	public function updateProductAction(){
        $storeId = Mage::app()->getRequest()->getParam('storeId');
        if(!$storeId){
            $storeId = Mage_Core_Model_App::ADMIN_STORE_ID;
        }
        Mage::app()->setCurrentStore($storeId);

        if ($this->getRequest()->getParam('code') == md5(Mage::getConfig()->getNode('modules')->Nybohansen_Economic2->license)){
        	if(mage::getStoreConfig('economic2_options/moduleInfo/moduleStatus', $storeId) && mage::getStoreConfig('economic2_options/productConfig/product_update_from_economic', $storeId)) {

                if ($_GET['number'] != '[NUMBER]') {
                    $economic_product_id = $_GET['number'];
                } else {
                    $economic_product_id = $_GET['new'];
                }
                //Remove prefix
                $magento_product_id = ltrim($economic_product_id, mage::getStoreConfig('economic2_options/productConfig/product_id_prefix', $storeId));

				//Load product
				if(mage::getStoreConfig('economic2_options/productConfig/product_id_use_default', $storeId)){
					//Load product by default product id
					$product = Mage::getModel('catalog/product')->setStoreId($storeId)->load($magento_product_id);
				}else{
					//Load product based on chosen id field
					$product = Mage::getModel('catalog/product')->setStoreId($storeId)->loadByAttribute(mage::getStoreConfig('economic2_options/productConfig/product_id_attribute', $storeId), $magento_product_id);
				}

				if($product){
					$result = Mage::getSingleton('economic2/economicOrder')->update_products_from_economic(array($product->getId()), $storeId);
					if($result){
						echo $result . ' updated';
					}else{
						echo 'There was a problem updating '.$economic_product_id;
					}
				}else{
					echo 'Product '.$economic_product_id.' does not exist in Magento';
				}


            }
		}else{
            mage::log($_SERVER['REMOTE_ADDR'].' tried to connect to the webhook url without correct code', Zend_Log::WARN, 'e-conomic2.log');
            echo 'e-conomic/Magento integration: Wrong code dude!'."</br>";
            echo 'Version: '.Mage::getConfig()->getNode('modules')->Nybohansen_Economic2->version."</br>";
            echo 'License: '.Mage::getConfig()->getNode('modules')->Nybohansen_Economic2->license."</br>";
            echo 'e-conomic id: '.mage::getStoreConfig('economic2_options/accountInfo/agreement_number', $storeId)."</br>";
        }
	}
	
	private function getEAPIConnection($storeId){
		if(!$this->eapi){
			$this->eapi = Mage::getModel('economic2/eapi');
            $this->eapi->connect($storeId);
		}
		return $this->eapi;
	}

	
	private function getMapping($storeId){
		if(!$this->mapping){
			$serialized_mapping = mage::getStoreConfig('economic2_options/productConfig/field_mapping_from_e_to_m', $storeId);
			$tmp = @unserialize($serialized_mapping);
			foreach ($tmp as $map) {
				$this->mapping[$map['economic_field']][] = $map['magento_field'];
			}
		}
		return $this->mapping;
	}
	
	
}
