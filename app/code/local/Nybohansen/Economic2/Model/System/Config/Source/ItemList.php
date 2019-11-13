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

class Nybohansen_Economic2_Model_System_Config_Source_ItemList {

	private $product_data;
	
	public function __construct(){
		$this->product_data = NULL;
	}
	
 	public function toOptionArray(){

 		if(!$this->product_data){
 			$eapi = Mage::getModel('economic2/eapi');
 			/* @var $eapi Nybohansen_Economic2_Model_Eapi */

            $storeId = Mage::helper('economic2/store')->getStoreId();
            $connection_result = $eapi->connect($storeId);

 			if($connection_result){
 				$product_handles = $eapi->product_get_all();
                if(count($product_handles)==1){
                    $this->product_data = array($eapi->product_get_data_array($product_handles));
                }else{
                    $this->product_data = $eapi->product_get_data_array_result($product_handles);
                }
 			}else{
 				return array(array('value' => null, 'label' => Mage::helper('economic2')->__('Cannot connect to e-conomic. Check your user credentials.')));
 			}
 		}
 		if($this->product_data){
 			$res = array();
 			$res[] = array('value' => null, 'label' => Mage::helper('economic2')->__('Please choose an e-conomic item'));
 			foreach ($this->product_data as $product) {
 				$res[] = array('value' => $product->Number, 'label' => $product->Name);
 			} 				
 			return $res;
 		}else{
 			return array(array('value' => null, 'label' => Mage::helper('economic2')->__('No items found in e-conomic')));
 		}
				
  	}
	
}
?>