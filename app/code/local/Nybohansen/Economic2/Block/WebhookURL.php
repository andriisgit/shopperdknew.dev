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

class Nybohansen_Economic2_Block_WebhookURL extends Mage_Adminhtml_Block_System_Config_Form_Field
{

    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
    	$html = '<input class="input-text" id="economic2_options_productConfig_webhook_url" style="width:280px;" name="" value="'.$this->getWebhookUrl().'" READONLY/> ';
        return $html;
    }
    
    private function getWebhookUrl(){
        $storeId = Mage::helper('economic2/store')->getStoreId();
        $url = Mage::app()->getStore()->getUrl('economic2/action/updateProduct',array('code' => $this->getKey(), 'storeId' => $storeId)).'?old=[OLDNUMBER]&new=[NEWNUMBER]&number=[NUMBER]';
        return $url;
    }
    
    private function getKey(){
    	return md5(Mage::getConfig()->getNode('modules')->Nybohansen_Economic2->license);
    }
    
    
}
?>