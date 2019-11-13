<?php
/**
 * @version   1.0 14.08.2012
 * @author    TonyEcommerce http://www.TonyEcommerce.com <support@TonyEcommerce.com>
 * @copyright Copyright (c) 2012 TonyEcommerce
 */

class Etheme_Buyshopconfig_Model_Fields_Source_Carouselvariants2
{
    public function toOptionArray()
    {
        return array(
            array('value'=>'new_sale','label' => Mage::helper('buyshopconfig')->__('New+Sale')),
            array('value'=>'bestsellers','label' => Mage::helper('buyshopconfig')->__('Bestsellers')),
        );
    }
}
