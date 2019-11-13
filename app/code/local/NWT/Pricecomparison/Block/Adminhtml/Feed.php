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
 * Feed admin block
 *
 */
class NWT_Pricecomparison_Block_Adminhtml_Feed extends Mage_Adminhtml_Block_Widget_Grid_Container{
    /**
     * constructor
     * @access public
     * @return void
     */
    public function __construct(){
        $this->_controller         = 'adminhtml_feed';
        $this->_blockGroup         = 'pricecomparison';
        $this->_headerText         = Mage::helper('pricecomparison')->__('Feed');
        $this->_addButtonLabel     = Mage::helper('pricecomparison')->__('Add Feed');
        parent::__construct();
    }
}