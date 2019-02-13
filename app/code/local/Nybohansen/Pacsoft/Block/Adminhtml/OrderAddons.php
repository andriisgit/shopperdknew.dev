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

class Nybohansen_Pacsoft_Block_Adminhtml_OrderAddons extends Mage_Core_Block_Template
{

    /* @var $_pacsoftOrder Nybohansen_Pacsoft_Model_PacsoftOrder */
    public $_pacsoftOrder;

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('pacsoft/orderAddons.phtml');
    }

    public function getAddons(){
        $serviceCode = $this->getData('serviceCode');
        $orderId = $this->getData('orderId');

        //Service stored on order
        $this->_pacsoftOrder = Mage::getModel('pacsoft/pacsoftOrder', $orderId);
        $serviceOnOrder = $this->_pacsoftOrder->getService();

        //Addons for servicecode
        $helper = Mage::helper('pacsoft/rates');
        $addons = $helper->getAddonsForService($serviceCode);

        //Addons stored on the order
        $addonsOnOrder = $this->_pacsoftOrder->getActiveAddons();

        $ret = array();
        foreach($addons as $key => $value){
            $ret[$key] = array('desc' => $value, 'checked' => (in_array($key, $addonsOnOrder) && $serviceOnOrder == $serviceCode) );
        }

        return $ret;
    }

    public function getServices(){
        return Mage::helper('pacsoft/rates')->getServices();
    }


    public function getAccountTypes(){
        return Mage::getModel('pacsoft/system_config_source_cODAccountType')->toOptionArray();
    }

    public function servicePointSelectHtml(){

        if($this->_pacsoftOrder->getServicePointId()){
            /** @var $webservice Nybohansen_Pacsoft_Helper_ServicePointsWebservice */
            $webservice = Mage::helper('pacsoft/servicePointsWebservice');
            $servicePoints = $webservice->getServicePointsInZipcode($this->_pacsoftOrder->getVisitingAddress_postalCode(), $this->_pacsoftOrder->getVisitingAddress_countryCode());

            $options = array();
            foreach($servicePoints as $servicePoint){
                $name = ucwords(strtolower($servicePoint->name));
                $address = ucwords(strtolower($servicePoint->visitingAddress->streetName)).' '.$servicePoint->visitingAddress->streetNumber;
                $zip = $servicePoint->visitingAddress->postalCode;
                $city = $servicePoint->visitingAddress->city;
                $options[$servicePoint->servicePointId] = $name.' · '.$address.' · '.$zip.' · '.$city;

                if($this->_pacsoftOrder->getServicePointId() == $servicePoint->servicePointId){
                    $matchFound = true;
                }
            }

            $select = Mage::app()->getLayout()->createBlock('core/html_select')
                ->setName("PUPOPT[servicePointId]")
                ->setValue($this->_pacsoftOrder->getServicePointId())
                ->setOptions($options);

            return $select->getHtml();
        }else{
            return '';
        }
    }


}