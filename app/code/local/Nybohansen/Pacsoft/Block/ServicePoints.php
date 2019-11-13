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

class Nybohansen_Pacsoft_Block_ServicePoints extends Mage_Core_Block_Template{


	public function __construct()  
    {
        parent::__construct();
        $this->setTemplate('pacsoft/servicePoints.phtml');
    }

    public function getDisplayType(){
        /** @var $displayType Nybohansen_Pacsoft_Helper_DisplayType */
        $displayType = Mage::helper('pacsoft/DisplayType');
        return $displayType->getDisplayType();
    }

    public function getFormCode(){
        return $this->getData('formCode');
    }

    public function getZip(){
        return intval($this->getData('zip'));
    }

    public function getPickupId(){
        return $this->getData('selectedPickupId');
    }

    public function getCountryCode(){
        return $this->getData('countryCode');
    }

    public function getServicePoints(){
        $webservice = Mage::helper('pacsoft/servicePointsWebservice');
        /** @var $webservice Nybohansen_Pacsoft_Helper_ServicePointsWebservice */
        return $webservice->getServicePointsInZipcode($this->getZip(), $this->getCountryCode());
    }

    
}


?>