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

class Nybohansen_Economic2_Model_System_Config_Source_TermOfPaymentList {

	private $term_of_payment_data;
	
	public function __construct(){
		$this->term_of_payment_data = NULL;
	}
	
 	public function toOptionArray(){

 		if(!$this->term_of_payment_data){
 			$eapi = Mage::getModel('economic2/eapi');
 			/* @var $eapi Nybohansen_Economic2_Model_Eapi */

            $storeId = Mage::helper('economic2/store')->getStoreId();
            $connection_result = $eapi->connect($storeId);

 			if($connection_result){
 				$term_of_payment_handles = $eapi->term_of_payment_get_all();
 				$this->term_of_payment_data = $eapi->term_of_payment_get_data_array_result($term_of_payment_handles);
 			}else{
 				return array(array('value' => null, 'label' => Mage::helper('economic2')->__('Cannot connect to e-conomic. Check your user credentials.')));
 			}
 		}
		
 		if($this->term_of_payment_data){
 			$res = array();
 			$res[] = array('value' => null, 'label' => Mage::helper('economic2')->__('Please choose a term of payment'));
 			foreach ($this->term_of_payment_data as $term_of_payment) {
 				$res[] = array('value' => $term_of_payment->Id, 'label' => $term_of_payment->Name);
 			} 				
 			return $res;
 		}else{
 			return array(array('value' => null, 'label' => Mage::helper('economic2')->__('Cannot connect to e-conomic. Check your user credentials.')));
 		}
				
  	}
	
}
?>