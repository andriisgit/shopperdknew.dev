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
 * Feed admin edit tabs
 *
 */
class NWT_Pricecomparison_Block_Adminhtml_Feed_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs{
    /**
     * constructor
     * @access public
     * @return void
     */
    public function __construct(){
        parent::__construct();
        $this->setId('feed_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('pricecomparison')->__('Feed'));
    }
    /**
     * before render html
     * @access protected
     * @return NWT_Pricecomparison_Block_Adminhtml_Feed_Edit_Tabs
     */
    protected function _beforeToHtml() {


        $feed = Mage::helper('pricecomparison')->getCurrentFeed($this);

        if(!$feed->getId()) {
            if (!($feedType = $feed->getFeedType())) {
                $feedType = $this->getRequest()->getParam('type', null);
            }
        } else {
            $feedType = true;
        } 


        if($feedType) {
            $this->addTab('form_section', array(
                'label'        => Mage::helper('pricecomparison')->__('General Settings'),
                'title'        => Mage::helper('pricecomparison')->__('General Settings'),
                'content'     => $this->getLayout()->createBlock('pricecomparison/adminhtml_feed_edit_tab_form')->toHtml(),
            ));
            $this->addTab('attributes_section', array(
                'label'        => Mage::helper('pricecomparison')->__('Field Settings'),
                'title'        => Mage::helper('pricecomparison')->__('Fields Settings'),
                'content'     => $this->getLayout()->createBlock('pricecomparison/adminhtml_feed_edit_tab_attributes')->toHtml(),
            ));
            $this->addTab('advanced_section', array(
                'label'        => Mage::helper('pricecomparison')->__('Advanced Settings'),
                'title'        => Mage::helper('pricecomparison')->__('Advanced Settings'),
                'content'     => $this->getLayout()->createBlock('pricecomparison/adminhtml_feed_edit_tab_advanced')->toHtml(),
            ));
        } else {        //select type
            $this->addTab('form_section', array(
                'label'        => Mage::helper('pricecomparison')->__('General Settings'),
                'title'        => Mage::helper('pricecomparison')->__('General Settings'),
                'content'     => $this->getLayout()->createBlock('pricecomparison/adminhtml_feed_edit_tab_type')->toHtml(),
            ));
        }
        return parent::_beforeToHtml();
    }



}