<?php
/**
 * @version   1.0 14.08.2012
 * @author    TonyEcommerce http://www.TonyEcommerce.com <support@TonyEcommerce.com>
 * @copyright Copyright (c) 2012 TonyEcommerce
 */

class Etheme_Buyshoparallax_Block_Adminhtml_Buyshoparallax_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();

        $this->_blockGroup = 'buyshoparallax';
        $this->_controller = 'adminhtml_buyshoparallax';
        $this->_mode = 'edit';
        
        $this->_addButton('save_and_continue', array(
                  'label' => Mage::helper('adminhtml')->__('Save And Continue Edit'),
                  'onclick' => 'saveAndContinueEdit()',
                  'class' => 'save',
        ), -100);

        $this->_updateButton('save', 'label', Mage::helper('buyshoparallax')->__('Save Slide'));
        $this->_updateButton('delete', 'label', Mage::helper('buyshoparallax')->__('Delete Slide'));

        $this->_formScripts[] = "
            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( !(Mage::registry('buyshoparallax_data') && Mage::registry('buyshoparallax_data')->getId()) )
            return Mage::helper('buyshoparallax')->__("Edit slide");


    }
}