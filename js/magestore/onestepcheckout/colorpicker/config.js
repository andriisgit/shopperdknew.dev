/*
 * Magestore
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Magestore
 * @package     Magestore_Onestepcheckout
 * @copyright   Copyright (c) 2017 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

// load color for input text in custom style

function loadColor_onestepcheckout(click_id){       
    if($('onestepcheckout_style_management_custom')){
        new colorPicker('onestepcheckout_style_management_custom',{
            previewElement:'onestepcheckout_style_management_custom',
            inputElement:'onestepcheckout_style_management_custom',
            eventName:click_id ,
            color:'#'+$('onestepcheckout_style_management_custom').value,
        });
    }
}

function loadColor_onestepcheckoutbutton(click_id){       
    if($('onestepcheckout_style_management_custombutton')){
        new colorPicker('onestepcheckout_style_management_custombutton',{
            previewElement:'onestepcheckout_style_management_custombutton',
            inputElement:'onestepcheckout_style_management_custombutton',
            eventName:click_id ,
            color:'#'+$('onestepcheckout_style_management_custombutton').value,
        });
    }
}

function toggleCustomValueElements(checkbox, container, excludedElements, checked){
    if(container && checkbox){
        var ignoredElements = [checkbox];
        if (typeof excludedElements != 'undefined') {
            if (Object.prototype.toString.call(excludedElements) != '[object Array]') {
                excludedElements = [excludedElements];
            }
            for (var i = 0; i < excludedElements.length; i++) {
                ignoredElements.push(excludedElements[i]);
            }
        }
        //var elems = container.select('select', 'input');
        var elems = Element.select(container, ['select', 'input', 'textarea', 'button', 'img']);
        var isDisabled = (checked != undefined ? checked : checkbox.checked);
        elems.each(function (elem) {
            if (checkByProductPriceType(elem)) {
                var isIgnored = false;
                for (var i = 0; i < ignoredElements.length; i++) {
                    if (elem == ignoredElements[i]) {
                        isIgnored = true;
                        break;
                    }
                }
                if (isIgnored) {
                    return;
                }
                elem.disabled=isDisabled;
                if (isDisabled) {
                    elem.addClassName('disabled');
                } else {
                    elem.removeClassName('disabled');
                }
                if(elem.tagName == 'IMG') {
                    isDisabled ? elem.hide() : elem.show();
                }
            }
        })
    }
} 


