<?php
/**
 * @version   1.0 14.08.2012
 * @author    TonyEcommerce http://www.TonyEcommerce.com <support@TonyEcommerce.com>
 * @copyright Copyright (c) 2012 TonyEcommerce
 */

class Etheme_Buyshopconfig_Model_Fields_Source_Position
{
    public function toOptionArray()
    {
        return array(
            array('value'=>'top_left','label' => Mage::helper('buyshopconfig')->__('Top Left')),
            array('value'=>'top_right','label' => Mage::helper('buyshopconfig')->__('Top Right')),
            array('value'=>'bottom_left','label' => Mage::helper('buyshopconfig')->__('Bottom Left')),
            array('value'=>'bottom_right','label' => Mage::helper('buyshopconfig')->__('Bottom Right')),
        );
    }
}
