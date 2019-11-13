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

class Nybohansen_Pacsoft_Model_Mysql4_Rates_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('pacsoft/rates');
    }

    public function addStoreFilter($store){

        $this->join('ratesStore','ratesStore.rate_id=main_table.rate_id')
             ->addFilter('store_id', $store)
             ->distinct(true);

        return $this;
    }

    public function addCountryFilter($country){

        $ids = array();
        foreach($this->getItems() as $key => $row){
            if(in_array($country,explode(',',$row->getCountry()))){
                $ids[] = $key;
            }
        }
        $this->clear();

        $this->addFieldToFilter('rate_id', array('in' => $ids));

        return $this;
    }

    public function addZipFilter($zip){
        $ids = array();
        foreach($this->getItems() as $key => $row){
            if($row->rateApplicableInZip($zip)){
                $ids[] = $key;
            }
        }
        $this->clear();

        $this->addFieldToFilter('rate_id', array('in' => $ids));

        return $this;
    }

    public function addConditionRangeFilter($val){
        $ids = array();
        foreach($this->getItems() as $key => $row){
            if($row->conditionCheck($val)){
                $ids[] = $key;
            }
        }
        $this->clear();

        $this->addFieldToFilter('rate_id', array('in' => $ids));

        return $this;
    }

    public function addAddonsFilter($addon){
        $ids = array();
        foreach($this->getItems() as $key => $row){
            if(in_array($addon,$row->getAddons())){
                $ids[] = $key;
            }
        }
        $this->clear();

        $this->addFieldToFilter('rate_id', array('in' => $ids));

        return $this;
    }


}