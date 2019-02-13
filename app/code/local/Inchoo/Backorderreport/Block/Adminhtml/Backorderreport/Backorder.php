<?php
class Inchoo_Backorderreport_Block_Adminhtml_Backorderreport_Backorder extends Mage_Core_Block_Template
{

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('inchoo/backorderreport/backorder.phtml');
    }
    
    public function getManufacturersName()
    {
        $product = Mage::getModel('catalog/product');

        $attributes = Mage::getResourceModel('eav/entity_attribute_collection')
            ->setEntityTypeFilter($product->getResource()->getTypeId())
            ->addFieldToFilter('attribute_code', 'manufacturer') 
            ->load(false);
        
        $attribute = $attributes->getFirstItem()->setEntity($product->getResource());

        /* @var $attribute Mage_Eav_Model_Entity_Attribute */
        $manufacturers = $attribute->getSource()->getAllOptions(false);
        
        return $manufacturers;
    }
    
    public function getManufacturersNameAsocArray()
    {
        $manufacturers = $this->getManufacturersName();
        
        foreach ($manufacturers as $manufacturer){
            $manufacturersAssAr[$manufacturer["value"]] = $manufacturer["label"];
        }
        
        return $manufacturersAssAr;
    }
    
    public function getBackorders($orders)
    {
        $ordersId = '';
        $manufacturerOrdersId = '';
        
        $i = 0;
        foreach ($orders as $order){
            $manufacturerOrdersId[$i] = $order->getOrderId();
            $i++;
        }
       
        $backOrderItems = Mage::getModel('sales/order_item')->getCollection()
            ->addFieldToFilter('order_id', array("in"=>$manufacturerOrdersId))
            ->addFieldToFilter('qty_backordered', array("notnull"=>''));
        $backOrderItems->getSelect()->group('order_id');
        
        $i = 0;
        foreach($backOrderItems as $backOrderItem){
            $ordersId[$i]= $backOrderItem->getOrderId();
            $i++;
        } 
        $backOrders = Mage::getModel('sales/order')->getCollection()
            ->addFieldToFilter('entity_id', array("in"=>$ordersId))
            ->addFieldToFilter('state', array("neq"=>'canceled'));

        return $backOrders;
    }
    
    public function getBackorderItems($orderId)
    {
        $backOrderItems = Mage::getModel('sales/order_item')->getCollection()
            ->addFieldToFilter('order_id', $orderId);
   
        $backOrderItems->getSelect()->join(
            array('sales_product_stock' => 'cataloginventory_stock_item'),
            'main_table.product_id = sales_product_stock.product_id',
            'qty'
            );
        
        $conditionsVarchar = array(
            'main_table.product_id = sales_product_manufacturer_varchar.entity_id',
            'sales_product_manufacturer_varchar.attribute_id = 102'
        );
        
        $backOrderItems->getSelect()->joinLeft(
            array('sales_product_manufacturer_varchar' => 'catalog_product_entity_varchar'),
            implode(' AND ', $conditionsVarchar),    
            array('value_varchar' => 'value')
            );
        
        $conditionsInt = array(
            'main_table.product_id = sales_product_manufacturer_int.entity_id',
            'sales_product_manufacturer_int.attribute_id = 102'
        );
        
        $backOrderItems->getSelect()->joinLeft(
            array('sales_product_manufacturer_int' => 'catalog_product_entity_int'),
            implode(' AND ', $conditionsInt),    
            array('value_int' => 'value')
            );

        return $backOrderItems;
    }
    
    public function getOrderIdByManufacturerValue($manufacturerValue)
    {
        $conditionsValue = array(
            'sales_product_manufacturer_int.value = ' . $manufacturerValue,
            'sales_product_manufacturer_varchar.value '
        );
        
        $backOrderItemsId = Mage::getModel('sales/order_item')->getCollection();
        
        $conditionsVarchar = array(
            'main_table.product_id = sales_product_manufacturer_varchar.entity_id',
            'sales_product_manufacturer_varchar.attribute_id = 102'
        );
        
        $backOrderItemsId->getSelect()->joinLeft(
            array('sales_product_manufacturer_varchar' => 'catalog_product_entity_varchar'),
            implode(' AND ', $conditionsVarchar),    
            array('value_varchar' => 'value')
            );
        
        $conditionsInt = array(
            'main_table.product_id = sales_product_manufacturer_int.entity_id',
            'sales_product_manufacturer_int.attribute_id = 102'
        );
        
        $backOrderItemsId->getSelect()->joinLeft(
            array('sales_product_manufacturer_int' => 'catalog_product_entity_int'),
            implode(' AND ', $conditionsInt),    
            array('value_int' => 'value')
            );
        $backOrderItemsId->addFieldToFilter(implode(' Or ', $conditionsValue) , $manufacturerValue);

        return $backOrderItemsId;
    }
    
}