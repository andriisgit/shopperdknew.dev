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
* @package    Nybohansen_Pacsoft
* @copyright  Copyright (c) 2014 Nybohansen ApS
* @license    LICENSE.txt
*/

class Nybohansen_Pacsoft_Model_System_Config_Source_WeightUnitList {

         public function toOptionArray() {
             return array(
                 array('value' => '1000', 'label' => Mage::helper('pacsoft')->__('Gram')),
                 array('value' => '1', 'label' => Mage::helper('pacsoft')->__('Kilo'))
             );
         }
	
}
?>