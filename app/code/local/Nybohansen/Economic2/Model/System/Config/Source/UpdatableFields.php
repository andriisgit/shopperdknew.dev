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

class Nybohansen_Economic2_Model_System_Config_Source_UpdatableFields {
	
	/**
	 * 
	 * Returns fields that can be updated from e-conomic to magento
	 */
 	public function toOptionArray(){
 		
 		return array(
 						array('value' => 'barcode', 'label' => Mage::helper('economic2')->__('Barcode')),
 					 	array('value' => 'costPrice', 'label' => Mage::helper('economic2')->__('Cost price')),
 					 	array('value' => 'description', 'label' => Mage::helper('economic2')->__('Description')),
 					 	array('value' => 'inStock', 'label' => Mage::helper('economic2')->__('In stock')),
 					 	array('value' => 'isAccessible', 'label' => Mage::helper('economic2')->__('Is accessible')),
 					 	array('value' => 'name', 'label' => Mage::helper('economic2')->__('Name')),
 					 	array('value' => 'salesPrice', 'label' => Mage::helper('economic2')->__('Sales price')),
 					 	array('value' => 'unit', 'label' => Mage::helper('economic2')->__('Unit')),
 					 	array('value' => 'recomendedPrice', 'label' => Mage::helper('economic2')->__('Recomended price'))
 					 );
  	}
	
}
?>