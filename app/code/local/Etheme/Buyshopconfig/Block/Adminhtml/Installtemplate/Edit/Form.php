<?php
/**
 * @version   1.0 14.08.2012
 * @author    TonyEcommerce http://www.TonyEcommerce.com <support@TonyEcommerce.com>
 * @copyright Copyright (c) 2012 TonyEcommerce
 */

class Etheme_Buyshopconfig_Block_Adminhtml_Installtemplate_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form_builder = new Varien_Data_Form();
        $form_builder->setMethod('post');
        $form_builder->setAction($this->getUrl('*/*/install'));
        $form_builder->setUseContainer(true);
        $form_builder->setId('edit_form');
        $this->setForm($form_builder);
        
        return parent::_prepareForm();
    }
}
