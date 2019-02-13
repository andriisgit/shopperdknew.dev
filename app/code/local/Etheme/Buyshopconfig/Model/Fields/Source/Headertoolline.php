<?php
/**
 * @version   1.0 14.08.2012
 * @author    TonyEcommerce http://www.TonyEcommerce.com <support@TonyEcommerce.com>
 * @copyright Copyright (c) 2012 TonyEcommerce
 */

class Etheme_Buyshopconfig_Model_Fields_Source_Headertoolline
{
    public function toOptionArray()
    {
        return array(
            array('value'=>'a','label' => Mage::helper('buyshopconfig')->__('Twitt, login, register')),
            array('value'=>'b','label' => Mage::helper('buyshopconfig')->__('Phone, message, account, language, currency')),
            array('value'=>'0','label' => Mage::helper('buyshopconfig')->__('Disable')),
        );
    }
}
