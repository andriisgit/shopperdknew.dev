<?php
/**
 * @version   1.0 14.08.2012
 * @author    TonyEcommerce http://www.TonyEcommerce.com <support@TonyEcommerce.com>
 * @copyright Copyright (c) 2012 TonyEcommerce
 */

class Etheme_Buyshoparallax_Block_Renderer_Image extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row) {

        $value = $row->getData($this->getColumn()->getIndex());
        $value = str_replace("no_selection", "", $value);
    	return '<center><img src="'.Mage::getBaseUrl('media') . $value. '"  width="400"/></center>';
    }

}