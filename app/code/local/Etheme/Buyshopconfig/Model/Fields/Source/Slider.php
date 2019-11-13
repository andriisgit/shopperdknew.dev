<?php
/**
 * @version   1.0 14.08.2012
 * @author    TonyEcommerce http://www.TonyEcommerce.com <support@TonyEcommerce.com>
 * @copyright Copyright (c) 2012 TonyEcommerce
 */

class Etheme_Buyshopconfig_Model_Fields_Source_Slider
{
    public function toOptionArray()
    {
        return array(
            array('value'=>'flex','label' => Mage::helper('buyshopconfig')->__('Flexslider')),
            array('value'=>'parallax','label' => Mage::helper('buyshopconfig')->__('Parallax layered slider')),
            array('value'=>'revolution','label' => Mage::helper('buyshopconfig')->__('Revolution slider')),
            array('value'=>'image','label' => Mage::helper('buyshopconfig')->__('Static image')),
            array('value'=>'0','label' => Mage::helper('buyshopconfig')->__('Disable')),
        );
    }
}
