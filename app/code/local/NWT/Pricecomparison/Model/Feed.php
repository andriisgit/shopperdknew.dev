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
 * Feed model
 *
 */
class NWT_Pricecomparison_Model_Feed extends Mage_Core_Model_Abstract {
    /**
     * Entity code.
     * Can be used as part of method name for entity processing
     */
    const ENTITY= 'pricecomparison_feed';
    const CACHE_TAG = 'pricecomparison_feed';

    /**
     * Prefix of model events names
     * @var string
     */
    protected $_eventPrefix = 'pricecomparison_feed';
    
    /**
     * Parameter name in event
     * @var string
     */
    protected $_eventObject = 'feed';

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
     * before save feed
     * @access protected
     * @return NWT_Pricecomparison_Model_Feed
     */
    protected function _beforeSave(){
        parent::_beforeSave();
        $now = Mage::getSingleton('core/date')->gmtDate();
        if ($this->isObjectNew()){
            $this->setCreatedAt($now);
        }
        $this->setUpdatedAt($now);
        $this->serializeData();
        return $this;
    }


   /**
     * after load feed
     * @access protected
     * @return NWT_Pricecomparison_Model_Feed
     */
    protected function _afterLoad(){
        parent::_afterLoad();
        $this->unserializeData();
        return $this;
    }

 
    public function setDefaultData($type='',$store=0) {

        $this->setData(array(
            'status'=>1,
            'list_outofstock'=>0,
            'list_disabled'=>0,
            'list_visibility'=>array(Mage_Catalog_Model_Product_Visibility::VISIBILITY_IN_CATALOG,Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH),
            'list_types'=>array(Mage_Catalog_Model_Product_Type::TYPE_SIMPLE,Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE),
            'csv_header'=>1,
            'csv_enclosure'=>chr('"'),
            'csv_separator'=>chr('|'),
            'attributes'=>array()
        ));

        $name = array();
        if($type) {

            $name[] = ucwords(str_replace('_',' ',$type));
            $this->setData('feed_type',$type);

            $feed = Mage::getSingleton('pricecomparison/source_feed')->getFeed($type);
            if(!empty($feed['attributes'])) {
                $this->setData('attributes',$feed['attributes']);
            }
        }

        if($store) {
            $stores =  Mage::getSingleton('adminhtml/system_store')->getStoreOptionHash();
            if(isset($stores[$store])) {
                $this->setData('store_id',$store);
                $name[] = $stores[$store];
            }
        }

        if($name) {
            $this->setData('name',$sname = implode(' - ',$name));
            $this->setData('filename',preg_replace('/[^a-z0-9_-]/','', strtolower($sname)).'.csv');
        }

    }

    protected function serializeData() {
        $attributes = $this->getData('attributes');
        $return = array();
        if(is_array($attributes)) {
            foreach($attributes as $k=>$val) {
                if($val && is_array($val) && !empty($val['attribute'])) {
                    $return[$k] = $val;
                }
            }
        }
        if($return) {
            $this->setData('attributes',json_encode($return));
        } else {
            $this->setData('attributes','');
        }
        
        $types = $this->getData('list_types');
        $return = array();
        if(is_array($types)) {
            foreach($types as $type) {
                if($type) $return[] = $type;
            }
        }
        $this->setData('list_types',implode(',',$return));


        $visibility = $this->getData('list_visibility');
        $return = array();
        if(is_array($visibility)) {
            foreach($visibility as $v) {
                if($v) $return[] = $v;
            }
        }
        $this->setData('list_visibility',implode(',',$return));
        return $this;
    }

    protected function unserializeData() {
        $attributes = $this->getData('attributes');
        if(!is_array($attributes)) {
            $attributes = $attributes?json_decode($attributes,true):array();
            $this->setData('attributes',is_array($attributes)?$attributes:array());
        }
        $types = $this->getData('list_types');
        if(!is_array($types)) {
            $this->setData('list_types',explode(',',$types));
        }
        $visibility = $this->getData('list_visibility');
        if(!is_array($visibility)) {
            $this->setData('list_visibility',explode(',',$visibility));
        }
    }


}