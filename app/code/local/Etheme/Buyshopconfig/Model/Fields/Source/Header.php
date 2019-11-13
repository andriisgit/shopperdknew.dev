<?php
/**
 * @version   1.0 14.08.2012
 * @author    TonyEcommerce http://www.TonyEcommerce.com <support@TonyEcommerce.com>
 * @copyright Copyright (c) 2012 TonyEcommerce
 */

class Etheme_Buyshopconfig_Model_Fields_Source_Header
{
    public function toOptionArray()
    {
        return array(
            array('value'=>'functional','label' => Mage::helper('buyshopconfig')->__('Functional')),
            array('value'=>'simple','label' => Mage::helper('buyshopconfig')->__('Simple')),
        );
    }
}
