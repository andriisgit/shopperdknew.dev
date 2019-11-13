<?php
/**
 * @version   1.0 14.08.2012
 * @author    TonyEcommerce http://www.TonyEcommerce.com <support@TonyEcommerce.com>
 * @copyright Copyright (c) 2012 TonyEcommerce
 */

class Etheme_Buyshopflex_Model_Fields_Source_Animation
{
    public function toOptionArray()
    {
        return array(
            array('value'=>'fade','label' => Mage::helper('buyshopconfig')->__('Fade')),
            array('value'=>'slide','label' => Mage::helper('buyshopconfig')->__('Slide')),
        );
    }
}
