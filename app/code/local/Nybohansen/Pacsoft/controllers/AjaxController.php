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

class Nybohansen_Pacsoft_AjaxController extends Mage_Core_Controller_Front_Action {

    public function importtrackingAction(){
        /* @var $servicePoints Nybohansen_Pacsoft_Model_TrackAndTrace */
        $tracking = Mage::getModel('pacsoft/TrackAndTrace');
        $tracking->import();
    }


	public function indexAction(){

        $action = $this->getRequest()->getParam('action');
        if($action == 'getServicePointElement'){
            $zip = ($this->getRequest()->getParam('zip') ? $this->getRequest()->getParam('zip') : $_POST['zip']);
            $countryCode = $this->getCountryCode();

            $block = $this->getLayout()->createBlock('pacsoft/servicePoints')
                ->setData('zip', $zip)
                ->setData('formCode', ($this->getRequest()->getParam('formCode') ? $this->getRequest()->getParam('formCode') : $_POST['formCode']))
                ->setData('countryCode', $countryCode)
                ->setData('selectedPickupId',0);


            /** @var $displayType Nybohansen_Pacsoft_Helper_DisplayType */
            $displayType = Mage::helper('pacsoft/DisplayType');
            $ret = array('displayType' => $displayType->getDisplayType(),
                        'html' => $block->toHtml());


            $this->getResponse()->setBody(json_encode($ret));

        }elseif($action == 'getMap'){
            $zip = ($this->getRequest()->getParam('zip') ? $this->getRequest()->getParam('zip') : $_POST['zip']);
            $countryCode = $this->getCountryCode();

            $block = $this->getLayout()->createBlock('pacsoft/servicePointsMap');

            $ret = array('displayType' => 'map',
                         'html' => $block->toHtml(),
                         'servicePoints' => $this->getServicePointsForZip($zip, $countryCode));

            $this->getResponse()->setBody(json_encode($ret));
        }elseif($action == 'getServicePointsWithinBounds'){
            $this->getResponse()->setBody(json_encode($this->getServicePointsForBounds()));
        }

	}

    public function updateServicePointAction(){
        /* @var $servicePoints Nybohansen_Pacsoft_Model_ServicePoints */
        $servicePoints = Mage::getModel('pacsoft/servicePoints');
        if($servicePoints->update(true)){
            echo $this->getLayout()->createBlock('pacsoft/servicePointLastUpdatedTime')->getHtml();
        }else{
            $this->getResponse()->setBody('false');
        }
    }

    //Functions used by map overlay
    private function getServicePointsForBounds(){
        /** @var $webservice Nybohansen_Pacsoft_Helper_ServicePointsWebservice */
        $webservice = Mage::helper('pacsoft/servicePointsWebservice');

        $coutryCode = $this->getCountryCode();
        $swLat = ($this->getRequest()->getParam('swLat') ? $this->getRequest()->getParam('swLat') : $_POST['swLat']);
        $swLng = ($this->getRequest()->getParam('swLng') ? $this->getRequest()->getParam('swLng') : $_POST['swLng']);
        $neLat = ($this->getRequest()->getParam('neLat') ? $this->getRequest()->getParam('neLat') : $_POST['neLat']);
        $neLng = ($this->getRequest()->getParam('neLng') ? $this->getRequest()->getParam('neLng') : $_POST['neLng']);

        return $webservice->getServicePointsWithinBounds($swLat, $swLng, $neLat, $neLng, $coutryCode);
    }

    private function getServicePointsForZip($zip, $countryCode){
        $webservice = Mage::helper('pacsoft/servicePointsWebservice');
        /** @var $webservice Nybohansen_Pacsoft_Helper_ServicePointsWebservice */
        return $webservice->getServicePointsInZipcode($zip, $countryCode);
    }

    private function getCountryCode(){
        return ($this->getRequest()->getParam('countryCode') ? $this->getRequest()->getParam('countryCode') : $_POST['countryCode']);
    }


}