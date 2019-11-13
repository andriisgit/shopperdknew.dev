<?php
/**
 * @version   1.0 14.08.2012
 * @author    TonyEcommerce http://www.TonyEcommerce.com <support@TonyEcommerce.com>
 * @copyright Copyright (c) 2012 TonyEcommerce
 */

class Etheme_Buyshopconfig_Model_Fields_Source_Tagcloud
{
    public function toOptionArray()
    {
        return array(
            array('value'=>'flash','label' => Mage::helper('buyshopconfig')->__('Flash')),
            array('value'=>'usual','label' => Mage::helper('buyshopconfig')->__('Usual')),
            array('value'=>'js',   'label' => Mage::helper('buyshopconfig')->__('JS cloud (if flash not works)')),
        );
    }
}
