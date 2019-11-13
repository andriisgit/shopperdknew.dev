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

class Nybohansen_Pacsoft_Block_Adminhtml_Addons extends Mage_Core_Block_Template
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('pacsoft/addons.phtml');
    }

    public function getAddons(){
        $rateId = $this->getData('rateId');
        $serviceCode = $this->getData('serviceCode');

        $model = Mage::getSingleton('pacsoft/rates')->load($rateId);;

        $helper = Mage::helper('pacsoft/rates');
        $addons = $helper->getAddonsForService($serviceCode);

        $ret = array();
        foreach($addons as $key => $value){
            $ret[$key] = array('desc' => $value, 'checked' => (in_array($key, $model->getAddons()) && $model->getShipmentType() == $serviceCode) );
        }

        return $ret;
    }



}