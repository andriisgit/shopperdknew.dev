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
 * Feed admin grid filename column renderer
 *
 */

class NWT_Pricecomparison_Block_Adminhtml_Feed_Grid_Filename extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

public function render(Varien_Object $row) {

    $value =  $row->getData($this->getColumn()->getIndex());
    if(!$value) {
        return ' - no file -';
    }
    $file =Mage::helper('pricecomparison')->getFeedPath($value);

    if(file_exists($file)) {
        return $value.', generated on '.Mage::getModel('core/date')->date(null, filemtime($file)).', '.round(filesize($file)/1024,0).' kB'.', <a href="'.Mage::helper('pricecomparison')->getFeedUrl($value).'">download</a>';
    } else {
        return $value.' (not yet generated)';
    }
    return '<span style="color:red;">'.$value.'</span>';
}

}


