<?php
/**
 * @version   1.0 14.08.2012
 * @author    TonyEcommerce http://www.TonyEcommerce.com <support@TonyEcommerce.com>
 * @copyright Copyright (c) 2012 TonyEcommerce
 */

class Etheme_Buyshopconfig_Model_Fields_Source_Sidebar
{
    public function toOptionArray()
    {
        return array(
            array('value'=>'shop_by_sort_0','label' => Mage::helper('buyshopconfig')->__('Shop by (sort code 0)')),
            array('value'=>'price_slider_sort_1','label' => Mage::helper('buyshopconfig')->__('Ajax price slider (sort code 1)')),
            array('value'=>'tags_popular_sort_2','label' => Mage::helper('buyshopconfig')->__('Tags (sort code 2)')),
            array('value'=>'compare_sort_3','label' => Mage::helper('buyshopconfig')->__('Compare (sort code 3)')),
            array('value'=>'poll_sort_4','label' => Mage::helper('buyshopconfig')->__('Community pool (sort code 4)')),
            array('value'=>'slider_sort_5','label' => Mage::helper('buyshopconfig')->__('Banner Slider (sort code 5)')),
            array('value'=>'leftmenu_sort_6','label' => Mage::helper('buyshopconfig')->__('Leftmenu (sort code 6)')),
        );
    }
}
