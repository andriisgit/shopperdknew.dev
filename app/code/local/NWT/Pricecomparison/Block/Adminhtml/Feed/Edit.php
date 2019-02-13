<?php
/**
 * NWT_Pricecomparison extension
 *
 * @category    NWT
 * @package     NWT_Pricecomparison
 * @copyright   Copyright (c) 2014 Nordic Web Team ( http://nordicwebteam.se/ )
 * @license     NWT Commercial License (NWTCL 1.0)
 * @author      Emil [carco] Sirbu (emil.sirbu@gmail.com)
 *
 */
/**
 * Feed admin edit block
 *
 */
class NWT_Pricecomparison_Block_Adminhtml_Feed_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     * constuctor
     * @access public
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->_blockGroup = 'pricecomparison';
        $this->_controller = 'adminhtml_feed';
        $this->_updateButton('save', 'label', Mage::helper('pricecomparison')->__('Save Feed'));
        $this->_updateButton('delete', 'label', Mage::helper('pricecomparison')->__('Delete Feed'));
        $this->_addButton('saveandcontinue', array(
            'label'        => Mage::helper('pricecomparison')->__('Save And Continue Edit'),
            'onclick'    => 'saveAndContinueEdit()',
            'class'        => 'save',
        ), -100);
        $this->_formScripts[] = "
            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }
    /**
     * get the edit form header
     * @access public
     * @return string
     */
    public function getHeaderText()
    {
        $current_feed = Mage::registry('current_feed');

        if( $current_feed  && $current_feed->getId() ) {
            return Mage::helper('pricecomparison')->__("Edit Feed '%s'", $this->htmlEscape($current_feed->getName()));
        } else {
            return Mage::helper('pricecomparison')->__('Add Feed');
        }
    }

}