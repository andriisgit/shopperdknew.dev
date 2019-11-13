<?php
/**
 * @version   1.0 14.08.2012
 * @author    TonyEcommerce http://www.TonyEcommerce.com <support@TonyEcommerce.com>
 * @copyright Copyright (c) 2012 TonyEcommerce
 */

class Etheme_Buyshopconfig_Block_Adminhtml_Fields_Color extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $output=parent::_getElementHtml($element);
        if (!Mage::registry('mColorPicker')) {
            $output.='
            <script type="text/javascript" src="'.$this->getJsUrl('etheme/buyshop/jquery-1.7.2.min.js').'"></script>
            <script type="text/javascript" src="'.$this->getJsUrl('etheme/buyshop/mColorPicker.js').'"></script>
            <script type="text/javascript">
                jQuery.noConflict();
                 jQuery.fn.mColorPicker.defaults.imageFolder="'.$this->getJsUrl('etheme/buyshop/images/').'";
            </script>
            ';
            Mage::register('mColorPicker', 1);
        }
		$output .= '
        <script type="text/javascript">
            jQuery(function(){
                 jQuery("#'.$element->getHtmlId().'").attr("data-hex", true).mColorPicker();
            })
        </script> ';
        return $output;
    }
}