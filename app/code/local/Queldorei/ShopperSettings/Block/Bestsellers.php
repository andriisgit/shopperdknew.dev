<?php
/**
 * @version   1.0 12.0.2012
 * @author    Queldorei http://www.queldorei.com <mail@queldorei.com>
 * @copyright Copyright (C) 2010 - 2012 Queldorei
 */

class Queldorei_ShopperSettings_Block_Bestsellers extends Mage_Catalog_Block_Product_Abstract
{
    public function __construct(){
        parent::_construct();
        $this->setData('bestsellers', Mage::getStoreConfig('shoppersettings/catalog/bestsellers'));
	
	 $this->addData(array(
            'cache_lifetime'    => 3600*24*30,
            'cache_tags'        => array('queldorei_shopper_product_list_bestsellers'),
        ));
    }

    public function _prepareLayout()
    {
        return parent::_prepareLayout();
    }
    
     /**
     * Get Key pieces for caching block content
     *
     * @return array
     */
    public function getCacheKeyInfo()
    {
        return array(
           'CATALOG_PRODUCT_BESTSELLERS',
           Mage::app()->getStore()->getId(),
           Mage::getDesign()->getPackageName(),
           Mage::getDesign()->getTheme('template'),
           Mage::getSingleton('customer/session')->getCustomerGroupId(),
	   Mage::app()->getStore()->getCurrentCurrencyCode(),
	   (int)Mage::app()->getStore()->isCurrentlySecure(),
           'template' => $this->getTemplate(),          
        );
    }

    public function getBestsellers()
    {
        $id = $this->getData('bestsellers');
        if (  empty($id) ) return null;

	    $productIds = explode(',',$this->getData('bestsellers'));
        $products = Mage::getModel("catalog/product")
		    ->getCollection()
            ->addUrlRewrite()
	        ->addStoreFilter()
		    ->addAttributeToSelect("*")
		    ->addAttributeToFilter('entity_id', array('in' => $productIds));

        return $products;
    }
}