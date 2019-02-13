<?php
/**
 * @version   1.0 14.08.2012
 * @author    TonyEcommerce http://www.TonyEcommerce.com <support@TonyEcommerce.com>
 * @copyright Copyright (c) 2012 TonyEcommerce
 */

class Etheme_Buyshopconfig_Model_Fields_Source_Productinfoimagesize
{
    public function toOptionArray()
    {
        return array(
            array('value'=>'small','label' => Mage::helper('buyshopconfig')->__('Small')),
            array('value'=>'medium','label' => Mage::helper('buyshopconfig')->__('Medium')),
            array('value'=>'big','label' => Mage::helper('buyshopconfig')->__('Big')),
        );
    }
}
