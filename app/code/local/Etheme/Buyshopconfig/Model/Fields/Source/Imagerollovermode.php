<?php
/**
 * @version   1.0 14.08.2012
 * @author    TonyEcommerce http://www.TonyEcommerce.com <support@TonyEcommerce.com>
 * @copyright Copyright (c) 2012 TonyEcommerce
 */

class Etheme_Buyshopconfig_Model_Fields_Source_Imagerollovermode
{
    public function toOptionArray()
    {
        return array(
            array('value'=>'imagerollover','label' => Mage::helper('buyshopconfig')->__('Image rollover')),
            array('value'=>'image_gallery','label' => Mage::helper('buyshopconfig')->__('Info block with image gallery')),
        );
    }
}
