<?php
/**
 * @version   1.0 14.08.2012
 * @author    TonyEcommerce http://www.TonyEcommerce.com <support@TonyEcommerce.com>
 * @copyright Copyright (c) 2012 TonyEcommerce
 */

class Etheme_Buyshoprevolution_Block_Adminhtml_Buyshoprevolution_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form(array(
	          'id' => 'edit_form',
	          'action' => $this->getUrl('*/*/save', array('id' => $this->getRequest()->getParam('id'))),
	          'method' => 'post',
	          'enctype' => 'multipart/form-data'
	       )
      );

      $form->setUseContainer(true);
      $this->setForm($form);
      return parent::_prepareForm();
  }
}