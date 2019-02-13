<?php
/**
 * @version   1.0 14.08.2012
 * @author    TonyEcommerce http://www.TonyEcommerce.com <support@TonyEcommerce.com>
 * @copyright Copyright (c) 2012 TonyEcommerce
 */

class Etheme_Buyshopflex_Block_Buyshopflex extends Mage_Core_Block_Template
{
    protected function _construct()
    {
        parent::_construct();

        $this->addData(array(
            'cache_lifetime'    => 86400,
            'cache_tags'        => array(Mage_Catalog_Model_Product::CACHE_TAG),
        ));
    }

    public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }

	public function getDataSlides()
    {
        $data_slides  = Mage::getModel('buyshopflex/buyshopflex')->getCollection()
        	->addFieldToSelect('*')
        	->addFieldToFilter('status', 1);

        $data=$data_slides->toArray();
        $filtered_data=array();
        if(count($data['items']))
        foreach($data['items'] as $slide)
        {
            $stores=explode(',',$slide['store_id']);
            if(in_array(Mage::app()->getStore()->getStoreId(),$stores) || in_array(0,$stores))$filtered_data[]=$slide;
        }

        return $filtered_data;
    }

    public function getDataSlider()
    {
        if (!$this->hasData('buyshopflex'))$this->setData('buyshopflex', Mage::registry('buyshopflex'));
        else return $this->getData('buyshopflex');
    }

}