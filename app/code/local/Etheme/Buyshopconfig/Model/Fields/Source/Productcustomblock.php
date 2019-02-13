<?php
/**
 * @version   1.0 14.08.2012
 * @author    TonyEcommerce http://www.TonyEcommerce.com <support@TonyEcommerce.com>
 * @copyright Copyright (c) 2012 TonyEcommerce
 */

class Etheme_Buyshopconfig_Model_Fields_Source_Productcustomblock
{
    public function toOptionArray()
    {
        return array(
            array('value'=>'customhtml','label' => Mage::helper('buyshopconfig')->__('Custom HTML')),
            array('value'=>'related','label' => Mage::helper('buyshopconfig')->__('Related Products')),
            array('value'=>'0','label' => Mage::helper('buyshopconfig')->__('Disabled')),
        );
    }
}
