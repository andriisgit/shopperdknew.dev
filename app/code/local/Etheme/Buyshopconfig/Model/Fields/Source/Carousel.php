<?php
/**
 * @version   1.0 14.08.2012
 * @author    TonyEcommerce http://www.TonyEcommerce.com <support@TonyEcommerce.com>
 * @copyright Copyright (c) 2012 TonyEcommerce
 */

class Etheme_Buyshopconfig_Model_Fields_Source_Carousel
{
    public function toOptionArray()
    {
        return array(
            array('value'=>'tabs','label' => Mage::helper('buyshopconfig')->__('a (tabs)')),
            array('value'=>'2_1','label' => Mage::helper('buyshopconfig')->__('b (2+1)')),
            array('value'=>'3_rows','label' => Mage::helper('buyshopconfig')->__('c (3 rows)')),
            array('value'=>'0','label' => Mage::helper('buyshopconfig')->__('Disable')),
        );
    }
}
