<?php
/**
 * Nybohansen ApS
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This package designed for Magento COMMUNITY edition
 * We do not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * In case of incorrect edition usage, we don't provide support.
 * =================================================================
 *
 * @category   Nybohansen
 * @package    Nybohansen_Pacsoft
 * @copyright  Copyright (c) 2014 Nybohansen ApS
 * @license    LICENSE.txt
 */

class Nybohansen_Pacsoft_Block_Adminhtml_Rates_Renderer_Country extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

    public function render(Varien_Object $row)
    {
        $countries = explode(',',trim($row->getCountry(),','));
        $ret = '';
        $n = 0;
        foreach($countries as $countryCode){
            $countryName = Mage::app()->getLocale()->getCountryTranslation($countryCode);
            $ret .= $countryName.', ';
            if($n == 10){
                $ret .= "...";
                break;
            }
            $n++;
        }
        return trim($ret,', ');

    }
}