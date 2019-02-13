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

class Nybohansen_Economic2_Model_System_Config_Source_DepartmentList {

 	public function toOptionArray(){
  		
 		$eapi = Mage::getModel('economic2/eapi');

        $storeId = Mage::helper('economic2/store')->getStoreId();
        $connection_result = $eapi->connect($storeId);

		if($connection_result && mage::getStoreConfig('economic2_options/orderConfig/use_departments', $storeId)){
			$departments = $eapi->department_get_all();
			$res = array();
			if(is_array($departments)){
                foreach ($departments as $department_number => $department_name) {
                    $res[] = array('value' => $department_number, 'label' => $department_name);
                }
            }
			return $res;	
		}else{
			return array(array('value' => null, 'label' => Mage::helper('economic2')->__('Cannot connect to e-conomic. Check your user credentials.')));
		}
		
  	}
	
}
?>