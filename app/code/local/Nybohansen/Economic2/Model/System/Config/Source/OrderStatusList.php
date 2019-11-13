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
 * @package    Nybohansen_Economic2
 * @copyright  Copyright (c) 2014 Nybohansen ApS
 * @license    LICENSE.txt
 */

class Nybohansen_Economic2_Model_System_Config_Source_OrderStatusList {


    public function toOptionArray(){
        $ret = array();
        $ret[] = array('value' => '', 'label' => Mage::helper('economic2')->__('Choose order status'));
        if(is_object(Mage::getResourceModel('sales/order_status_collection'))){
            $collection = Mage::getResourceModel('sales/order_status_collection')->joinStates();
            foreach($collection as $status){
                $ret[] = array('value' => $status['status'], 'label' =>$status['label'] );
            }
        }else{
            $collection = Mage::getSingleton('sales/order_config')->getStatuses();
            foreach($collection as $key => $status){
                $ret[] = array('value' => $key, 'label' =>$status );
            }
        }
        return $ret;

    }

}
?>