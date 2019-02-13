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

class Nybohansen_Economic2_Model_System_Config_Source_CardTypeList {



 	public function QuickpayCards(){
         return array(
             array('value' => 'american-express', 'label' => Mage::helper('economic2')->__('American Express')),
             array('value' => 'american-express-dk', 'label' => Mage::helper('economic2')->__('American Express (udstedt i Danmark)')),
             array('value' => 'dankort', 'label' => Mage::helper('economic2')->__('Dankort')),
             array('value' => 'danske-dk', 'label' => Mage::helper('economic2')->__('Danske Net Bank')),
             array('value' => 'diners', 'label' => Mage::helper('economic2')->__('Diners Club')),
             array('value' => 'diners-dk', 'label' => Mage::helper('economic2')->__('Diners Club (udstedt i Danmark)')),
             array('value' => 'edankort', 'label' => Mage::helper('economic2')->__('eDankort')),
             array('value' => 'fbg1886', 'label' => Mage::helper('economic2')->__('Forbrugsforeningen af 1886')),
             array('value' => 'jcb', 'label' => Mage::helper('economic2')->__('JCB')),
             array('value' => 'mastercard', 'label' => Mage::helper('economic2')->__('Mastercard')),
             array('value' => 'mastercard-dk', 'label' => Mage::helper('economic2')->__('Mastercard (udstedt i Danmark)')),
             array('value' => 'mastercard-debet-dk', 'label' => Mage::helper('economic2')->__('Mastercard Debet (udstedt i Danmark)')),
             array('value' => 'nordea-dk', 'label' => Mage::helper('economic2')->__('Nordea Net Bank')),
             array('value' => 'visa', 'label' => Mage::helper('economic2')->__('Visa')),
             array('value' => 'visa-dk', 'label' => Mage::helper('economic2')->__('Visa (udstedt i Danmark)')),
             array('value' => 'visa-electron', 'label' => Mage::helper('economic2')->__('Visa Electron')),
             array('value' => 'visa-electron-dk', 'label' => Mage::helper('economic2')->__('Visa Electron (udstedt i Danmark)')),
             array('value' => 'paypal', 'label' => Mage::helper('economic2')->__('PayPal')),
         );
    }

    public function EpayCards(){
        return array(
            array('value' => '1', 'label' => Mage::helper('economic2')->__('Dankort/Visa-Dankort')),
            array('value' => '2', 'label' => Mage::helper('economic2')->__('eDankort')),
            array('value' => '3', 'label' => Mage::helper('economic2')->__('Visa / Visa Electron')),
            array('value' => '4', 'label' => Mage::helper('economic2')->__('MasterCard')),
            array('value' => '6', 'label' => Mage::helper('economic2')->__('JCB')),
            array('value' => '7', 'label' => Mage::helper('economic2')->__('Maestro')),
            array('value' => '8', 'label' => Mage::helper('economic2')->__('Diners Club')),
            array('value' => '9', 'label' => Mage::helper('economic2')->__('American Express')),
            array('value' => '11', 'label' => Mage::helper('economic2')->__('Forbrugsforeningen')),
            array('value' => '12', 'label' => Mage::helper('economic2')->__('Nordea e-betaling')),
            array('value' => '13', 'label' => Mage::helper('economic2')->__('Danske Netbetalinger')),
            array('value' => '14', 'label' => Mage::helper('economic2')->__('PayPal')),
            array('value' => '17', 'label' => Mage::helper('economic2')->__('Klarna')),
            array('value' => '23', 'label' => Mage::helper('economic2')->__('ViaBill')),
            array('value' => '24', 'label' => Mage::helper('economic2')->__('Beeptify')),
            array('value' => '25', 'label' => Mage::helper('economic2')->__('iDEAL')),
            array('value' => '27', 'label' => Mage::helper('economic2')->__('Paii')),
            array('value' => '28', 'label' => Mage::helper('economic2')->__('Brandts Gavekort')),
            array('value' => '29', 'label' => Mage::helper('economic2')->__('MobilePay Online'))
        );
    }

		


	
}
?>