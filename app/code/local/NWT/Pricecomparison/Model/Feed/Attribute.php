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
 * Feed Custom Attributes Model
 *
 */

class NWT_Pricecomparison_Model_Feed_Attribute {

    protected $_categories;
 
    protected function _getCategory($id,$sep = '/') {

        if(is_null($this->_categories)) {

            $this->_categories = array();

            $rootCategoryID = Mage::app()->getStore()->getRootCategoryId();
            if(!$rootCategoryID) {
                return '';
            }


            $rootCategory = Mage::getModel('catalog/category')->load($rootCategoryID);
            $rootCategory->setFullName('');
            $this->_categories[$rootCategory->getId()] = $rootCategory;
            
            $this->_getChildren($rootCategory,$sep);
        }

        if(!isset($this->_categories[$id])) {
            return '';
        }
        return $this->_categories[$id];

    }


   protected function _getChildren($category,$sep = '/'){

        $children = $category->getChildrenCategories();

        if($children) {

            foreach ($children as $child) {
                if ($child->getIsActive()) {
                    $child->setFullName(ltrim($category->getFullName()." {$sep} ".$child->getName(),"{$sep} "));
                    $this->_categories[$child->getId()] = $child;
                    $this->_getChildren($child,$sep);
                }
            }
        }

    }



    /* Custom fields */


    public function get_price_by_type($product = null, $attribute = null) {

        if(!$product) {
            return '0.00';
        }
        $price = null;
        if($product->getTypeId() == 'grouped' || $product->getTypeId() == 'bundle') {
            $price = $product->getMinimalPrice();
        }
        if(!$price) {
            $price = $product->getFinalPrice();
        }
        return $price;
    }


    public function get_category($product = null, $attribute = null) {
        if(!$product || !($ids = $product->getCategoryIds())) {
            return '';
        }
        $cat = null;

        $paths = array();
        
        $params = (array)$attribute->getParams();
        $sep1 = empty($params[0])?'/':trim($params[0]);
        $sep2 = empty($params[1])?'':trim($params[1]);
        
   
        foreach($ids as $id) {

            $_cat = $this->_getCategory($id,$sep1);
	    if($_cat) {
              $paths[] = $_cat->getPath();
              if(!$cat || $_cat->getLevel()>$cat->getLevel()) {
                $cat = $_cat;
              }
            }            
        }
        
        
        if(count($paths)<=1 || empty($sep2)) {
            if($cat) {
                $names[] = $cat->getFullName();
            }
        } else {
            $sep2 = " {$sep2} ";
            
            //sort paths
            $goodPaths = array();
            sort($paths);       
            $last = $paths[0];
            $cnt = 0;
            foreach($paths as $p) {
                $_p = substr($p.'/',0,strlen($last));
                if($_p == $last) {
                    $goodPaths[$cnt] = $p;
                } else {
                    $cnt++;
                }
                $last = $p.'/';
             }
             foreach($goodPaths as $p) {
                $p = explode('/',$p);
                $id = array_pop($p);
                if($id) {
                    $_cat = $this->_getCategory($id,$sep1);
                    if($_cat) $names[] = $_cat->getFullName();
                }
             }
        }

        return implode($sep2, $names);
        
    }


    public function get_category_id($product = null, $attribute = null) {
        if(!$product || !($ids = $product->getCategoryIds())) {
            return '';
        }
        foreach($ids as $id) {
            $_cat = $this->_getCategory($id);
            if($_cat && (!$cat || $_cat->getLevel()>$cat->getLevel())) {
                $cat = $_cat;
            }
        }
        if($cat) {
            return $cat->getId();
        } else {
            return '';
        } 
    }

    public function get_url($product = null, $attribute = null) {
        if(!$product) {
            return '';
        }
        return $product->getProductUrl();
    }

    public function get_image($product = null, $attribute = null) {
        if(!$product) {
            return '';
        }
        $val = $product->getData('image');
        if($val) {
            return Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).'catalog/product/'.ltrim($val,'/');
        } else {
            return '';
        } 
    }

    public function get_empty($product = null, $attribute = null) {
    	if($attribute && $options = $attribute->getOptions()){
    		return $options['default'] ? $options['default'] : '';
        }

        return '';
    }

    //@see NWT_Pricecomparison_Model_Resource_Catalog_Product_Collection::addCatalogInventory
    public function get_is_in_stock($product = null, $attribute = null) {
        return $product->getData('inventory_in_stock');
    }

    //use this to use produc::getIsSalable which have custom method for configurable/bundle
    public function get_is_saleable($product = null, $attribute = null) {
        return $product->getIsSalable();
    }

}
