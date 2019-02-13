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

class Nybohansen_Economic2_Block_Adminhtml_Sales_OrderInfoGrid_Renderer_Order extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

    public function render(Varien_Object $row)
    {
        $ret = '';

        if($row->getData('economic_order_id') != '' && $row->getData('economic_invoice_id') == ''){
            $ret = '<a href="'.$this->getUrl('*/sales_orderInfoGrid/downloadOrderPdf', array('economic_order_id' => $row->getData('economic_order_id'),
                                                                                             'storeid' => $row->getStoreId())).'">'.$row->getData('economic_order_id').'</a>';
            if($row->getData('credit_order_id') != ''){
                $ret .= '</br>(<a href="'.$this->getUrl('*/sales_orderInfoGrid/downloadOrderPdf', array('economic_order_id' => $row->getData('credit_order_id'),
                    'storeid' => $row->getStoreId())).'">'.$row->getData('credit_order_id').'</a>)';
            }
        }else{
            $ret = $row->getData('economic_order_id');
        }

        return $ret;
    }
}