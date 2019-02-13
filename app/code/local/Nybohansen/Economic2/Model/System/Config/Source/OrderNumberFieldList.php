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

class Nybohansen_Economic2_Model_System_Config_Source_OrderNumberFieldList {

 	public function toOptionArray(){
  		
 		$configuration = Mage::getModel('economic2/configuration');
 		
 		return array(array('value'=> $configuration->STORE_ORDER_NUMBER_IN_HEADING, 'label' => Mage::helper('economic2')->__('Heading')),
 		             array('value'=> $configuration->STORE_ORDER_NUMBER_IN_OTHER_REF, 'label' => Mage::helper('economic2')->__('Other reference')),
                     array('value'=> $configuration->STORE_ORDER_COMMENT_TEXTLINE1, 'label' => Mage::helper('economic2')->__('Text line 1')),
                     array('value'=> $configuration->STORE_ORDER_COMMENT_TEXTLINE2, 'label' => Mage::helper('economic2')->__('Text line 2')));



  	}
	
}
?>