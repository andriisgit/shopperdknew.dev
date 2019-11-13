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

class Nybohansen_Pacsoft_Model_Rates extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('pacsoft/rates');
    }

    public function getStoresAsArray(){
        $stores = $this->getStores();

        $arr = array();
        foreach($stores as $store){
            $arr[] = $store->getStoreId();
        }

        return $arr;
    }

    public function getStores(){
        return Mage::getModel('pacsoft/ratesStore')->getCollection()->addFilter('rate_id', $this->getRateId());
    }


    public function setStores(array $stores){

        //Delete the old store-rates
        $this->deleteStores();

        //Save new stores
        foreach($stores as $store){
            $model = Mage::getModel('pacsoft/ratesStore');
            $model->setData(array('store_id' => $store, 'rate_id' => $this->getRateId()));
            $model->save();
        }
    }


    public function getAddons(){
        $addon = Mage::getModel('pacsoft/rates')->getCollection()->addFilter('rate_id', $this->getRateId());
        return explode(',', $addon->getFirstItem()->getData('addons'));
    }

    public function getAddonsAsStr(){
        $addon = Mage::getModel('pacsoft/rates')->getCollection()->addFilter('rate_id', $this->getRateId());
        return $addon->getFirstItem()->getData('addons');
    }

    public function delete(){
        $this->deleteStores();
        parent::delete();
    }

    protected function deleteStores(){
        $oldStores = $this->getStores();
        foreach($oldStores as $oldStore){
            $oldStore->delete();
        }
    }

    public function servicePointAddonActive(){
        $addons = $this->getAddons();
        return in_array('PUPOPT',$addons);
    }

    public function showDeliveryNote(){
        $addons = $this->getAddons();
        return in_array('DLVFLEX',$addons);
    }


    /**
     * Returns true if zip in range, otherwise false
     * @param $zip zip to check
     * @param $range zip range to use
     */
    public function rateApplicableInZip($zip){
        if($this->getZipRange() == '*'){
            return true;
        }

        //Split range by , ; or -
        $delimeters = '/[;,]/';
        $ranges = preg_split($delimeters, $this->getZipRange());
        foreach($ranges as $range){
            if (strpos($range, '-')){
                //It really is a range, split by -
                $range = explode('-', $range);
                if(count($range) == 2){
                    if($range[0] <= $zip && $zip <= $range[1]){
                        return true;
                    }
                }
            }else{
                if($zip == $range){
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Returns true, if value is inside condition
     * @param $val Value to be checked against condition
     * @param $condition Condition, can be a range i.e. 1-4, or a single number like 2
     */
    public function conditionCheck($val){

        $condition = $this->getConditionRange();

        if (strpos($condition, '-')){
            //It really is a range, split by -
            $condition = explode('-', $condition);
            if(count($condition) == 2){
                if($condition[0] <= $val && $val <= $condition[1]){
                    return true;
                }
            }
        }else{
            if($val == $condition){
                return true;
            }
        }
        return false;
    }

}