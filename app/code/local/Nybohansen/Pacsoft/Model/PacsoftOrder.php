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

class Nybohansen_Pacsoft_Model_PacsoftOrder extends Mage_Sales_Model_Order
{

    public $_pacsoftOrderInfo;

    public $_addonsValues;

    private $_pacsoftOrderNo;

    public function __construct($orderId)
    {
        parent::_construct();
        parent::load($orderId);
        if(!$this->getIsVirtual()){
            $this->loadPacsoftOrderInfo();
            $this->loadAddonsSettings();
        }
    }

    public function getService(){
        if(isset($this->_pacsoftOrderInfo['shipment_type'])){
            return $this->_pacsoftOrderInfo['shipment_type'];
        }else{
            return Mage::getStoreConfig('carriers/pacsoft/default_service', $this->getStoreId());
        }
    }

    public function setService($value){
        $this->_pacsoftOrderInfo['shipment_type'] = $value;
    }

    public function getActiveAddons(){
        return explode(',', $this->getAddons());
    }

    private function loadPacsoftOrderInfo(){
        $pacsoftOrderInfo = Mage::getModel('pacsoft/orderInfo')->getCollection()->addFilter('order_id', $this->getIncrementId());
        $this->_pacsoftOrderInfo = $pacsoftOrderInfo->getFirstItem()->getData();
    }

    public function save(){
        //Serialize and save into DB
        $relation = Mage::getModel('pacsoft/orderInfo')->load($this->getIncrementId(), 'order_id');

        $relation->setData(array('line_id'                      => $relation->getData('line_id'),
                                 'order_id'                     => $this->getIncrementId(),
                                 'servicePointId'               => $this->_pacsoftOrderInfo['servicePointId'],
                                 'name'                         => $this->_pacsoftOrderInfo['name'],
                                 'visitingAddress_streetName'   => $this->_pacsoftOrderInfo['visitingAddress_streetName'],
                                 'visitingAddress_streetNumber' => $this->_pacsoftOrderInfo['visitingAddress_streetNumber'],
                                 'visitingAddress_postalCode'   => $this->_pacsoftOrderInfo['visitingAddress_postalCode'],
                                 'visitingAddress_city'         => $this->_pacsoftOrderInfo['visitingAddress_city'],
                                 'visitingAddress_countryCode'  => $this->_pacsoftOrderInfo['visitingAddress_countryCode'],
                                 'deliveryAddress_streetName'   => $this->_pacsoftOrderInfo['deliveryAddress_streetName'],
                                 'deliveryAddress_streetNumber' => $this->_pacsoftOrderInfo['deliveryAddress_streetNumber'],
                                 'deliveryAddress_postalCode'   => $this->_pacsoftOrderInfo['deliveryAddress_postalCode'],
                                 'deliveryAddress_city'         => $this->_pacsoftOrderInfo['deliveryAddress_city'],
                                 'deliveryAddress_countryCode'  => $this->_pacsoftOrderInfo['deliveryAddress_countryCode'],
                                 'longitude'                    => $this->_pacsoftOrderInfo['longitude'],
                                 'latitude'                     => $this->_pacsoftOrderInfo['latitude'],
                                 'shipment_type'                => $this->getService(),
                                 'addons'                       => $this->_pacsoftOrderInfo['addons'],
                                 'addons_settings'              => json_encode($this->_addonsValues),
                                 'freetext1'                    => $this->_pacsoftOrderInfo['freetext1'],
                                 'freetext2'                    => $this->_pacsoftOrderInfo['freetext2'],
                                 'contents'                     => $this->_pacsoftOrderInfo['contents'],
                                 'copies'                       => $this->_pacsoftOrderInfo['copies'],
                                 'weight'                       => $this->_pacsoftOrderInfo['weight']));
        $relation->save();
        parent::save();
    }

    private function loadAddonsSettings(){
        //Load from DB and unserialize
        $relation = Mage::getModel('pacsoft/orderInfo')->load($this->getIncrementId(), 'order_id');
        $this->_addonsValues = json_decode($relation->getData('addons_settings'), true);

        $storeId = $this->getStoreId();
        $this->_addonsValues['COD']['amount'] = isset($this->_addonsValues['COD']['amount']) ? $this->_addonsValues['COD']['amount'] :  $this->getGrandTotal();
        $this->_addonsValues['COD']['reference'] = isset($this->_addonsValues['COD']['reference']) ? $this->_addonsValues['COD']['reference'] : $this->getIncrementId();
        $this->_addonsValues['COD']['referenceType'] = isset($this->_addonsValues['COD']['referenceType']) ? $this->_addonsValues['COD']['referenceType'] : mage::getStoreConfig('carriers/pacsoft/COD_referenceType', $storeId);
        $this->_addonsValues['COD']['account'] = isset($this->_addonsValues['COD']['account']) ? $this->_addonsValues['COD']['account'] : mage::getStoreConfig('carriers/pacsoft/COD_account', $storeId);
        $this->_addonsValues['COD']['accountType'] = isset($this->_addonsValues['COD']['accountType']) ? $this->_addonsValues['COD']['accountType'] : mage::getStoreConfig('carriers/pacsoft/COD_accountType', $storeId);

        $this->_addonsValues['INSU']['amount'] = isset($this->_addonsValues['INSU']['amount']) ? $this->_addonsValues['INSU']['amount'] : $this->getGrandTotal();
        $this->_addonsValues['INSU']['misc'] = isset($this->_addonsValues['INSU']['misc']) ? $this->_addonsValues['INSU']['misc'] : mage::getStoreConfig('carriers/pacsoft/INSU_misc', $storeId);

        $this->_addonsValues['VALUE']['amount'] = isset($this->_addonsValues['VALUE']['amount']) ? $this->_addonsValues['VALUE']['amount'] : $this->getGrandTotal();

        $this->_addonsValues['NOTSMS']['misc'] = isset($this->_addonsValues['NOTSMS']['misc']) ? $this->_addonsValues['NOTSMS']['misc'] : $this->getShippingAddress()->getTelephone();
        $this->_addonsValues['NOTEMAIL']['misc'] = isset($this->_addonsValues['NOTEMAIL']['misc']) ? $this->_addonsValues['NOTEMAIL']['misc'] : $this->getCustomerEmail();
        $this->_addonsValues['NOTPHONE']['misc'] = isset($this->_addonsValues['NOTPHONE']['misc']) ? $this->_addonsValues['NOTPHONE']['misc'] : $this->getShippingAddress()->getData("telephone");
    }

    public function isPostDanmarkOrder(){
        if(!$this->getShippingCarrier())
            return false;

        return $this->getShippingCarrier()->getCarrierCode() == 'pacsoft';
    }

    public function getServicePointId(){
        return isset($this->_pacsoftOrderInfo['servicePointId']) ? $this->_pacsoftOrderInfo['servicePointId'] : '';
    }

    public function getServicePointName(){
        return isset($this->_pacsoftOrderInfo['name']) ? $this->_pacsoftOrderInfo['name'] : '';
    }

    public function getVisitingAddress_streetName(){
        return isset($this->_pacsoftOrderInfo['visitingAddress_streetName']) ? $this->_pacsoftOrderInfo['visitingAddress_streetName'] : '';
    }

    public function getVisitingAddress_streetNumber(){
        return isset($this->_pacsoftOrderInfo['visitingAddress_streetNumber']) ? $this->_pacsoftOrderInfo['visitingAddress_streetNumber'] : '';
    }

    public function getVisitingAddress_postalCode(){
        return isset($this->_pacsoftOrderInfo['visitingAddress_postalCode']) ? $this->_pacsoftOrderInfo['visitingAddress_postalCode'] : '';
    }

    public function getVisitingAddress_city(){
        return isset($this->_pacsoftOrderInfo['visitingAddress_city']) ? $this->_pacsoftOrderInfo['visitingAddress_city'] : '';
    }

    public function getVisitingAddress_countryCode(){
        return isset($this->_pacsoftOrderInfo['visitingAddress_countryCode']) ? $this->_pacsoftOrderInfo['visitingAddress_countryCode'] : '';
    }

    public function getDeliveryAddress_streetName(){
        return isset($this->_pacsoftOrderInfo['deliveryAddress_streetName']) ? $this->_pacsoftOrderInfo['deliveryAddress_streetName'] : '';
    }

    public function getDeliveryAddress_streetNumber(){
        return isset($this->_pacsoftOrderInfo['deliveryAddress_streetNumber']) ? $this->_pacsoftOrderInfo['deliveryAddress_streetNumber'] : '';
    }

    public function getDeliveryAddress_postalCode(){
        return isset($this->_pacsoftOrderInfo['deliveryAddress_postalCode']) ? $this->_pacsoftOrderInfo['deliveryAddress_postalCode'] : '';
    }

    public function getDeliveryAddress_city(){
        return isset($this->_pacsoftOrderInfo['deliveryAddress_city']) ? $this->_pacsoftOrderInfo['deliveryAddress_city'] : '';
    }

    public function getDeliveryAddress_countryCode(){
        return isset($this->_pacsoftOrderInfo['deliveryAddress_countryCode']) ? $this->_pacsoftOrderInfo['deliveryAddress_countryCode'] : '';
    }

    public function getLongitude(){
        return isset($this->_pacsoftOrderInfo['longitude']) ? $this->_pacsoftOrderInfo['longitude'] : '';
    }

    public function getLatitude(){
        return isset($this->_pacsoftOrderInfo['latitude']) ? $this->_pacsoftOrderInfo['latitude'] : '';
    }

    public function getShipment_type(){
        return $this->getService();
    }

    public function getAddons(){
        return isset($this->_pacsoftOrderInfo['addons']) ? $this->_pacsoftOrderInfo['addons'] : '';
    }

    public function getAddons_settings($addon, $setting){
        return $this->_addonsValues[$addon][$setting];
    }

    public function getFreetext1(){
        return isset($this->_pacsoftOrderInfo['freetext1']) ? $this->_pacsoftOrderInfo['freetext1'] : '';
    }

    public function getFreetext2(){
        return isset($this->_pacsoftOrderInfo['freetext2']) ? $this->_pacsoftOrderInfo['freetext2'] : '';
    }

    public function getContents(){
        return isset($this->_pacsoftOrderInfo['contents']) ? $this->_pacsoftOrderInfo['contents'] : '';
    }

    public function getCopies(){
        return isset($this->_pacsoftOrderInfo['copies']) ? $this->_pacsoftOrderInfo['copies'] : 1;
    }

    public function getWeight(){
        if(isset($this->_pacsoftOrderInfo['weight']) && $this->_pacsoftOrderInfo['weight']>0){
            $weight = $this->_pacsoftOrderInfo['weight'];
        }else{
            $weight = 0;
            foreach ($this->getAllItems() as $_item) {
                $weight += $_item->getWeight() * $_item->getQtyOrdered();
            }
            $weight = $weight / mage::getStoreConfig('carriers/pacsoft/weightUnit', $this->getStoreId());
        }
        $this->_pacsoftOrderInfo['weight'] = $weight;
        return max(0.01, $this->_pacsoftOrderInfo['weight']);
    }

    public function setServicePointId($value){
        $this->_pacsoftOrderInfo['servicePointId'] = $value;
    }

    public function setServicePointName($value){
        $this->_pacsoftOrderInfo['name'] = $value;
    }

    public function setVisitingAddress_streetName($value){
        $this->_pacsoftOrderInfo['visitingAddress_streetName'] = $value;
    }

    public function setVisitingAddress_streetNumber($value){
        $this->_pacsoftOrderInfo['visitingAddress_streetNumber'] = $value;
    }

    public function setVisitingAddress_postalCode($value){
        $this->_pacsoftOrderInfo['visitingAddress_postalCode'] = $value;
    }

    public function setVisitingAddress_city($value){
        $this->_pacsoftOrderInfo['visitingAddress_city'] = $value;
    }

    public function setVisitingAddress_countryCode($value){
        $this->_pacsoftOrderInfo['visitingAddress_countryCode'] = $value;
    }

    public function setDeliveryAddress_streetName($value){
        $this->_pacsoftOrderInfo['deliveryAddress_streetName'] = $value;
    }

    public function setDeliveryAddress_streetNumber($value){
        $this->_pacsoftOrderInfo['deliveryAddress_streetNumber'] = $value;
    }

    public function setDeliveryAddress_postalCode($value){
        $this->_pacsoftOrderInfo['deliveryAddress_postalCode'] = $value;
    }

    public function setDeliveryAddress_city($value){
        $this->_pacsoftOrderInfo['deliveryAddress_city'] = $value;
    }

    public function setDeliveryAddress_countryCode($value){
        $this->_pacsoftOrderInfo['deliveryAddress_countryCode'] = $value;
    }

    public function setLongitude($value){
        $this->_pacsoftOrderInfo['longitude'] = $value;
    }

    public function setLatitude($value){
        $this->_pacsoftOrderInfo['latitude'] = $value;
    }

    public function setAddons($value){
        $this->_pacsoftOrderInfo['addons'] = $value;
    }

    public function setAddons_settings($addon, $setting, $value){
        $this->_addonsValues[$addon][$setting] = $value;
    }

    public function clearAddons_settings(){
        $this->_addonsValues = array();
    }

    public function setFreetext1($value){
        $this->_pacsoftOrderInfo['freetext1'] = $value;
    }

    public function setFreetext2($value){
        $this->_pacsoftOrderInfo['freetext2'] = $value;
    }

    public function setContents($value){
        $this->_pacsoftOrderInfo['contents'] = $value;
    }

    public function setCopies($value){
        $this->_pacsoftOrderInfo['copies'] = $value;
    }

    public function setWeight($value){
        $this->_pacsoftOrderInfo['weight'] = $value;
    }

    public function setPacsoftOrderNo($value){
        $this->_pacsoftOrderNo = $value;
    }

    public function getPacsoftOrderNo(){
        return $this->_pacsoftOrderNo;
    }
}