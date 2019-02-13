<?php
/**
 * @version   1.0 14.08.2012
 * @author    TonyEcommerce http://www.TonyEcommerce.com <support@TonyEcommerce.com>
 * @copyright Copyright (c) 2012 TonyEcommerce
 */

class Etheme_Buyshopflex_Block_Adminhtml_Buyshopflex_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('buyshopflex_form', array('legend'=>Mage::helper('buyshopflex')->__('Slide information')));


      if (!Mage::app()->isSingleStoreMode()) {
          $fieldset->addField('store_id', 'multiselect', array(
              'name' => 'stores[]',
              'label' => Mage::helper('buyshopflex')->__('Store View'),
              'title' => Mage::helper('buyshopflex')->__('Store View'),
              'required' => true,
              'values' => Mage::getSingleton('adminhtml/system_store')
                  ->getStoreValuesForForm(false, true),
          ));
      }
      else {
          $fieldset->addField('store_id', 'hidden', array(
              'name' => 'stores[]',
              'value' => Mage::app()->getStore(true)->getId()
          ));
      }


      $fieldset->addField('link', 'text', array(
          'label'     => Mage::helper('buyshopflex')->__('Link'),
          'required'  => false,
          'name'      => 'link',
          'index'      => 'link',
      ));


	  $data = array();
	  if ( Mage::getSingleton('adminhtml/session')->getbuyshopflexData() )
      {
			$data = Mage::getSingleton('adminhtml/session')->getbuyshopflexData();
	  } elseif ( Mage::registry('buyshopflex_data') ) {
			$data = Mage::registry('buyshopflex_data')->getData();
	  }


	  $imgfront='';
      if (!empty($data['image']) )$imgfront = '<br/><a href="' . Mage::getBaseUrl('media').$data['image'] . '" target="_blank" >'."<img src=" . Mage::getBaseUrl('media').$data['image'] . " width='200px' alt='' /></a>";

      $fieldset->addField('image', 'file', array(
          'label'     => Mage::helper('buyshopflex')->__('Slide Image'),
          'name'      => 'image',
	      'note' => $imgfront,
	  ));




      $fieldset->addField('status', 'select', array(
          'label'     => Mage::helper('buyshopflex')->__('Status'),
          'name'      => 'status',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('buyshopflex')->__('Active'),
              ),
              array(
                  'value'     => 2,
                  'label'     => Mage::helper('buyshopflex')->__('Inactive'),
              ),
          ),
      ));

      if ( Mage::getSingleton('adminhtml/session')->getbuyshopflexData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getbuyshopflexData());
          Mage::getSingleton('adminhtml/session')->setbuyshopflexData(null);
      } elseif ( Mage::registry('buyshopflex_data') ) {
          $form->setValues(Mage::registry('buyshopflex_data')->getData());
      }
      return parent::_prepareForm();
  }
}