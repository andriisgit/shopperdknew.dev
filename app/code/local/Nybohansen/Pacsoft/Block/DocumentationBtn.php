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

class Nybohansen_Pacsoft_Block_DocumentationBtn extends Mage_Adminhtml_Block_System_Config_Form_Field
{

    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {

    	$html = $this->getLayout()->createBlock('adminhtml/widget_button')
                    ->setType('button')
                    ->setClass('scalable')
                    ->setLabel(Mage::helper('pacsoft')->__('Documentation'))
                    ->setOnClick("window.open('".$this->generateDocumentationUrl()."','_newtab')")
                    ->toHtml();
        
        return $html;
    }

    private function generateDocumentationUrl(){
        $version = Mage::getConfig()->getNode('modules')->Nybohansen_Pacsoft->version;
        $languageCode = explode('_', Mage::app()->getLocale()->getLocaleCode());
        return 'http://magentomoduler.dk/documentation/?module=pacsoft&version='.$version.'&locale='.$languageCode[0];
    }

    
}
?>