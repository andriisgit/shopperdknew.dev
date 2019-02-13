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

class Nybohansen_Economic2_Block_TestConnectionBtn extends Mage_Adminhtml_Block_System_Config_Form_Field
{

    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
    	
    	$this->setElement($element);
    	$url = Mage::helper("adminhtml")->getUrl('economic2/action/testconnection');

    	
    	$js = "$('row_economic2_options_accountInfo_connectionResult').down('td.value').innerHTML = '". Mage::helper('economic2')->__('Checking credentials ...')."';
    		   var url = '".$url."';
    		   if($('economic2_options_accountInfo_use_token_based_login').value == 1){
    		   		new Ajax.Request(url, {  
				                method: 'post',
				                parameters: {token: $('economic2_options_accountInfo_apiToken').value},  
								onComplete: function(transport) {
									if(transport.responseText=='true'){
										$('row_economic2_options_accountInfo_connectionResult').down('td.value').innerHTML = '".Mage::helper('economic2')->__('Connection established, please save before continuing')."';
									}else{
										$('row_economic2_options_accountInfo_connectionResult').down('td.value').innerHTML = '".Mage::helper('economic2')->__('Cannot connect to e-conomic. Check your user credentials.')."';
									}
								}});
    			}else{
    				new Ajax.Request(url, {  
				                method: 'post',
				                parameters: {agreement_number: $('economic2_options_accountInfo_agreement_number').value,
    										 username: $('economic2_options_accountInfo_username').value,
    										 password: $('economic2_options_accountInfo_password').value},  
								onComplete: function(transport) {
									if(transport.responseText=='true'){
										$('row_economic2_options_accountInfo_connectionResult').down('td.value').innerHTML = '".Mage::helper('economic2')->__('Connection established, please save before continuing')."';
									}else{
										$('row_economic2_options_accountInfo_connectionResult').down('td.value').innerHTML = '".Mage::helper('economic2')->__('Cannot connect to e-conomic. Check your user credentials.')."';
									}
								}});
    			}";
    	
    	$element = $this->getLayout()->createBlock('adminhtml/widget_button')
		    	->setType('button')
		    	->setClass('scalable')
		    	->setLabel(Mage::helper('economic2')->__('Fetch token!'))
		    	->setOnClick("setLocation('".$this->generatetokenUrl()."')"); 
    	
    	$html = $element->toHtml() .'<span>  </span>';
    	
    	$html .= $this->getLayout()->createBlock('adminhtml/widget_button')
                    ->setType('button')
                    ->setClass('scalable')
                    ->setLabel(Mage::helper('economic2')->__('Test connection!'))
                    ->setOnClick('javascript: '.$js)
                    ->toHtml();
        
        $html .= "<script type=\"text/javascript\">
					Event.observe(window, 'load', function() {
        				$('row_economic2_options_accountInfo_connectionResult').down('td.scope-label').innerHTML = '';
						$('row_economic2_options_accountInfo_connectionTest').down('td.scope-label').innerHTML = '';

						if($('economic2_options_accountInfo_use_token_based_login').value == 1){
							$('{$element->getHtmlId()}').show();
						}else{
							$('{$element->getHtmlId()}').hide();
						}
						
					});
					
					Event.observe($('economic2_options_accountInfo_use_token_based_login'), 'change', function(){
						if($('economic2_options_accountInfo_use_token_based_login').value == 1){
							$('{$element->getHtmlId()}').show();
						}else{
							$('{$element->getHtmlId()}').hide();
						}
                	});
				</script>";
        
        
        return $html;
    }
    
    private function generatetokenUrl(){
        $appToken = Mage::getConfig()->getNode('modules')->Nybohansen_Economic2->appToken;
        $storeInfo = Mage::helper('economic2/store')->getStoreInfo();
		$redirectURL = Mage::helper("adminhtml")->getUrl('economic2/action/token',array('code2' => $this->getRequest()->getParam('key'),
																					    'storeId' => $storeInfo['id'],
																					    'scope' => $storeInfo['scope'],
																						'website' => $storeInfo['website'],
																						'store' => $storeInfo['store']));
		return 'https://secure.e-conomic.com/secure/api1/requestaccess.aspx?appId='.$appToken.'&redirectUrl='.$redirectURL;
    }
    
}
?>