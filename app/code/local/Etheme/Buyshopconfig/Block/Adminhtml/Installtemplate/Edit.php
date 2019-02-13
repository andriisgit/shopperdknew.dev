<?php
class Etheme_Buyshopconfig_Block_Adminhtml_Installtemplate_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
        $this->_controller = 'adminhtml_installtemplate';
        $this->_blockGroup = 'buyshopconfig';
        $this->_updateButton('save', 'label', Mage::helper('buyshopconfig')->__('Auto install'));
        $this->_removeButton('back');
    }

    public function getHeaderText()
    {
        return Mage::helper('buyshopconfig')->__('Buyshop Install');
    }
}

?>