<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 22.07.2015
 * Time: 16:16
 */ 
class Etheme_Buyshopconfig_Helper_ConfigurableSwatches_Mediafallback extends Mage_ConfigurableSwatches_Helper_Mediafallback {

    protected function _resizeProductImage($product, $type, $keepFrame, $image = null, $placeholder = false)
    {
        $hasTypeData = $product->hasData($type) && $product->getData($type) != 'no_selection';
        if ($image == 'no_selection') {
            $image = null;
        }
        if ($hasTypeData || $placeholder || $image) {
            $helper = Mage::helper('catalog/image')
                ->init($product, $type, $image)
                ->keepFrame(($hasTypeData || $image) ? $keepFrame : false)  // don't keep frame if placeholder
            ;

            $keep_aspect_ratio = Mage::getStoreConfig('coolbabylayout/options/keep_image_aspect_ratio');
            $aspect_ratio_width=Mage::getStoreConfig('coolbabylayout/options/image_width');
            $aspect_ratio_height=Mage::getStoreConfig('coolbabylayout/options/image_height');

            if ($type == 'small_image') {
                $w = 258;
                $h = 245;

                $helper->constrainOnly(true)->resize($w, $h);
            } else if($type == 'image')
            {
                $w = 780;
                $h = 1050;

                if(!$keep_aspect_ratio && !empty($aspect_ratio_width)){
                    $w = Mage::getStoreConfig('coolbabylayout/options/image_width');
                }

                if(!$keep_aspect_ratio && !empty($aspect_ratio_height)){
                    $h = Mage::getStoreConfig('coolbabylayout/options/image_height');
                }

                if($keep_aspect_ratio)
                {
                    $helper->constrainOnly(false)->keepAspectRatio(true)->keepFrame(false)->resize($w, $h);
                }else
                {
                    $helper->constrainOnly(true)->resize($w, $h);
                }
            }

            return (string)$helper;

        }
        return false;
    }
}