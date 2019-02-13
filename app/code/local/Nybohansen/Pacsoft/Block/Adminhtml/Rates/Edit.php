<?php
/**
 * Nybohansen ApS
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This package designed for Magento COMMUNITY edition
 * We do not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * In case of incorrect edition usage, we don't provide support.
 * =================================================================
 *
 * @category   Nybohansen
 * @package    Nybohansen_Pacsoft
 * @copyright  Copyright (c) 2014 Nybohansen ApS
 * @license    LICENSE.txt
 */

class Nybohansen_Pacsoft_Block_Adminhtml_Rates_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     * Init class
     */
    public function __construct()
    {
        $this->_blockGroup = 'pacsoft';
        $this->_controller = 'adminhtml_rates';
        parent::__construct();

        $this->_updateButton('save', 'label', $this->__('Save shipping rate'));
        $this->_updateButton('delete', 'label', $this->__('Delete Rate'));
    }

    /**
     * Get Header text
     *
     * @return string
     */
    public function getHeaderText()
    {
        if (Mage::registry('pacsoft_rate')->getRateId()) {
            return $this->__('Edit Rate');
        }
        else {
            return $this->__('New Rate');
        }
    }
}
