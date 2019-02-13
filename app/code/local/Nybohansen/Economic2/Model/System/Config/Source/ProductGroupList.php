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

class Nybohansen_Economic2_Model_System_Config_Source_ProductGroupList {

 	public function toOptionArray(){
  		
 		$eapi = Mage::getModel('economic2/eapi');

        $storeId = Mage::helper('economic2/store')->getStoreId();
        $connection_result = $eapi->connect($storeId);


		if($connection_result){
			$product_groups = $eapi->product_groups_get_all();
			$res = array();
			foreach ($product_groups as $product_group_number => $product_group_name) {
				$res[] = array('value' => $product_group_number, 'label' => $product_group_name);
			}
			return $res;	
		}else{
			return array(array('value' => null, 'label' => Mage::helper('economic2')->__('Cannot connect to e-conomic. Check your user credentials.')));
		}
		
  	}
	
}
?>