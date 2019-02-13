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
 * Feed  Generator
 *
 */


/**
 * Product collection
 * hack to use leftJoin
 *
 */
class NWT_Pricecomparison_Model_Resource_Catalog_Product_Collection extends Mage_Catalog_Model_Resource_Product_Collection
{

    public function isEnabledFlat() {
        return false;
    }

    //hack productLimitionPrice to use left jon
    protected function _productLimitationPrice($joinLeft = false) {
        return parent::_productLimitationPrice(true);
    }


    protected $_stockAdded = false;

    //see Mage_CatalogInventory_Model_Resource_Stock_Item::addCatalogInventoryToProductCollection 
    //changed to add also qty field

    public function addCatalogInventory() {

        if($this->_stockAdded) {
            return $this;
        }

        $adapter = $this->getConnection();
        $isManageStock = (int)Mage::getStoreConfig(Mage_CatalogInventory_Model_Stock_Item::XML_PATH_MANAGE_STOCK);
        $stockExpr = $adapter->getCheckSql('cisi.use_config_manage_stock = 1', $isManageStock, 'cisi.manage_stock');
        $stockExpr = $adapter->getCheckSql("({$stockExpr} = 1)", 'cisi.is_in_stock', '1');
        $qtyExpr   = $adapter->getCheckSql('cisi.is_in_stock', 'cisi.qty', 0);

        if(Mage::helper('catalog')->isModuleEnabled('Mage_CatalogInventory')) {
            $this->joinTable(
                array('cisi' => 'cataloginventory/stock_item'),
                'product_id=entity_id',
                array(
                    'is_saleable' => new Zend_Db_Expr($stockExpr),
                    'inventory_in_stock' => 'is_in_stock',
                    'qty' => new Zend_Db_Expr($qtyExpr)
                ),
               '{{table}}.stock_id='.Mage_CatalogInventory_Model_Stock::DEFAULT_STOCK_ID,
                'left'
            );  
        }

        $this->_stockAdded = true;
        return $this;
    }
}
