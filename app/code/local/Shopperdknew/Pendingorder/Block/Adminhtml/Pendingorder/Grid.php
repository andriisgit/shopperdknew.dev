<?php

class Shopperdknew_Pendingorder_Block_Adminhtml_Pendingorder_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('pendingorderGrid');
      $this->setDefaultSort('pendingorder_id');
      $this->setDefaultDir('ASC');
      $this->setSaveParametersInSession(true);
  }
}