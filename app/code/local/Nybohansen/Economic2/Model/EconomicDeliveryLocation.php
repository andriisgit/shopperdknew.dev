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

class Nybohansen_Economic2_Model_EconomicDeliveryLocation{

    /**
     * @var Nybohansen_Economic2_Model_Eapi
     */
    private $eapi;

    private $storeId;

    public $addressId;
    public $debtorHandle;
    public $number;

    public $address;
    public $postalCode;
    public $city;
    public $country;
    public $termsOfDelivery;
    public $isAccessible;
    public $externalId;
    public $county;


    public static function fromMagentoCustomerAddress(Mage_Customer_Model_Address $address) {
        $instance = new self();
        $instance->storeId = $address->getStoreId();

        $instance->connectToWebservice();

        $instance->debtorHandle = $instance->getDebtorHandle($address->getCustomerId());

        $instance->address = $address->getStreetFull();
        $instance->postalCode = $address->getPostcode();
        $instance->city = $address->getCity();
        $instance->country = $address->getCountry();
        $instance->termsOfDelivery = '';
        $instance->isAccessible = true;
        $instance->externalId = $address->getId();
        return $instance;
    }

    public function sendToEconomic(){

        //We can only create a delivery address if the debtor exists in e-conomic
        if($this->debtorHandle){
            //Check if address already exists by using find by external id
            $deliveryLocationHandle = $this->getDeliveryLocationHandle();
            if(!$deliveryLocationHandle){
                //Creating new delivery location
                $deliveryLocationHandle = $this->eapi->deliveryLocation_create($this->debtorHandle);
            }
            $deliveryLocationData = $this->eapi->deliveryLocation_getData($deliveryLocationHandle);
            $deliveryLocationData = $this->updateDeliveryLocationData($deliveryLocationData);
            return $this->eapi->deliveryLocation_updateFromData($deliveryLocationData);
        }else{
            return false;
        }

    }

    public function deleteFromEconomic(){
        $this->eapi->deliveryLocation_delete($this->getDeliveryLocationHandle());
    }

    private function getDeliveryLocationHandle(){
        return $this->eapi->deliveryLocation_findByExternalId($this->externalId);
    }

    private function getDebtorHandle($magentoCustomerId){
        $relation = Mage::getModel('economic2/customerId')->load($magentoCustomerId, 'magento_customer_id');
        $debtor_handle = false;
        if($relation->economic_customer_id){
            $debtor_handle = $this->eapi->debtor_get_by_number($relation->economic_customer_id);
        }
        return $debtor_handle;
    }

    private function updateDeliveryLocationData($deliveryLocationData){
        $deliveryLocationData->Address = $this->address;
        $deliveryLocationData->PostalCode = $this->postalCode;
        $deliveryLocationData->City = $this->city;
        $deliveryLocationData->Country = $this->country;
        $deliveryLocationData->TermsOfDelivery = $this->termsOfDelivery;
        $deliveryLocationData->IsAccessible = 1;
        $deliveryLocationData->ExternalId = $this->externalId;
        return $deliveryLocationData;
    }

    private function connectToWebservice(){
        $this->eapi = Mage::getSingleton('economic2/eapi');
        $connection_result = $this->eapi->connect($this->storeId);
        if(!$connection_result){
            $this->add_log_entry(Zend_Log::EMERG, 'Cannot connect to e-conomic');
        }
    }

    /**
     *
     * Add log entry to economic2.log
     *
     * @param $level  Zend_Log::level
     * @param $object Message to write in log
     */
    private function add_log_entry($level, $object){
        mage::log($object, $level, 'e-conomic2.log');
    }

}
