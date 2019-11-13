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


class Nybohansen_Pacsoft_Model_ServicePoints extends Varien_Object
{

    private $updatePath = 'https://api2.postnord.com/rest/businesslocation/v1/servicepoint/getServicePointInformation.json?apikey=479d13176a57491b03add4d4c55cdb01&countryCode=';
    private $zipLookupPath  = 'https://api2.postnord.com/rest/businesslocation/v1/servicepoint/findNearestByAddress.json?apikey=479d13176a57491b03add4d4c55cdb01';

    private $countries = array('dk', 'fi', 'no', 'se');

    public function update($forceUpdate = null){
        $hour = date('H');
        if(($hour == rand(0,6) && rand(0, 40) == 1) || $forceUpdate === true){
            foreach($this->countries as $country){
                $filename = 'ServicePointInformation-'.$country.'.json';
                $json = file_get_contents($this->updatePath.strtoupper($country));
                if($json && json_decode($json)){
                    file_put_contents($this->getPacsoftDir().DS.$filename, $json);
                }else{
                    return false;
                }
            }
        }
        return true;
    }

    public function getServicePoint($servicePointId, $countryCode){
        $servicePoints = $this->LoadServicePoints($countryCode);
        $res = array();
        foreach($servicePoints->servicePointInformationResponse->servicePoints as $servicePoint){
            if(isset($servicePoint->servicePointId)){
                if($servicePointId == $servicePoint->servicePointId){
                    return $servicePoint;
                }
            }
        }
        return array_values($res);
    }

    public function getServicePointsForZip($zipCode, $countryCode){
        $zipCode = trim($zipCode);

        $servicePoints = $this->LoadServicePoints($countryCode);
        $res = array();
        foreach($servicePoints->servicePointInformationResponse->servicePoints as $servicePoint){
            if(isset($servicePoint->notificationArea)){
                if(in_array($zipCode, $servicePoint->notificationArea->postalCodes)){
                    $res[$servicePoint->servicePointId] = $servicePoint;
                }
            }elseif($servicePoint->visitingAddress->postalCode == $zipCode){
                $res[$servicePoint->servicePointId] = $servicePoint;
            }
        }

        //If empty, call webservice as last resort
        if(count($res) == 0){
            $servicePointsIds = $this->getServicePointsIdsFromPostDk($zipCode, $countryCode);
            foreach($servicePoints->servicePointInformationResponse->servicePoints as $servicePoint){
                if(in_array($servicePoint->servicePointId, $servicePointsIds)){
                    $res[$servicePoint->servicePointId] = $servicePoint;
                }
            }
        }
        return array_values($res);
    }



    public function getServicePointsWithinBounds($swLat, $swLng, $neLat, $neLng, $countryCode){
        $servicePoints = $this->LoadServicePoints($countryCode);
        $res = array();
        foreach($servicePoints->servicePointInformationResponse->servicePoints as $servicePoint){
            if(count($servicePoint->coordinates) > 0){
                if($servicePoint->coordinates[0]->northing >= $swLat &&
                    $servicePoint->coordinates[0]->northing <= $neLat &&
                    $servicePoint->coordinates[0]->easting >= $swLng &&
                    $servicePoint->coordinates[0]->easting <= $neLng){
                    $res[$servicePoint->servicePointId] = $servicePoint;
                }
            }
        }
        return array_values($res);
    }


    private function getServicePointsIdsFromPostDk($zipCode, $countryCode){
        $url = $this->zipLookupPath;
        $url .= '&countryCode='.strtoupper($countryCode);
        $url .= '&postalCode='.$zipCode;
        $json_encoded = file_get_contents($url);
        $json_decoded = json_decode($json_encoded);
        $res = array();
        foreach($json_decoded->servicePointInformationResponse->servicePoints as $servicePoint){
            $res[] = $servicePoint->servicePointId;
        }
        return $res;
    }

    public function getLastUpdateTime(){
        $res = array();
        foreach($this->countries as $country){
            $filename = 'ServicePointInformation-'.$country.'.json';
            $varFilename = $this->getPacsoftDir().DS.$filename;
            if(file_exists($varFilename)){
                $res[strtoupper($country)] = filemtime($varFilename);
            }else{
                $dataDir = Mage::getModuleDir('', 'Nybohansen_Pacsoft').DS.'Data';
                $res[strtoupper($country)] = filemtime($dataDir.DS.$filename);
            }
        }
        return $res;
    }


    private function LoadServicePoints($country){
        $country = strtolower($country);

        $filename = 'ServicePointInformation-'.$country.'.json';
        $varFilename = $this->getPacsoftDir().DS.$filename;

        if(!file_exists($varFilename)){
            $this->RollBackServicePointFiles($filename, $varFilename);
        }

        if(file_exists($varFilename)){
            $json = json_decode(file_get_contents($varFilename));
            if($json){
                return $json;
            }else{
                mage::log('Corrupt ServicePointInformation file: '.$varFilename.'. Doing a rollback');
                $this->RollBackServicePointFiles($filename, $varFilename);
                $json = json_decode(file_get_contents($varFilename));
                if($json) {
                    return $json;
                }else{
                    mage::log('Serious problem: corrupt ServicePointInformation file: '.$varFilename.'. after rollback!');
                }
                return null;
            }
        }else{
            mage::log('Could not find the ServicePointInformation file: '.$varFilename);
            return null;
        }
    }

    private function RollBackServicePointFiles($filename, $varFilename){
        //Copy default data
        $dataDir = Mage::getModuleDir('', 'Nybohansen_Pacsoft').DS.'Data';
        copy($dataDir.DS.$filename, $varFilename);
    }

    //Returns pacsoft dir from var/pacsoft
    private function getPacsoftDir(){
        $varDir = Mage::getBaseDir('var');
        $pacsoftDir = $varDir.DS.'pacsoft';
        if(!is_dir($pacsoftDir)){
            mkdir($pacsoftDir);
        }

        return $pacsoftDir;
    }


}	