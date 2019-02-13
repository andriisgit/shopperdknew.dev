<?php
/**
 * NWT_Pricecomparison extension
 *
 * @category    NWT
 * @package     NWT_Pricecomparison
 * @copyright   Copyright (c) 2014 Nordic Web Team ( http://nordicwebteam.se/ )
 * @license     NWT Commercial License (NWTCL 1.0)
 * @author      Emil [carco] Sirbu (emil.sirbu@gmail.com)
 *
 */

/**
 * Feed collection resource model
 *
 */
class NWT_Pricecomparison_Model_Resource_Feed_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract {

    protected $_joinedFields = array();
    /**
     * constructor
     * @access public
     * @return void
     */

    public function _construct(){
        parent::_construct();
        $this->_init('pricecomparison/feed');
    }

    /**
     * get feeds as array
     * @access protected
     * @param string $valueField
     * @param string $labelField
     * @param array $additional
     * @return array
     */
    protected function _toOptionArray($valueField='entity_id', $labelField='name', $additional=array()){
        return parent::_toOptionArray($valueField, $labelField, $additional);
    }

    /**
     * get options hash
     * @access protected
     * @param string $valueField
     * @param string $labelField
     * @return array
     */
    protected function _toOptionHash($valueField='entity_id', $labelField='name'){
        return parent::_toOptionHash($valueField, $labelField);
    }
}