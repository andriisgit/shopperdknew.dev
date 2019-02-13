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

class Nybohansen_Pacsoft_Block_Adminhtml_Rates extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        // The blockGroup must match the first half of how we call the block, and controller matches the second half
        // ie. foo_bar/adminhtml_baz
        $this->_blockGroup = 'pacsoft';
        $this->_controller = 'adminhtml_rates';
        $this->_headerText = $this->__('Manage rates');

        parent::__construct();

        $this->removeButton('add');
        $this->_addButton('add', array(
            'label'     => $this->__('Add new shipping rate'),
            'onclick'   => "setLocation('".$this->getUrl('*/*/edit')."')",
        ));

        $this->setTemplate('pacsoft/rates.phtml');
    }
}