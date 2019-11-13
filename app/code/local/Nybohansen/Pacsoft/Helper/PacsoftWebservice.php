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

define ('SERVICEPOINTS_WEBSERVICE_URL', 'http://pacsoft.webservice.magentomoduler.dk/index.php');

class Nybohansen_Pacsoft_Helper_PacsoftWebservice extends Mage_Core_Helper_Abstract
{

    /* @var $_pacsoftOrder Nybohansen_Pacsoft_Model_PacsoftOrder */
    private $_pacsoftOrder;

    private $_uniqid;

    private $_returnLabel;

    private $_pacsoftUser;
    private $_pacsoftPin;
    private $_sndid;

    private $_nondelivery;

    private $_linkToPrint_sendemail;
    private $_linkToPrint_from;
    private $_linkToPrint_to;
    private $_linkToPrint_bcc;
    private $_linkToPrint_message;

    private $_emailNotification_active;
    private $_emailNotification_from;
    private $_emailNotification_cc;
    private $_emailNotification_bcc;
    private $_emailNotification_message;
    private $_emailNotification_errorto;


    public function createLabel($pacsoftOrder, $returnLabel = false){

        $this->_pacsoftOrder = $pacsoftOrder;
        $this->_returnLabel = $returnLabel;

        $this->_pacsoftOrder->setPacsoftOrderNo(uniqid());

        $this->loadConfiguration();

        $xml = $this->getXML();

        if($this->parseXMLResponse($this->post($xml))){
            $storeId = $this->_pacsoftOrder->getStoreId();
            if(mage::getStoreConfig('carriers/pacsoft/insert_comment', $storeId)){
                //Insert comment, that order information has been transfered to the Pacsoft system
                if($returnLabel){
                    $comment = Mage::helper('pacsoft')->__('Pacsoft: Returnlabel information has been transfered to the Pacsoft system');
                }else{
                    $comment = Mage::helper('pacsoft')->__('Pacsoft: Orderlabel information order has been transfered to the Pacsoft system');
                }

                $this->_pacsoftOrder->addStatusHistoryComment($comment, $this->_pacsoftOrder->getStatus());
                $this->_pacsoftOrder->save();
            }
            return true;
        }

        //Something went wrong...
        return false;;

    }

    private function loadConfiguration(){
        $storeId = $this->_pacsoftOrder->getStoreId();
        $this->_pacsoftUser = mage::getStoreConfig('carriers/pacsoft/pacsoftUser');
        $this->_pacsoftPin = mage::getStoreConfig('carriers/pacsoft/pacsoftPin');

        $this->_sndid = mage::getStoreConfig('carriers/pacsoft/PacsoftQuickId', $storeId);

        $this->_nondelivery = mage::getStoreConfig('carriers/pacsoft/nondelivery', $storeId);

        $this->_COD_referenceType = mage::getStoreConfig('carriers/pacsoft/COD_referenceType', $storeId);
        $this->_COD_account = mage::getStoreConfig('carriers/pacsoft/COD_account', $storeId);
        $this->_COD_accountType = mage::getStoreConfig('carriers/pacsoft/COD_accountType', $storeId);

        $this->_INSU_misc = mage::getStoreConfig('carriers/pacsoft/INSU_misc', $storeId);

        $this->_linkToPrint_sendemail = mage::getStoreConfig('carriers/pacsoft/linkToPrint_sendemail', $storeId);
        $this->_linkToPrint_from = mage::getStoreConfig('carriers/pacsoft/linkToPrint_from', $storeId);
        $this->_linkToPrint_to = mage::getStoreConfig('carriers/pacsoft/linkToPrint_to', $storeId);
        $this->_linkToPrint_bcc = mage::getStoreConfig('carriers/pacsoft/linkToPrint_bcc', $storeId);
        $this->_linkToPrint_message = mage::getStoreConfig('carriers/pacsoft/linkToPrint_message', $storeId);

        $this->_emailNotification_active = mage::getStoreConfig('carriers/pacsoft/emailNotification_active', $storeId);
        $this->_emailNotification_from = mage::getStoreConfig('carriers/pacsoft/emailNotification_from', $storeId);
        $this->_emailNotification_cc = mage::getStoreConfig('carriers/pacsoft/emailNotification_cc', $storeId);
        $this->_emailNotification_bcc = mage::getStoreConfig('carriers/pacsoft/emailNotification_bcc', $storeId);
        $this->_emailNotification_message = mage::getStoreConfig('carriers/pacsoft/emailNotification_message', $storeId);
        $this->_emailNotification_errorto = mage::getStoreConfig('carriers/pacsoft/emailNotification_errorto', $storeId);

    }

    private function getXML(){
        return '<?xml version="1.0" encoding="UTF-8"?>
                    <data>'.
                        $this->declareMeta().
                        $this->declareSender().
                        $this->declareReceiver().
                        $this->declareServicePointAddress().
                        $this->declareShipment().
                    '</data>';
    }

    private function declareMeta(){
        return '';
    }

    private function declareSender(){
        return '<sender sndid="'.$this->_sndid.'"></sender>';
    }

    private function declareReceiver(){
        $streets = explode('\n',$this->_pacsoftOrder->getShippingAddress()->getData("street"));
        $street0 = isset($streets[0]) ? $streets[0] : "";
        $street1 = isset($streets[1]) ? $streets[1] : "";

        $name = ($this->_pacsoftOrder->getShippingAddress() && $this->_pacsoftOrder->getShippingAddress()->getData("company")) ? $this->_pacsoftOrder->getShippingAddress()->getData("company") : $this->_pacsoftOrder->getShippingAddress()->getName();
        $contactName = ($this->_pacsoftOrder->getShippingAddress() && $this->_pacsoftOrder->getShippingAddress()->getData("company")) ? $this->_pacsoftOrder->getShippingAddress()->getName() : '';


        return '<receiver rcvid="'.htmlspecialchars($this->_pacsoftOrder->getIncrementId()).'">
                    <val n="name">'.htmlspecialchars($name).'</val>
                    <val n="address1">'.htmlspecialchars($street0).'</val>
                    <val n="address2">'.htmlspecialchars($street1).'</val>
                    <val n="zipcode">'.htmlspecialchars($this->_pacsoftOrder->getShippingAddress()->getData("postcode")).'</val>
                    <val n="city">'.htmlspecialchars($this->_pacsoftOrder->getShippingAddress()->getData("city")).'</val>
                    <val n="country">'.htmlspecialchars($this->_pacsoftOrder->getShippingAddress()->getCountry()).'</val>
                    <val n="contact">'.htmlspecialchars($contactName).'</val>
                    <val n="phone">'.htmlspecialchars($this->_pacsoftOrder->getShippingAddress()->getData("telephone")).'</val>
                    <val n="email">'.htmlspecialchars($this->_pacsoftOrder->getCustomerEmail()).'</val>
                    <val n="sms">'.htmlspecialchars($this->_pacsoftOrder->getShippingAddress()->getData("telephone")).'</val>
                </receiver>';
    }

    private function declareServicePointAddress(){
        if(!$this->servicePointOrder()){
            return '';
        }

        return '<receiver rcvid="'.htmlspecialchars($this->_pacsoftOrder->getServicePointId()).'">
                    <val n="name">'.htmlspecialchars($this->_pacsoftOrder->getServicePointName()).'</val>
                    <val n="address1">'.htmlspecialchars($this->_pacsoftOrder->getDeliveryAddress_streetName()).' '.$this->_pacsoftOrder->getDeliveryAddress_streetNumber().'</val>
                    <val n="address2"></val>
                    <val n="zipcode">'.htmlspecialchars($this->_pacsoftOrder->getDeliveryAddress_postalCode()).'</val>
                    <val n="city">'.htmlspecialchars($this->_pacsoftOrder->getDeliveryAddress_city()).'</val>
                    <val n="country">'.htmlspecialchars($this->_pacsoftOrder->getDeliveryAddress_countryCode()).'</val>
                    <val n="contact"></val>
                    <val n="email"></val>
                    <val n="sms"></val>
                </receiver>';
    }

    private function declareShipment(){
        $ret = '<shipment orderno="'.htmlspecialchars($this->_pacsoftOrder->getPacsoftOrderNo()).'">
                    <val n="from">'.htmlspecialchars($this->_sndid).'</val>
                    <val n="to">'.htmlspecialchars($this->_pacsoftOrder->getIncrementId()).'</val>';

        if($this->servicePointOrder()){
            $ret .= '<val n="agentto">'.htmlspecialchars($this->_pacsoftOrder->getServicePointId()).'</val>';
        }

        $ret .= '   <val n="reference">'.htmlspecialchars($this->_pacsoftOrder->getIncrementId()).'</val>
                    <val n="freetext1">'.htmlspecialchars($this->_pacsoftOrder->getFreetext1()).'</val>
                    <val n="freetext2">'.htmlspecialchars($this->_pacsoftOrder->getFreetext2()).'</val>
                    <val n="freetext3"></val>
                    <val n="freetext4"></val>
                    '.$this->declareServices().'
                    '.$this->declareContainer().'
                    '.$this->declareOptions().'
                </shipment>';

        return $ret;
    }

    private function declareServices(){
        //Be carefull if return label!
        return '<service srvid="'.htmlspecialchars($this->_pacsoftOrder->getShipment_type()).'">
                <val n="returnlabel">'.htmlspecialchars($this->boolToYesNoConvert($this->_returnLabel)).'</val>
                <val n="nondelivery">'.htmlspecialchars($this->_nondelivery).'</val>
                '.$this->declareAddons().'
            </service>';

    }

    private function declareContainer(){
        return '<container type="parcel">
                    <val n="copies">'.htmlspecialchars($this->_pacsoftOrder->getCopies()).'</val>
                    <val n="weight">'.htmlspecialchars($this->_pacsoftOrder->getWeight()).'</val>
                    <val n="contents">'.htmlspecialchars($this->_pacsoftOrder->getContents()).'</val>
                </container>';
    }

    private function declareOptions(){

        $ret = '';
        if($this->_linkToPrint_sendemail){
            $ret .= '<ufonline>
                         <option optid="'.htmlspecialchars(($this->returnLabel() ? 'LNKPRTR' : 'LNKPRTN')).'">
                            <val n="sendemail">'.htmlspecialchars($this->boolToYesNoConvert($this->_linkToPrint_sendemail)).'</val>
                            <val n="from">'.htmlspecialchars($this->_linkToPrint_from).'</val>
                            <val n="to">'.htmlspecialchars($this->_linkToPrint_to).'</val>
                            <val n="bcc">'.htmlspecialchars($this->_linkToPrint_bcc).'</val>
                            <val n="message">'.htmlspecialchars($this->_linkToPrint_message).'</val>
                         </option>
                     </ufonline>';

        }

        if($this->_emailNotification_active){
            $ret .= '<ufonline>
                         <option optid="enot">
                            <val n="from">'.htmlspecialchars($this->_emailNotification_from).'</val>
                            <val n="to">'.htmlspecialchars($this->_pacsoftOrder->getCustomerEmail()).'</val>
                            <val n="cc">'.htmlspecialchars($this->_emailNotification_cc).'</val>
                            <val n="bcc">'.htmlspecialchars($this->_emailNotification_bcc).'</val>
                            <val n="message">'.htmlspecialchars($this->_emailNotification_message).'</val>
                            <val n="errorto">'.htmlspecialchars($this->_emailNotification_errorto).'</val>
                         </option>
                     </ufonline>';

        }

        return $ret;
    }

    private function declareAddons(){
        $ret = '';
        //Foreach addon present on the order, generate addon XML
        foreach($this->getAddons() as $addon){
            $ret .= $this->declareAddon($addon);
        }
        return $ret;
    }

    private function declareAddon($addonId){
        $ret = '<addon adnid="'.$addonId.'">';
        switch($addonId){
            case 'COD':
                $ret .= '<val n="amount">'.htmlspecialchars($this->_pacsoftOrder->getGrandTotal()).'</val>'.
                        '<val n="reference">'.htmlspecialchars($this->_pacsoftOrder->getIncrementId()).'</val>'.
                        '<val n="referencetype">'.htmlspecialchars($this->_pacsoftOrder->getAddons_settings('COD', 'referenceType')).'</val>'.
                        '<val n="account">'.htmlspecialchars($this->_pacsoftOrder->getAddons_settings('COD', 'account')).'</val>'.
                        '<val n="accounttype">'.htmlspecialchars($this->_pacsoftOrder->getAddons_settings('COD', 'accountType')).'</val>';
                break;
            case 'INSU':
                $ret .= '<val n="amount">'.htmlspecialchars($this->_pacsoftOrder->getAddons_settings('INSU', 'amount')).'</val>'.
                        '<val n="misc">'.htmlspecialchars($this->_pacsoftOrder->getAddons_settings('INSU', 'misc')).'</val>';
                break;
            case 'NOTEMAIL':
                $ret .= '<val n="misc">'.htmlspecialchars($this->_pacsoftOrder->getCustomerEmail()).'</val>';
                break;
            case 'NOTPHONE':
                $ret .= '<val n="misc">'.htmlspecialchars($this->_pacsoftOrder->getShippingAddress()->getData("telephone")).'</val>';
                break;
            case 'NOTSMS':
                $ret .= '<val n="misc">'.htmlspecialchars($this->_pacsoftOrder->getShippingAddress()->getData("telephone")).'</val>';
                break;
            case 'VALUE':
                $ret .= '<val n="amount">'.htmlspecialchars($this->_pacsoftOrder->getGrandTotal()).'</val>';
                break;
        }
        $ret .= '</addon>';

        //Check if freetext fields is filled, otherwise remove addon
        if($addonId == 'DLVFLEX'){
            if(!($this->_pacsoftOrder->getFreetext1() || $this->_pacsoftOrder->getFreetext1())){
                $ret = '';
            }
        }

        //Check if phone field is filled, otherwise remove addon
        if($addonId == 'NOTPHONE' || $addonId == 'NOTSMS'){
            if(!$this->_pacsoftOrder->getShippingAddress()->getData("telephone")){
                $ret = '';
            }
        }

        return $ret;
    }

    private function post($xml_data){
        $url = 'https://www.unifaunonline.com/ufoweb/order?session=po_DK&user='.$this->_pacsoftUser.'&pin='.$this->_pacsoftPin.'&developerid=230004049&type=xml';
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: text/xml'));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml_data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }

    private function getAddons(){
        return $this->_pacsoftOrder->getActiveAddons();
    }

    private function servicePointOrder(){
        return in_array('PUPOPT', $this->getAddons()) && $this->_pacsoftOrder->getServicePointId();// && !$this->returnLabel();
    }

    private function returnLabel(){
        return $this->boolToYesNoConvert($this->_returnLabel) == 'yes';
    }

    private function boolToYesNoConvert($bool){
        return ($bool ? 'yes' : 'no');
    }

    private function parseXMLResponse($response){
        mage::log($response);
        $xmlResponse = simplexml_load_string($response);
        $status = $xmlResponse->xpath('/response/val[@n="status"]');

        $ret = (string)$status[0] == '201';

        if(!$ret){
            mage::log('Response from Post Danmark webservice');
            mage::log($ret);
        }

        return $ret;
    }

}