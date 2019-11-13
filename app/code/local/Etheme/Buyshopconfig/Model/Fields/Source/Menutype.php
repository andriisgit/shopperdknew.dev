<?php
/**
 * @version   1.0 14.08.2012
 * @author    TonyEcommerce http://www.TonyEcommerce.com <support@TonyEcommerce.com>
 * @copyright Copyright (c) 2012 TonyEcommerce
 */

class Etheme_Buyshopconfig_Model_Fields_Source_Menutype
{
    public function toOptionArray()
    {
        return array(
            array('value'=>'simple','label' => Mage::helper('buyshopconfig')->__('Simple')),
            array('value'=>'megamenu','label' => Mage::helper('buyshopconfig')->__('Megamenu')),
        );
    }
}
