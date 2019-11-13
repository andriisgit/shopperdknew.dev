<?php
/**
 * @version   1.0 14.08.2012
 * @author    TonyEcommerce http://www.TonyEcommerce.com <support@TonyEcommerce.com>
 * @copyright Copyright (c) 2012 TonyEcommerce
 */

class Etheme_Buyshopflex_Block_Adminhtml_Buyshopflex_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('buyshopflex_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('buyshopflex')->__('Item Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('buyshopflex')->__('Slide Information'),
          'title'     => Mage::helper('buyshopflex')->__('Slide Information'),
          'content'   => $this->getLayout()->createBlock('buyshopflex/adminhtml_buyshopflex_edit_tab_form')->toHtml(),
      ));
      return parent::_beforeToHtml();
  }
}