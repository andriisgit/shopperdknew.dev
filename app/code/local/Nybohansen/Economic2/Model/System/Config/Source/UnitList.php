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

class Nybohansen_Economic2_Model_System_Config_Source_UnitList {

 	public function toOptionArray(){
  		
 		$eapi = Mage::getModel('economic2/eapi');
 		/* @var $eapi Nybohansen_Economic2_Model_Eapi */

        $storeId = Mage::helper('economic2/store')->getStoreId();
        $connection_result = $eapi->connect($storeId);
 		
		if($connection_result){
			$units_handles = $eapi->unit_get_all();

            if(!is_array($units_handles)){
                $units_handles = array($units_handles);
            }
			$res = array();
			foreach ($units_handles as $unit_handle) {
				$unit_name = $eapi->unit_get_name($unit_handle);
				$res[] = array('value' => $unit_name, 'label' => $unit_name);
			}
			return $res;	
		}else{
			return array(array('value' => null, 'label' => Mage::helper('economic2')->__('Cannot connect to e-conomic. Check your user credentials.')));
		}
		

  	}
	
}
?>