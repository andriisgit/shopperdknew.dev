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
 * Html (oneline) select element
 */
class NWT_Pricecomparison_Block_Adminhtml_Form_Field_Select extends Mage_Core_Block_Html_Select 
{
 
    public function setInputName($value) {
        return $this->setName($value);
    }
}
