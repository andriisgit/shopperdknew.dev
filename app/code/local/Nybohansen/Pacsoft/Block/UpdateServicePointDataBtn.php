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

class Nybohansen_Pacsoft_Block_UpdateServicePointDataBtn extends Mage_Adminhtml_Block_System_Config_Form_Field
{

    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
    	
    	$this->setElement($element);
    	$url = Mage::helper("adminhtml")->getUrl('pacsoft/ajax/updateServicePoint');

    	
    	$js = "$('row_carriers_pacsoft_updateResult').down('td.value').innerHTML = '". Mage::helper('pacsoft')->__('Updating service point data ...')."';
    		   var url = '".$url."';

                new Ajax.Request(url, {
                            method: 'post',
                            onComplete: function(transport) {
                                if(transport.responseText=='false'){
                                    $('row_carriers_pacsoft_updateResult').down('td.value').innerHTML = '".Mage::helper('pacsoft')->__('Could not update service point data')."';
                                }else{
                                $('row_carriers_pacsoft_updateResult').down('td.value').innerHTML = '".Mage::helper('pacsoft')->__('Service point data updated')."';
                                    $('row_carriers_pacsoft_lastUpdatedTime').down('td.value').innerHTML = transport.responseText;
                                }
                            }});
    		";
    	
    	$btn = $this->getLayout()->createBlock('adminhtml/widget_button')
                    ->setType('button')
                    ->setClass('scalable')
                    ->setLabel(Mage::helper('pacsoft')->__('Manual update service point data!'))
                    ->setOnClick('javascript: '.$js);

    	$html = $btn->toHtml();

        
        $html .= "<script type=\"text/javascript\">
					Event.observe(window, 'load', function() {
						$('row_carriers_pacsoft_updateResult').down('td.scope-label').innerHTML = '';
					});
				</script>";
        
        
        return $html;
    }
    

}
?>