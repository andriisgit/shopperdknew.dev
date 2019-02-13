<?php

class Queldorei_ShopperSettings_Block_Product_List extends Mage_Catalog_Block_Product_List
{

	protected $_cacheKeyArray;

	protected function _construct()
	{
		$this->addData(array(
			'cache_lifetime' => 3600*24*30,
			'cache_tags'     => array('queldorei_shopper_product_list'),
		));
	}

	public function getCacheKeyInfo()
	{
		if (NULL === $this->_cacheKeyArray)
		{
			$this->_cacheKeyArray = array(
				Mage::app()->getStore()->getId(),
				Mage::getDesign()->getPackageName(),
				Mage::getDesign()->getTheme('template'),
				Mage::app()->getStore()->getCurrentCurrencyCode(),
				(int)Mage::app()->getStore()->isCurrentlySecure(),
				$this->getCategoryId(),
				$this->getNumProducts(),
				$this->getTemplate(),
			);
		}
		return $this->_cacheKeyArray;
	}

	protected function _getProductCollection()
	{
		if (is_null($this->_productCollection)) {
			if ( $this->getCategoryId() ) {
				$category = Mage::getModel('catalog/category')->load($this->getCategoryId());
				$collection = $category->getProductCollection();
			} else {
				$collection = Mage::getResourceModel('catalog/product_collection');
			}
			Mage::getModel('catalog/layer')->prepareProductCollection($collection);
			$collection->addStoreFilter();
			$collection->addAttributeToSort('position');
			$numProducts = $this->getNumProducts() ? $this->getNumProducts() : 6;
			$collection->setPage(1, $numProducts)->load();
			$this->_productCollection = $collection;
		}
		return $this->_productCollection;
	}

    public function getBlockTitle()
    {
        $title = $this->getTitle();
        if (empty($title)) {
            $title = 'Featured Products';
        }
        return $title;
    }
	
	public function getProductSwatches($_product)
	{
		$confSwatchesBlock = $this->getLayout()->createBlock('core/template');
		$confSwatchesBlock->setTemplate('configurableswatches/catalog/product/list/swatches.phtml');
		$confSwatchesBlock->setProduct($_product);
		return $confSwatchesBlock->toHtml();
	}
	
	public function getConfSwatchesJs()
	{
		$confSwatchesJs = $this->getLayout()->createBlock('core/template');
		$confSwatchesJs->setTemplate('configurableswatches/catalog/media/js_slider.phtml');		
		return $confSwatchesJs->toHtml();
	}
}