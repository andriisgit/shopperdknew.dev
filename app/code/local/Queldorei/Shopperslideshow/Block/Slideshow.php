<?php
/**
 * @version   1.0 12.0.2012
 * @author    Queldorei http://www.queldorei.com <mail@queldorei.com>
 * @copyright Copyright (C) 2010 - 2012 Queldorei
 */

class Queldorei_Shopperslideshow_Block_Slideshow extends Mage_Core_Block_Template
{
	protected function _beforeToHtml()
	{
		$config = Mage::getStoreConfig('shopperslideshow', Mage::app()->getStore()->getId());
		if (Mage::helper('shopperslideshow/data')->isSlideshowEnabled()) {
			$this->setTemplate('queldorei/' . $config['config']['slider'] . '.phtml');
		}

		return $this;
	}

	public function _prepareLayout()
	{
		return parent::_prepareLayout();
	}

	public function getSlideshow()
	{
		if (!$this->hasData('shopperslideshow')) {
			$this->setData('shopperslideshow', Mage::registry('shopperslideshow'));
		}
		return $this->getData('shopperslideshow');

	}

	public function getSlides()
	{
		$config = Mage::getStoreConfig('shopperslideshow', Mage::app()->getStore()->getId());
		if ( $config['config']['slider'] == 'flexslider' ) {
			$model = Mage::getModel('shopperslideshow/shopperslideshow');
		} else {
			$model = Mage::getModel('shopperslideshow/shopperrevolution');
		}
		$slides = $model->getCollection()
			->addStoreFilter(Mage::app()->getStore())
			->addFieldToSelect('*')
			->addFieldToFilter('status', 1)
			->setOrder('sort_order', 'asc');
		return $slides;
	}
	
	public function isRevoulutionActive()
	{
		return Mage::helper('shopperslideshow')->isRevoulutionActive();
	}
	
	public function revolutionSliderAlias()
	{
		return Mage::getStoreConfig('shopperslideshow/revolutionslider/slider_id');
	}
	
	/**
     * Get cache key informative items
     *
     * @return array
     */
    public function getCacheKeyInfo()
    {
        return array(
            'BLOCK_BRANDS',
            Mage::app()->getStore()->getId(),
            Mage::app()->getStore()->getCurrentCurrencyCode(),
	    (int)Mage::app()->getStore()->isCurrentlySecure(),
            Mage::getDesign()->getPackageName(),
            Mage::getDesign()->getTheme('template'),
            Mage::getSingleton('customer/session')->isLoggedIn()
        );
    }
}