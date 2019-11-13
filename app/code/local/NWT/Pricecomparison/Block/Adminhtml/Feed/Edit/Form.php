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
 * Feed edit form
 *
 */
class NWT_Pricecomparison_Block_Adminhtml_Feed_Edit_Form extends Mage_Adminhtml_Block_Widget_Form{
    /**
     * prepare form
     * @access protected
     * @return NWT_Pricecomparison_Block_Adminhtml_Feed_Edit_Form
     */
    protected function _prepareForm() {

        $form = new Varien_Data_Form(array(
                        'id'         => 'edit_form',
                        'action'     => $this->getUrl('*/*/save', array('id' => $this->getRequest()->getParam('id'))),
                        'method'     => 'post',
                        //'enctype'    => 'multipart/form-data'
                    )
        );
        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }
}