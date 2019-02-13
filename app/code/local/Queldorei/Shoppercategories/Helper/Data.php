<?php
/**
 * @version   1.0 12.0.2012
 * @author    Queldorei http://www.queldorei.com <mail@queldorei.com>
 * @copyright Copyright (C) 2010 - 2012 Queldorei
 */

class Queldorei_Shoppercategories_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * check if there is custom color scheme for given category id
     *
     * @param int $current_category - category ID
     * @return null|array with color scheme options
     */
    public function getCategoryCss()
    {		
		if(Mage::registry('current_category'))
		{
			$storeId = Mage::app()->getStore()->getId();
			$catId = Mage::registry('current_category')->getId();
			$cssFile = 'css/categories/store-' . $storeId . '_category-' . $catId . '.css';			
			if(file_exists(Mage::getBaseDir() . '/skin/frontend/shopper/default/' . $cssFile))
			{
				return $cssFile;
			}
			else
			{
				$schemes = Mage::getModel('shoppercategories/shoppercategories')->getCollection()
					->addStoreFilter(Mage::app()->getStore())
					->addFieldToSelect('*')
					->addFieldToFilter('status', 1);				
				if($schemes->count())
				{					
					$_current_category = Mage::getModel('catalog/category')->load($catId);
					if ($_current_category->getId()) 
					{					
						$path = explode('/', $_current_category->getPath());
						foreach ($schemes as $scheme) 
						{							
							if(in_array($scheme['category_id'], $path)) 
							{
								$cssFile = 'css/categories/store-' . $storeId . '_category-' . $scheme['category_id'] . '.css';
								if(file_exists(Mage::getBaseDir() . '/skin/frontend/shopper/default/' . $cssFile))
								{
									return $cssFile;
								}
							}
						}
					}
				}
			}
		}
		return;
    }
	
	public function getCategoryScheme($current_category)
    {
        $scheme = Mage::getModel('shoppercategories/shoppercategories')->getCollection()
            ->addStoreFilter(Mage::app()->getStore())
            ->addFieldToSelect('*')
            ->addFieldToFilter('status', 1);

        $current_scheme = null;
        if ($scheme->count()) {

            foreach ($scheme as $_scheme) {
                if ( $_scheme['category_id'] == $current_category ) {
                    $current_scheme = $_scheme;
                }
            }

            // check if we have parent category
            if ( !$current_scheme ) {
                $_current_category = Mage::getModel('catalog/category')->load($current_category);
                if ( $_current_category->getId() ) {
                    $path = $_current_category->getPath();
                    $path = explode('/', $path);
                    foreach ($scheme as $_scheme) {
                        if ( in_array($_scheme['category_id'], $path) ) {
                            $current_scheme = $_scheme;
                        }
                    }
                }
            }
        }

        return $current_scheme;
    }
}