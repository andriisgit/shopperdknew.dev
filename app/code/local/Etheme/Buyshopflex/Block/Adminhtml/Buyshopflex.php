<?php
/**
 * @version   1.0 14.08.2012
 * @author    TonyEcommerce http://www.TonyEcommerce.com <support@TonyEcommerce.com>
 * @copyright Copyright (c) 2012 TonyEcommerce
 */

class Etheme_Buyshopflex_Block_Adminhtml_Buyshopflex extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	public function __construct()
	{
		$this->_controller = 'adminhtml_buyshopflex';
		$this->_blockGroup = 'buyshopflex';
		$this->_headerText = Mage::helper('buyshopflex')->__('Item Manager');
		$this->_addButtonLabel = Mage::helper('buyshopflex')->__('Add Item');
		parent::__construct();
	}
}