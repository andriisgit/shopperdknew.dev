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

require_once('Mage/Adminhtml/controllers/Sales/OrderController.php');

class Nybohansen_Pacsoft_Adminhtml_Sales_PacsoftOrderController extends Mage_Adminhtml_Sales_OrderController
{

    protected function _construct()
    {
    	parent::_construct();
    }

    public function massGenerateLabelsAction()
    {
        $orderIds = $this->getRequest()->getPost('order_ids', array());
        $this->massGenerateLabels($orderIds, 'DEFAULT');
    }

    public function massGenerateReturnLabelsAction()
    {
        $orderIds = $this->getRequest()->getPost('order_ids', array());
        $this->massGenerateLabels($orderIds, 'RETURN');
    }

    public function massGenerateBusinessLabelsAction()
    {
        $orderIds = $this->getRequest()->getPost('order_ids', array());
        $this->massGenerateLabels($orderIds, 'BUSINESS');
    }

    public function massGeneratePrivateLabelsAction()
    {
        $orderIds = $this->getRequest()->getPost('order_ids', array());
        $this->massGenerateLabels($orderIds, 'PRIVATE');
    }

    private function massGenerateLabels($orderIds, $type){
        $countOrder = 0;


        $lastPrintedOrder = null;
        foreach ($orderIds as $orderId) {
            /* @var $pacsoftOrder Nybohansen_Pacsoft_Model_PacsoftOrder */
            $pacsoftOrder = Mage::getModel('pacsoft/pacsoftOrder', $orderId);
            if($this->printLabel($pacsoftOrder, $type)){
                $countOrder++;
                $lastPrintedOrder = $pacsoftOrder;
            }
        }

        if ($countOrder) {
            if($countOrder == 1){
                $this->_getSession()->addSuccess($this->__('%s Post Danmark order(s) have been sent to Pacsoft for further processing. Click <a href="%s" TARGET="_blank">here</a> if you want to go to packsoftonline.', $countOrder, $this->getDirectPrintUrl($lastPrintedOrder, Mage::helper('adminhtml')->getUrl('adminhtml/sales_pacsoftOrder/index'))));
            }else{
                $this->_getSession()->addSuccess($this->__('%s Post Danmark order(s) have been sent to Pacsoft for further processing. Click <a href="%s" TARGET="_blank">here</a> if you want to go to packsoftonline.', $countOrder, $this->getDirectPrintUrl(null, Mage::helper('adminhtml')->getUrl('adminhtml/sales_pacsoftOrder/index'))));
            }
        }else{
            $this->_getSession()->addError($this->__('No orders have been transfered to Pacsoft'));
        }
        $this->_redirect('adminhtml/sales_order/index');
    }

    public function printLabelAction(){
        $orderId = $_POST['pacsoft_order_id'];

        /* @var $pacsoftOrder Nybohansen_Pacsoft_Model_PacsoftOrder */
        $pacsoftOrder = Mage::getModel('pacsoft/pacsoftOrder', $orderId);

        if($this->printLabel($pacsoftOrder, false)){
            $this->getResponse()->setBody($this->__('Label has been created, <a href="%s" target="_blank">click here for print dialog</a>', $this->getDirectPrintUrl($pacsoftOrder, Mage::helper('adminhtml')->getUrl('adminhtml/sales_pacsoftOrder/view', array('order_id' => $pacsoftOrder->getId())))));
        }else{
            $this->getResponse()->setBody($this->__('Something went wrong trying to create your label. Please check your settings'));
        }

    }

    public function printReturnLabelAction(){
        $orderId = $_POST['pacsoft_order_id'];

        /* @var $pacsoftOrder Nybohansen_Pacsoft_Model_PacsoftOrder */
        $pacsoftOrder = Mage::getModel('pacsoft/pacsoftOrder', $orderId);

        if($this->printLabel($pacsoftOrder, 'RETURN')){
            $this->getResponse()->setBody($this->__('Returnlabel has been created, <a href="%s" target="_blank">click here for print dialog</a>',$this->getDirectPrintUrl($pacsoftOrder, Mage::helper('adminhtml')->getUrl('adminhtml/sales_pacsoftOrder/view', array('order_id' => $pacsoftOrder->getId())))));
        }else{
            $this->getResponse()->setBody($this->__('Something went wrong trying to create your label. Please check your settings'));
        }

    }

    private function getDirectPrintUrl($pacsoftOrder, $returnUrl){
        /* @var $pacsoftOrder Nybohansen_Pacsoft_Model_PacsoftOrder */
        $pacsoftUser = mage::getStoreConfig('carriers/pacsoft/pacsoftUser');
        $pacsoftPin = mage::getStoreConfig('carriers/pacsoft/pacsoftPin');
        if($pacsoftOrder != null){
            $orderNo = $pacsoftOrder->getPacsoftOrderNo();
            return 'https://www.pacsoftonline.com/ext.po.dk.dk.StartEmbeddedShipmentJob?Login='.$pacsoftUser.'&Pass='.$pacsoftPin.'&Stage=PRINT&OrderNo='.$orderNo.'&Url='.$returnUrl;
        }else{
            return 'https://www.pacsoftonline.com/webapp?Env=po_DK&Action=act_SystemActions_AutoLogin&Company='.$pacsoftUser.'&User='.$pacsoftUser.'&Pass='.$pacsoftPin.'&Menu=Printing&Body=act_MenuActions_Item_ItemHandler_ShipmentJobSearchActions';
        }

    }

    //Returns true if label has been printed, otherwise false
    private function printLabel($pacsoftOrder, $type){
        /* @var $pacsoftOrder Nybohansen_Pacsoft_Model_PacsoftOrder */

        /* @var $helper Nybohansen_Pacsoft_Helper_PacsoftWebservice */
        $helper = Mage::Helper('pacsoft/PacsoftWebservice');

        if(isset($_POST['pacsoft_service'])){

            //If called from order view
            $pacsoftOrder->clearAddons_settings();

            if(isset($_POST['addons'])){
                foreach($_POST['addons'] as $addon){
                    //If called from order view, and uses service points...
                    if($addon == 'PUPOPT'){
                        $webservice = Mage::helper('pacsoft/servicePointsWebservice');

                        //Save new servicepoint and override the customer chosen
                        $servicePointId = $_POST[$addon]['servicePointId'];
                        /** @var $webservice Nybohansen_Pacsoft_Helper_ServicePointsWebservice */
                        $servicepoints_info = $webservice->getServicePoint($servicePointId, ($pacsoftOrder->getDeliveryAddress_countryCode() ? $pacsoftOrder->getDeliveryAddress_countryCode() : $pacsoftOrder->getShippingAddress()->getCountry()));

                        $pacsoftOrder->setServicePointId($servicePointId);
                        $pacsoftOrder->setServicePointName($servicepoints_info->name);
                        $pacsoftOrder->setVisitingAddress_streetName($servicepoints_info->visitingAddress->streetName);
                        $pacsoftOrder->setVisitingAddress_streetNumber($servicepoints_info->visitingAddress->streetNumber);
                        $pacsoftOrder->setVisitingAddress_postalCode($servicepoints_info->visitingAddress->postalCode);
                        $pacsoftOrder->setVisitingAddress_city($servicepoints_info->visitingAddress->city);
                        $pacsoftOrder->setVisitingAddress_countryCode($servicepoints_info->visitingAddress->countryCode);
                        $pacsoftOrder->setDeliveryAddress_streetName($servicepoints_info->deliveryAddress->streetName);
                        $pacsoftOrder->setDeliveryAddress_streetNumber($servicepoints_info->deliveryAddress->streetNumber);
                        $pacsoftOrder->setDeliveryAddress_postalCode($servicepoints_info->deliveryAddress->postalCode);
                        $pacsoftOrder->setDeliveryAddress_city($servicepoints_info->deliveryAddress->city);
                        $pacsoftOrder->setDeliveryAddress_countryCode($servicepoints_info->deliveryAddress->countryCode);
                        $pacsoftOrder->setLongitude($servicepoints_info->coordinates[0]->easting);
                        $pacsoftOrder->setLatitude($servicepoints_info->coordinates[0]->northing);
                    }else{
                        if(isset($_POST[$addon])){
                            foreach($_POST[$addon] as $addonValueKey => $addonValue){
                                $pacsoftOrder->setAddons_settings($addon, $addonValueKey, $addonValue);
                            }
                        }
                    }
                }
            }

            if(isset($_POST['addons'])){
                $pacsoftOrder->setAddons(implode(',',$_POST['addons']));
            }else{
                $pacsoftOrder->setAddons("");
            }

            $pacsoftOrder->setService($_POST['pacsoft_service']);
            $pacsoftOrder->setFreetext1($_POST['pacsoft_freetext1']);
            $pacsoftOrder->setFreetext2($_POST['pacsoft_freetext2']);
            $pacsoftOrder->setContents($_POST['pacsoft_contents']);
            $pacsoftOrder->setCopies($_POST['pacsoft_copies']);
            $pacsoftOrder->setWeight($_POST['pacsoft_weight']);

            $pacsoftOrder->save();

            if($type == 'RETURN'){
                return $helper->createLabel($pacsoftOrder, true);
            }else{
                return $helper->createLabel($pacsoftOrder, false);
            }
        }else{
            if($type == 'RETURN'){
                return $helper->createLabel($pacsoftOrder, true);
            }elseif($type == 'BUSINESS'){
                //Standard business label
                $pacsoftOrder->setService('PDKEP');
                return $helper->createLabel($pacsoftOrder, false);
            }elseif($type == 'PRIVATE'){
                //Standard private label
                $pacsoftOrder->clearAddons_settings();
                $pacsoftOrder->setService('P19DK');
                return $helper->createLabel($pacsoftOrder, false);
            }else{
                //Standard label based on shipping method
                if($pacsoftOrder->isPostDanmarkOrder()){
                    return $helper->createLabel($pacsoftOrder, false);
                }else{
                    //not a pacsoft order
                    return false;
                }
            }
        }
    }

    public function getAddonsForServiceAction(){
        $serviceCode = $this->getRequest()->getPost('serviceCode');
        $orderId = $this->getRequest()->getPost('orderId');

        $block = Mage::getSingleton('core/layout')->createBlock('pacsoft/adminhtml_orderAddons');
        $block->setData('serviceCode', $serviceCode);
        $block->setData('orderId', $orderId);

        $this->getResponse()->setHeader('Content-type', 'application/json');
        $this->getResponse()->setBody(json_encode($block->toHtml()));

    }

}
