<?php
/**
 * @version   1.0 14.08.2012
 * @author    TonyEcommerce http://www.TonyEcommerce.com <support@TonyEcommerce.com>
 * @copyright Copyright (c) 2012 TonyEcommerce
 */

class Etheme_Buyshopflex_Block_Adminhtml_Buyshopflex_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();

        $this->_blockGroup = 'buyshopflex';
        $this->_controller = 'adminhtml_buyshopflex';
        $this->_mode = 'edit';
        
        $this->_addButton('save_and_continue', array(
                  'label' => Mage::helper('adminhtml')->__('Save And Continue Edit'),
                  'onclick' => 'saveAndContinueEdit()',
                  'class' => 'save',
        ), -100);

        $this->_updateButton('save', 'label', Mage::helper('buyshopflex')->__('Save Slide'));
        $this->_updateButton('delete', 'label', Mage::helper('buyshopflex')->__('Delete Slide'));

        $this->_formScripts[] = "
            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( !(Mage::registry('buyshopflex_data') && Mage::registry('buyshopflex_data')->getId()) )
            return Mage::helper('buyshopflex')->__("Edit Item '%s'", $this->escapeHtml(Mage::registry('buyshopflex_data')->getLink()));


    }
}