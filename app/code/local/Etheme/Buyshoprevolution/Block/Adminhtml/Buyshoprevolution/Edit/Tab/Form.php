<?php
/**
 * @version   1.0 14.08.2012
 * @author    TonyEcommerce http://www.TonyEcommerce.com <support@TonyEcommerce.com>
 * @copyright Copyright (c) 2012 TonyEcommerce
 */

class Etheme_Buyshoprevolution_Block_Adminhtml_Buyshoprevolution_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset_slide = $form->addFieldset('buyshoprevolution_form_2', array('legend'=>Mage::helper('buyshoprevolution')->__('For advanced user, leave it empty to switch on next options')));
      //$fieldset = $form->addFieldset('buyshoprevolution_form', array('legend'=>Mage::helper('buyshoprevolution')->__('Create your parallax slide by preset options (only if you did not fill the "Slide HTML code")')));


	  $data = array();
	  if (Mage::getSingleton('adminhtml/session')->getbuyshoprevolutionData())
      {
	  	$data = Mage::getSingleton('adminhtml/session')->getbuyshoprevolutionData();
	  } elseif ( Mage::registry('buyshoprevolution_data') ) {
		$data = Mage::registry('buyshoprevolution_data')->getData();
	  }


      if (!Mage::app()->isSingleStoreMode()) {
          $fieldset_slide->addField('store_id', 'multiselect', array(
              'name' => 'stores[]',
              'label' => Mage::helper('buyshoprevolution')->__('Store View'),
              'title' => Mage::helper('buyshoprevolution')->__('Store View'),
              'required' => true,
              'values' => Mage::getSingleton('adminhtml/system_store')
                  ->getStoreValuesForForm(false, true),
          ));
      }
      else {
          $fieldset_slide->addField('store_id', 'hidden', array(
              'name' => 'stores[]',
              'value' => Mage::app()->getStore(true)->getId()
          ));
      }

      $fieldset_slide->addField('link', 'text', array(
          'label'     => Mage::helper('buyshoprevolution')->__('Link'),
          'required'  => false,
          'name'      => 'link',
          'index'      => 'link',
      ));

      $fieldset_slide->addField('slide_html_code', 'textarea', array(
          'label'     => Mage::helper('buyshoprevolution')->__('Slide HTML code'),
          'required'  => false,
          'name'      => 'slide_html_code',
          'index'     => 'slide_html_code',
          'note'      => 'examples of the slide html you can find here <br/> /app/code/local/Etheme/Buyshoprevolution/Model/data_slides.xml',
      ));

      $fieldset_slide->addField('status', 'select', array(
          'label'     => Mage::helper('buyshoprevolution')->__('Status'),
          'name'      => 'status',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('buyshoprevolution')->__('Active'),
              ),
              array(
                  'value'     => 2,
                  'label'     => Mage::helper('buyshoprevolution')->__('Inactive'),
              ),
          ),
      ));

      if ( Mage::getSingleton('adminhtml/session')->getbuyshoprevolutionData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getbuyshoprevolutionData());
          Mage::getSingleton('adminhtml/session')->setbuyshoprevolutionData(null);
      } elseif ( Mage::registry('buyshoprevolution_data') ) {
          $form->setValues(Mage::registry('buyshoprevolution_data')->getData());
      }
      return parent::_prepareForm();
  }
}