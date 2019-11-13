<?php
class Shopperdknew_Pendingorder_Block_Adminhtml_Pendingorderd extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {

    $this->_controller = 'adminhtml_pendingorderd';
    $this->_blockGroup = 'pendingorder';
    //$this->_headerText = Mage::helper('pendingorder')->__('Pending items');
    //$this->_addButtonLabel = Mage::helper('pendingorder')->__('Add Item');

      parent::__construct();
      $this->setTemplate('shopperdknew/pendingorder/listbydate.phtml');

  }
}