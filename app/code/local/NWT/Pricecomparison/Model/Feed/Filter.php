<?php
/**
 * NWT_Pricecomparison extension
 *
 * @category    NWT
 * @package     NWT_Pricecomparison
 * @copyright   Copyright (c) 2014 Nordic Web Team ( http://nordicwebteam.se/ )
 * @license     NWT Commercial License (NWTCL 1.0)
 * @author      Emil [carco] Sirbu (emil.sirbu@gmail.com)
 *
 */

/**
 * Feed Filters Model
 *
 */

class NWT_Pricecomparison_Model_Feed_Filter {


    public function filter_yesno($val,$yes='Yes',$no='No') {
        return $val?trim($yes):trim($no);
    }

    public function filter_striptags($val) {
        return  Mage::helper('core/string')->stripTags($val);
    }

    public function filter_htmlescape($val) {
        return  Mage::helper('core/string')->escapeHtml($val);
    }


/*
                <round label="Round (1 params: 2)" help="Round value to the specified decimals" />
                <format_price label="Format Price" help="Format price as per store configuration" />
                <convert_price label="Store (currency) Price " help="Convert price value to store currency" />
                <convert_format_price label="Store (currency) formated Price " help="Convert and format price value to store currency" />
*/

    public function filter_round($val,$decimals = 2) {
        if(is_int($decimals) && $decimals>=0) {
            $val = round($val,$decimals);
        }
        return $val;
    }


    public function filter_format_price($val) {
        return Mage::app()->getStore()->formatPrice($val, false);
    }

    public function filter_convert_price($val,$decimals=2) {
        $val = Mage::app()->getStore()->convertPrice($val, false, false);
        if(is_int($decimals) && $decimals>=0) {
            $val = round(  $val ,$decimals);
        }
        return $val;
    }

    public function filter_convert_format_price($val) {
        return Mage::app()->getStore()->convertPrice($val, true, false);
    }

}
