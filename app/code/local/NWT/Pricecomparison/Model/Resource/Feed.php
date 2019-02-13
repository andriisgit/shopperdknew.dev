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
 * Feed resource model
 *
 */
class NWT_Pricecomparison_Model_Resource_Feed extends Mage_Core_Model_Resource_Db_Abstract{
    /**
     * constructor
     * @access public
     * @return void
     */
    public function _construct(){
        $this->_init('pricecomparison/feed', 'entity_id');
    }
}