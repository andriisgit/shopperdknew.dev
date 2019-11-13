<?php
/**
 * @version   1.0 14.08.2012
 * @author    TonyEcommerce http://www.TonyEcommerce.com <support@TonyEcommerce.com>
 * @copyright Copyright (c) 2012 TonyEcommerce
 */

class Etheme_Buyshoparallax_Block_Adminhtml_Buyshoparallax extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	public function __construct()
	{
		$this->_controller = 'adminhtml_buyshoparallax';
		$this->_blockGroup = 'buyshoparallax';
		$this->_headerText = Mage::helper('buyshoparallax')->__('Item Manager');
		$this->_addButtonLabel = Mage::helper('buyshoparallax')->__('Add Item');
		parent::__construct();
	}
}