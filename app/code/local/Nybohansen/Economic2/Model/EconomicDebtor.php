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

class Nybohansen_Economic2_Model_EconomicDebtor {

    /**
     * @var Nybohansen_Economic2_Model_Eapi
     */
    private $eapi;

    public $storeId;

    public $magentoCustomerId;
    public $magentoCustomerGroupId;

    public $email;
    public $name;
    public $phone;
    public $VAT;

    //Address
    public $street;
    public $city;
    public $countryCode;
    public $countryName;
    public $postcode;

    //Shipping country code used in VAT calculation
    public $shippingCountryCode;

    public $debtorCurrency;

    public $economicDebtorId;

    /**
     * @var Nybohansen_Economic2_Model_Configuration
     */
    private $configuration;

    public function __construct(){
        $this->configuration = Mage::getModel('economic2/configuration');
        $this->economicDebtorId = null;
    }

    public static function fromMagentoCustomer(Mage_Customer_Model_Customer $customer) {
        $instance = new self();
        $instance->connectToWebservice();

        $instance->storeId = $customer->getStore()->getId();

        $instance->magentoCustomerId = $customer->getId();
        $instance->customerId = $customer->getId();
        $instance->email = $customer->getEmail();
        $instance->name = $customer->getName();

        $customer_address = Mage::getModel('customer/address')->load($customer->getDefaultBilling());
        $instance->street = $customer_address->getData('street');
        $instance->city = $customer_address->getData('city');

        $instance->countryCode = $customer_address->getData('country_id');
        $instance->countryName = Mage::app()->getLocale()->getCountryTranslation($customer_address->getData('country_id'));

        $instance->shippingCountryCode = $instance->countryCode;

        $instance->postcode = $customer_address->getData('postcode');
        $instance->phone = $customer_address->getData('telephone');
        $instance->magentoCustomerGroupId = $customer->getData('group_id');
        $instance->VAT = str_replace(' ', '', $customer->getData('taxvat'));

        return $instance;
    }

    public static function fromOrder(Mage_Sales_Model_Order $order) {
        $instance = new self();

        $instance->storeId =  $order->getStoreId();
        $instance->connectToWebservice();

        $instance->magentoCustomerId = $order->getCustomerId();
        $instance->customerId = $order->getCustomerId();
        $instance->email =  $order->getCustomerEmail();
        $instance->name = $instance->getCustomerName($order->getBillingAddress());

        $instance->street = $order->getBillingAddress()->getStreetFull();
        $instance->city = $order->getBillingAddress()->getCity();

        $instance->countryCode = $order->getBillingAddress()->getCountry();


        if($order->getShippingAddress()){
            $instance->shippingCountryCode = $order->getShippingAddress()->getCountry();
        }else{
            $instance->shippingCountryCode = $order->getBillingAddress()->getCountry();
        }

        $instance->countryName = Mage::app()->getLocale()->getCountryTranslation($order->getBillingAddress()->getCountry());

        $instance->postcode = $order->getBillingAddress()->getPostcode();
        $instance->phone = $order->getBillingAddress()->getTelephone();
        $instance->magentoCustomerGroupId =  $order->getCustomerGroupId();

        $vat = $order->getCustomerTaxvat();
        if($vat == ''){
            $vat = $order->getBillingAddress()->getVatId();
        }

        $instance->VAT = str_replace(' ', '', $vat);

        $instance->currency = $instance->eapi->currency_find_by_code($order->getOrderCurrency()->currency_code);

        return $instance;
    }

    /**
     * Sends all information to e-conomic and returns debtor handle
     */
    public function sendToEconomic(){

        $debtorHandle = $this->getDebtorHandle();

        $newlyCreated = false;
        if(!$debtorHandle){
            //Debtor does not exist, creating new
            $debtorHandle = $this->createNewDebtor();
            $newlyCreated = true;
        }

        if($newlyCreated || mage::getStoreConfig('economic2_options/debtorConfig/update_debtor_information', $this->storeId)){
            //Updating debtor with new information
            //Modify data and send to e-conomic
            $debtorData = $this->eapi->debtor_get_data($debtorHandle);
            //Update all the e-conomic debtor fields
            $this->updateData($debtorData);
            //Send to e-conomic
            $this->eapi->debtor_updateFromData($debtorData);
            //Send customer addresses to e-conomic
            $this->sendCustomerAdressesToEconomic();
        }

        return $debtorHandle;
    }

    private function getDebtorHandle(){
        $debtor_handle = false;
        if(!$this->economicDebtorId){
            //If we havent already explicetly set the debtor id, find it
            if(mage::getStoreConfig('economic2_options/debtorConfig/debtor_create_new', $this->storeId) == 0){
                //We should always use the same debtor, depending on country (home-country, EU or Abroad)
                if($this->get_debtor_vat_zone() == 'HomeCountry'){
                    $this->economicDebtorId = mage::getStoreConfig('economic2_options/debtorConfig/default_debtor_home', $this->storeId);
                }elseif($this->get_debtor_vat_zone() == 'EU'){
                    $this->economicDebtorId = mage::getStoreConfig('economic2_options/debtorConfig/default_debtor_eu', $this->storeId);
                }else{
                    $this->economicDebtorId = mage::getStoreConfig('economic2_options/debtorConfig/default_debtor_abroad', $this->storeId);
                }
            }elseif(mage::getStoreConfig('economic2_options/debtorConfig/debtor_create_new', $this->storeId)==2){
                //We should always create a debtor no matter what - just pick the next available number
                $this->economicDebtorId  = $this->eapi->debtor_get_next_available_number();
            }
        }

        if($this->economicDebtorId){
            //If the debtor number is explicitly set, we either want to load an existing debtor or create a new one with that specific id
            $debtor_handle = $this->eapi->debtor_get_by_number($this->economicDebtorId);
        }elseif(mage::getStoreConfig('economic2_options/debtorConfig/debtor_identification_field', $this->storeId) == $this->configuration->DEBTOR_IDENTIFICATION_ID){
            //Using internal id
            $relation = Mage::getModel('economic2/customerId')->load($this->customerId, 'magento_customer_id');
            if($relation->economic_customer_id){
                $debtor_handle = $this->eapi->debtor_get_by_number($relation->economic_customer_id);
            }
        }else{
            //Using CVR or e-mail
            if(mage::getStoreConfig('economic2_options/debtorConfig/debtor_identification_field', $this->storeId) == $this->configuration->DEBTOR_IDENTIFICATION_CVR){
                $debtor_handles = $this->eapi->debtor_get_by_CI_number($this->VAT);
            }else{
                $debtor_handles = $this->eapi->debtor_get_by_email($this->email);
            }

            //Check if both identification field and debtor group matches
            if($debtor_handles){
                if(is_array($debtor_handles)){


                    //if more than one debtor match
                    $debtors_data = $this->eapi->debtor_get_data_array($debtor_handles);
                    foreach ($debtors_data as $debtor_data) {

                        if(!mage::getStoreConfig('economic2_options/debtorConfig/compare_debtorgroup_when_searching_for_debtor', $this->storeId)){
                            $debtor_handle = $debtor_data->Handle;
                            break;
                        }

                        if($debtor_data->DebtorGroupHandle->Number == $this->eapi->debtor_groups_find_by_number($this->getDebtorGroupHandle($this->magentoCustomerGroupId)->Number)->Number){
                            $debtor_handle = $debtor_data->Handle;
                            //Break out of loop, we found a match!
                            break;
                        }
                    }
                }else{
                    //if a single debtor match

                    if(!mage::getStoreConfig('economic2_options/debtorConfig/compare_debtorgroup_when_searching_for_debtor', $this->storeId)){
                        $debtor_handle = $debtor_handles;
                    }else{
                        if($this->eapi->debtor_get_debtor_group($debtor_handles)->Number == $this->eapi->debtor_groups_find_by_number($this->getDebtorGroupHandle($this->magentoCustomerGroupId)->Number)->Number){
                            $debtor_handle = $debtor_handles;
                        }
                    }

                }
            }
        }
        return $debtor_handle;
    }

    private function sendCustomerAdressesToEconomic(){
        $magentoCustomer = Mage::getModel('customer/customer')->load($this->magentoCustomerId);
        /* @var $magentoCustomer Mage_Customer_Model_Customer */

        foreach($magentoCustomer->getAddressesCollection() as $address){
            $deliveryLocation = Nybohansen_Economic2_Model_EconomicDeliveryLocation::fromMagentoCustomerAddress($address);
            $deliveryLocation->sendToEconomic();
        }
    }

    private function createNewDebtor(){
        if(!$this->economicDebtorId){
            $this->economicDebtorId = $this->eapi->debtor_get_next_available_number();
        }
        $debtorHandle = $this->eapi->debtor_create_new($this->economicDebtorId,
                                                       $this->getDebtorGroupHandle(),
                                                       $this->name,
                                                       $this->get_debtor_vat_zone());

        if($this->magentoCustomerId){
            $model = Mage::getModel('economic2/customerId')->load($this->magentoCustomerId, 'magento_customer_id');

            if(!$model->getLineId()){
                //Insert the id into the e-conomic customer id table, regardless if we use the id or not when retrieving.
                $model->setData(array('magento_customer_id' => $this->magentoCustomerId,
                                      'economic_customer_id' => $debtorHandle->Number));
                $model->save();
            }else{
                $model->addData(array('economic_customer_id' => $debtorHandle->Number));
                $model->setLineId($model->getLineId())->save();
                $model->save();
            }
        }

        return $debtorHandle;
    }

    private function getCustomerName($address){
        if(mage::getStoreConfig('economic2_options/debtorConfig/use_company_name_if_available', $this->storeId) && $address->getCompany()){
            return $address->getCompany();
        }else{
            return $address->getName();
        }
    }

    private function getDebtorGroupHandle(){
        $serialized_mapping = mage::getStoreConfig('economic2_options/debtorConfig/debtor_mapping', $this->storeId);
        $tmp = @unserialize($serialized_mapping);
        foreach ($tmp as $map) {
            $mapping[$map['magento_field']][] = $map['economic_field'];
        }

        if($mapping[$this->magentoCustomerGroupId][0]){
            return $this->eapi->debtor_groups_find_by_number($mapping[$this->magentoCustomerGroupId][0]);
        }else{
            $this->add_log_entry(Zend_Log::CRIT,"The Magento customer group are not mapped to an e-conomic debtor group. Cannot create order!");
        }
        return null;
    }

    private function getPriceGroupHandle(){
        return array('Number' => 0);
    }

    private function get_debtor_vat_zone(){
        $home_countries = explode(',',mage::getStoreConfig('economic2_options/debtorConfig/vat_home_country', $this->storeId));
        $eu_countries = explode(',',mage::getStoreConfig('economic2_options/debtorConfig/vat_eu_countries', $this->storeId));

        if(in_array(strtoupper($this->shippingCountryCode), $home_countries)){
            //Home country
            return 'HomeCountry';
        }elseif(in_array(strtoupper($this->shippingCountryCode), $eu_countries)){
            //EU country
            return 'EU';
        }else{
            //Abroad
            return 'Abroad';
        }


    }

    private function updateData($data){
        if(mage::getStoreConfig('economic2_options/debtorConfig/debtor_create_new', $this->storeId) != 0) {
            $data->DebtorGroupHandle = $this->getDebtorGroupHandle();
            $data->VatZone = $this->get_debtor_vat_zone();
        }

        $data->Name = $this->name;
//        $data['CurrencyHandle'] = $this->currency;
//        $data['PriceGroupHandle'] = $this->getPriceGroupHandle();
//        $data['IsAccessible'] = '';
//        $data['Ean'] = '';
//        $data['PublicEntryNumber'] = '';
        $data->Email = $this->email;
        $data->TelephoneAndFaxNumber = $this->phone;
//        $data['Website'] = '';
        $data->Address = $this->street;
        $data->PostalCode = $this->postcode;
        $data->City = $this->city;
        $data->Country = $this->countryName;
//        $data['CreditMaximum'] = false;
//        $data->VatNumber = $this->VAT;
//        $data['County'] = '';
        $data->CINumber = $this->VAT;
//        $data['TermOfPaymentHandle'] = '';
//        $data['LayoutHandle'] = '';
//        $data['AttentionHandle'] = '';
//        $data['YourReferenceHandle'] = '';
//        $data['OurReferenceHandle'] = '';
//        $data['Balance'] = '';
        return $data;
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