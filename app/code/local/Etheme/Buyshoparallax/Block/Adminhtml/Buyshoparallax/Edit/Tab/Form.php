<?php
/**
 * @version   1.0 14.08.2012
 * @author    TonyEcommerce http://www.TonyEcommerce.com <support@TonyEcommerce.com>
 * @copyright Copyright (c) 2012 TonyEcommerce
 */

class Etheme_Buyshoparallax_Block_Adminhtml_Buyshoparallax_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset_slide = $form->addFieldset('buyshoparallax_form_2', array('legend'=>Mage::helper('buyshoparallax')->__('For advanced user, leave it empty to switch on next options')));
      //$fieldset = $form->addFieldset('buyshoparallax_form', array('legend'=>Mage::helper('buyshoparallax')->__('Create your parallax slide by preset options (only if you did not fill the "Slide HTML code")')));


	  $data = array();
	  if (Mage::getSingleton('adminhtml/session')->getbuyshoparallaxData())
      {
	  	$data = Mage::getSingleton('adminhtml/session')->getbuyshoparallaxData();
	  } elseif ( Mage::registry('buyshoparallax_data') ) {
		$data = Mage::registry('buyshoparallax_data')->getData();
	  }


      if (!Mage::app()->isSingleStoreMode()) {
          $fieldset_slide->addField('store_id', 'multiselect', array(
              'name' => 'stores[]',
              'label' => Mage::helper('buyshoparallax')->__('Store View'),
              'title' => Mage::helper('buyshoparallax')->__('Store View'),
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
          'label'     => Mage::helper('buyshoparallax')->__('Link'),
          'required'  => false,
          'name'      => 'link',
          'index'      => 'link',
      ));

      $fieldset_slide->addField('slide_html_code', 'textarea', array(
          'label'     => Mage::helper('buyshoparallax')->__('Slide HTML code'),
          'required'  => false,
          'name'      => 'slide_html_code',
          'index'      => 'slide_html_code',
          'note'      => 'examples of the slide html you can find here <br/> /app/code/local/Etheme/Buyshoparallax/Model/data_slides.xml',
      ));

      $fieldset_slide->addField('status', 'select', array(
          'label'     => Mage::helper('buyshoparallax')->__('Status'),
          'name'      => 'status',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('buyshoparallax')->__('Active'),
              ),
              array(
                  'value'     => 2,
                  'label'     => Mage::helper('buyshoparallax')->__('Inactive'),
              ),
          ),
      ));

      if ( Mage::getSingleton('adminhtml/session')->getbuyshoparallaxData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getbuyshoparallaxData());
          Mage::getSingleton('adminhtml/session')->setbuyshoparallaxData(null);
      } elseif ( Mage::registry('buyshoparallax_data') ) {
          $form->setValues(Mage::registry('buyshoparallax_data')->getData());
      }
      return parent::_prepareForm();
  }
}