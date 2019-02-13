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


class Nybohansen_Pacsoft_Model_TrackAndTrace extends Varien_Object
{

    private $_session;

    public function import(){
        if(mage::getStoreConfig('carriers/pacsoft/trackandtrace_active')){
            $this->_createSession();
            $parcelData = $this->_getParcelData();
            $this->parseParcelData($parcelData);
        }
    }

    private function _createSession(){
        $client = new Zend_Soap_Client("https://www.unifaunonline.com/ws-extapi2/Authentication2?wsdl", array('encoding' => 'UTF-8','soap_version' => SOAP_1_1));
        $login = $client->Login1(array(
            'developerId' => '230004049',
            'userId' => mage::getStoreConfig('carriers/pacsoft/pacsoftUser'),
            'pass' => mage::getStoreConfig('carriers/pacsoft/pacsoftPin')
        ));
        $this->_session = $login->return;
    }

    private function _getParcelData($fetchId = 0){
        $client = new Zend_Soap_Client("https://www.unifaunonline.com/ws-extapi2/History3?wsdl", array('encoding' => 'UTF-8','soap_version' => SOAP_1_1));
        $result = $client->FetchNewShipments1(
            array('session' => $this->_session,
                  'fetchId' => $fetchId
            )
        );
        return $result;
    }

    private function parseParcelData($parcelData){
        if(isset($parcelData->return->shipments)){
            if(!is_array($parcelData->return->shipments) && $parcelData->return->shipments)
            {
                $parcelData->return->shipments = array($parcelData->return->shipments);
            }
            foreach($parcelData->return->shipments as $parcel){
                if($parcel->parcelNos && $parcel->reference){
                    /* @var $order Nybohansen_Pacsoft_Model_PacsoftOrder */
                    $orderId = Mage::getModel('sales/order')->loadByIncrementId($parcel->reference)->getId();
                    if($orderId){
                        $order = Mage::getModel('pacsoft/pacsoftOrder', $orderId);

                        $shippingCarrierTitle = Mage::getStoreConfig('carriers/pacsoft/title', $order->getStoreId());
                        $shippingCarrierCode = Mage::getModel('pacsoft/carrier_pacsoft')->getCarrierCode();

                        if($order->hasShipments()){
                            //Order has already been shipped - insert T&T
                            foreach($order->getShipmentsCollection() as $sc){
                                /* @var $shipment Mage_Sales_Model_Order_Shipment */
                                $shipment = Mage::getModel('sales/order_shipment')->load($sc->getId());

                                if($shipment->getId() != '') {

                                    if(!is_array($parcel->parcelNos)){
                                        $parcel->parcelNos = array($parcel->parcelNos);
                                    }

                                    foreach($parcel->parcelNos as $parcelNo){
                                        $addTrack = true;
                                        foreach($shipment->getTracksCollection() as $track){
                                            /* @var $track Mage_Sales_Model_Order_Shipment_Track */
                                            if($track->getNumber() == $parcelNo){
                                                $addTrack = false;
                                            }
                                        }
                                        if($addTrack){

                                            $track = Mage::getModel('sales/order_shipment_track')
                                                ->setShipment($shipment)
                                                ->setData('title', $shippingCarrierTitle)
                                                ->setData('number', $parcelNo)
                                                ->setData('carrier_code', $shippingCarrierCode)
                                                ->setData('order_id', $shipment->getData('order_id'))
                                                ->save();

                                            $track->save();
                                            $shipment->addTrack($track);
                                            $shipment->save();

                                            //Possibly send e-mail to customer
                                            if(mage::getStoreConfig('carriers/pacsoft/trackandtrace_sendEmailOnImport', $order->getStoreId())){
                                                Mage::getModel('sales/order_shipment')->load($sc->getId())->sendEmail(true,'');
                                            }

                                        }
                                    }
                                }
                            }
                        }else{
                            //Do you want to create a shipment?
                            if(mage::getStoreConfig('carriers/pacsoft/trackandtrace_createShipment')){
                                if ($order->canShip()) {
                                    try {
                                        $shipment = Mage::getModel('sales/service_order', $order)->prepareShipment($this->_getItemQtys($order));

                                        if(!is_array($parcel->parcelNos)){
                                            $parcel->parcelNos = array($parcel->parcelNos);
                                        }
                                        foreach($parcel->parcelNos as $parcelNo) {

                                            $arrTracking = array(
                                                'carrier_code' => $shippingCarrierCode,
                                                'title' => $shippingCarrierTitle,
                                                'number' => $parcelNo
                                            );

                                            $track = Mage::getModel('sales/order_shipment_track')->addData($arrTracking);
                                            $shipment->addTrack($track);

                                            // Register Shipment
                                            $shipment->register();

                                            // Save the Shipment
                                            $this->_saveShipment($shipment, $order, '');
                                        }

                                        // Finally, Save the Order
                                        $order->save();


                                    } catch (Exception $e) {
                                        throw $e;
                                    }
                                }
                            }
                        }

                    }
                }
            }
        }
        if($parcelData->return->done != 1 && $parcelData->return->minDelay<1000){
            usleep($parcelData->return->minDelay*1000);
            $result = $this->_getParcelData($parcelData->return->fetchId);
            $this->parseParcelData($result);
        }
    }

    /**
     * Get the Quantities shipped for the Order, based on an item-level
     * This method can also be modified, to have the Partial Shipment functionality in place
     *
     * @param $order Mage_Sales_Model_Order
     * @return array
     */
    protected function _getItemQtys(Mage_Sales_Model_Order $order)
    {
        $qty = array();

        foreach ($order->getAllItems() as $_eachItem) {
            if ($_eachItem->getParentItemId()) {
                $qty[$_eachItem->getParentItemId()] = $_eachItem->getQtyOrdered();
            } else {
                $qty[$_eachItem->getId()] = $_eachItem->getQtyOrdered();
            }
        }

        return $qty;
    }

    /**
     * Saves the Shipment changes in the Order
     *
     * @param $shipment Mage_Sales_Model_Order_Shipment
     * @param $order Mage_Sales_Model_Order
     * @param $customerEmailComments string
     */
    protected function _saveShipment(Mage_Sales_Model_Order_Shipment $shipment, Mage_Sales_Model_Order $order, $customerEmailComments = '')
    {
        $shipment->getOrder()->setIsInProcess(true);
        $transactionSave = Mage::getModel('core/resource_transaction')
            ->addObject($shipment)
            ->addObject($order)
            ->save();

        $emailSentStatus = $shipment->getData('email_sent');
        if (!$emailSentStatus) {
            $shipment->sendEmail(true, $customerEmailComments);
            $shipment->setEmailSent(true);
        }

        return $this;
    }

}	