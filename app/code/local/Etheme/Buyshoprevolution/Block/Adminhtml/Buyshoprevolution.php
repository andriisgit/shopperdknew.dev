<?php
/**
 * @version   1.0 14.08.2012
 * @author    TonyEcommerce http://www.TonyEcommerce.com <support@TonyEcommerce.com>
 * @copyright Copyright (c) 2012 TonyEcommerce
 */

class Etheme_Buyshoprevolution_Block_Adminhtml_Buyshoprevolution extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	public function __construct()
	{
		$this->_controller = 'adminhtml_buyshoprevolution';
		$this->_blockGroup = 'buyshoprevolution';
		$this->_headerText = Mage::helper('buyshoprevolution')->__('Item Manager');
		$this->_addButtonLabel = Mage::helper('buyshoprevolution')->__('Add Item');
		parent::__construct();
	}
}