<?php
/**
 * Nybohansen ApS
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This package designed for Magento COMMUNITY edition
 * We do not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * In case of incorrect edition usage, we don't provide support.
 * =================================================================
 *
 * @category   Nybohansen
 * @package    Nybohansen_Economic2
 * @copyright  Copyright (c) 2014 Nybohansen ApS
 * @license    LICENSE.txt
 */

class Nybohansen_Economic2_Model_EconomicOrder {

	/**
	 * @var Mage_Sales_Model_Order
	 */
	private $order;

    /**
     * @var Mage_Sales_Model_Order_Creditmemo
     */
    private $creditMemo;

    /**
	 * @var Mage_Sales_Model_Order_Address
	 */
	private $order_billing_address;

	/**
	 * @var Mage_Sales_Model_Order_Address
	 */
	private $order_shipping_address;

	/**
	 * @var Nybohansen_Economic2_Model_Eapi
	 */
	private $eapi;

	private $connected;

    //Defines if the current order is a credit memo
    private $isCreditMemo;

    //Contains the store id of the store we are currently working on
    private $storeId;

	/**
	 * 
	 * @var Nybohansen_Economic2_Model_Configuration
	 */
	private $configuration;

	/**
	 * Flag to avoid infinite loop when listening on event save_after when changing order
	 */
	public $save_flag = false;

	public function __construct(){
		$this->configuration = Mage::getModel('economic2/configuration');
	}


	public function create_order(Mage_Sales_Model_Order $order, $credit_memo_id = 0){

        if($credit_memo_id){
            $credit = Mage::getResourceModel('sales/order_creditmemo_collection')->addAttributeToFilter('increment_id', $credit_memo_id)->getAllIds();
            $this->creditMemo = Mage::getModel('sales/order_creditmemo')->load($credit[0]);
            $this->isCreditMemo = true;
        }

        $this->order = $order;
        $this->storeId = $order->getStoreId();
        $this->connect_to_webservice();



        if ($this->save_flag) {
			$this->save_flag = false;
			return false;
		}elseif($this->eapi->is_connected()){

			$this->order_billing_address = $order->getBillingAddress();
			$this->order_shipping_address = $order->getShippingAddress();
            $relation = Mage::getModel('economic2/orderStatus')->load($order->getIncrementId(), 'magento_order_id');

            if($this->isCreditMemo){
                //Only debtors from the original order can credit memos - Even if "Always create debtor" is set to true
                if($relation->economic_debtor_id){
                    $debtor_handle = $this->eapi->debtor_get_by_number($relation->economic_debtor_id);
                }else{
                    $this->add_log_entry(Zend_Log::WARN, 'Cannot create credit memo for unknown customer');
                    return false;
                }
            }else{
                //Create debtor or fetch existing
                $debtor_handle = $this->create_debtor();
            }

            //Create new order
            $order_handle = $this->eapi->order_create($debtor_handle);

            //Set the correct order date
            $this->set_order_date($order_handle);

            //Get e-conomic order number
            $economic_order_number = $this->eapi->order_get_number($order_handle);

            //Set currency of order
            $this->set_currency_code($order_handle);

            //Set term of payment of order
            $this->set_term_of_payment($order_handle);

            //Add billing info to order
            $this->order_set_billing_address($order_handle, $debtor_handle);

            //Add shipping info to order
            $this->order_set_shipping_address($order_handle);

            //Add the order lines
            $this->add_order_lines($order_handle);

            //Add shipping cost
            $this->add_shipping_cost_line($order_handle);

            //Add order discount line
            $this->add_discount_line($order_handle);

            //Add payment fee line
            $this->add_payment_fee_line($order_handle);

            //Add reward points line
            $this->add_rewardpoints_line($order_handle);

            //Add credit memo adjustment line - only applicable if this is a credit note
            if($this->isCreditMemo){
                $this->add_credit_memo_adjustment($order_handle);
            }

            //Add the Magento order id to the e-conomic order in the chosen e-conomic field
            $this->add_economic_comment($order_handle);

            //Add EAN info, if EAN payment has been used
            $this->add_ean_info($order_handle, $debtor_handle);


            //Add comment
            if($this->isCreditMemo){
                $this->add_order_comment(Mage::helper('economic2')->__('e-conomic: credit memo created with order id ').$economic_order_number);
            }else{
                $this->add_order_comment(Mage::helper('economic2')->__('e-conomic: Order created with order id ').$economic_order_number);
            }

            $integrityOk = $this->check_order_integrity($order_handle);

            //Store the e-conomic debtor id for later use.
            $economic_debtor_number = $this->eapi->debtor_get_number($debtor_handle);

            //Add entry in the e-conomic order status table
            $relation->setMagento_order_id($this->order->getIncrementId());

            if($this->isCreditMemo){
                $relation->setCredit_order_id($economic_order_number);
            }else{
                $relation->setEconomic_order_id($economic_order_number);
            }

            $relation->setEconomic_debtor_id($economic_debtor_number);
            $relation->setIntegrity_check($integrityOk);
            $relation->save();
            return true;
		}else{
            $this->add_order_comment(Mage::helper('economic2')->__('e-conomic: Cannot connect to e-conomic'));
            return false;
        }
	}


    public function create_invoice(Mage_Sales_Model_Order $order, $credit_memo_id = 0, $massActionCall = false ){

        if($credit_memo_id){
            $credit = Mage::getResourceModel('sales/order_creditmemo_collection')->addAttributeToFilter('increment_id', $credit_memo_id)->getAllIds();
            $this->creditMemo = Mage::getModel('sales/order_creditmemo')->load($credit[0]);
            $this->isCreditMemo = true;
        }

        $this->order = $order;
        $this->storeId = $order->getStoreId();
        $this->connect_to_webservice();

        if ($this->save_flag) {
            $this->save_flag = false;
        }elseif($this->eapi->is_connected()){

            //Check if order exists and if it does then upgrade
            //Otherwise call $this->create_order and uprade afterwards
            $relation = Mage::getModel('economic2/orderStatus')->load($order->getIncrementId(), 'magento_order_id');

            if($this->isCreditMemo){
                $orderHandle = $this->eapi->order_get_by_number($relation->getCredit_order_id());
            }else{
                $orderHandle = $this->eapi->order_get_by_number($relation->getEconomic_order_id());
            }

            if(!$relation->getEconomic_order_id() || !$orderHandle){
                $this->create_order($order, $credit_memo_id);
                $relation = Mage::getModel('economic2/orderStatus')->load($order->getIncrementId(), 'magento_order_id');
            }

            //Check if integrity check has been performed and the integrity is correct - or if we want to force booking...
            if($relation->getIntegrity_check() || mage::getStoreConfig('economic2_options/orderConfig/force_book', $this->storeId)){
                //Upgrade order to current invoice
                if($this->isCreditMemo){
                    $currentinvoice_handle = $this->eapi->order_upgrade_to_invoice($this->eapi->order_get_by_number($relation->getCredit_order_id()));
                }else{
                    $currentinvoice_handle = $this->eapi->order_upgrade_to_invoice($this->eapi->order_get_by_number($relation->getEconomic_order_id()));
                }

                if($currentinvoice_handle){

                    if($this->isCreditMemo){
                        $this->add_order_comment(Mage::helper('economic2')->__('e-conomic: Credit memo upgraded to current invoice and is ready to be booked'));
                    }else{
                        $this->add_order_comment(Mage::helper('economic2')->__('e-conomic: Order upgraded to current invoice and is ready to be booked'));
                    }

                    $relation->setEconomic_current_invoice_id($currentinvoice_handle->Id);

                    if(mage::getStoreConfig('economic2_options/orderConfig/book_invoice', $this->storeId) || $massActionCall){

                        if(mage::getStoreConfig('economic2_options/orderConfig/use_magento_invoice_number', $this->storeId) && $this->order->hasInvoices()){
                            //Book the invoice with the invoice number generated in Magento
                            if($this->isCreditMemo){
                                $invoice_handle = $this->eapi->invoice_book_with_number($currentinvoice_handle, $order->getCreditmemosCollection()->getFirstItem()->getIncrementId());
                            }else{
                                $invoice_handle = $this->eapi->invoice_book_with_number($currentinvoice_handle, $order->getInvoiceCollection()->getFirstItem()->getIncrementId());
                            }
                        }else{
                            //Book the invoice with the next invoice number
                            $invoice_handle = $this->eapi->invoice_book($currentinvoice_handle);
                        }

                        $invoice_id = $this->eapi->invoice_get_number($invoice_handle);

                        //Store invoice id in table
                        if($this->isCreditMemo){
                            $relation->setCredit_invoice_id($invoice_id);
                        }else{
                            $relation->setEconomic_invoice_id($invoice_id);
                        }

                        //Add comment
                        if($this->isCreditMemo){
                            $this->add_order_comment(Mage::helper('economic2')->__('e-conomic: Credit memo is booked with invoice id ').$invoice_id);
                        }else{
                            $this->add_order_comment(Mage::helper('economic2')->__('e-conomic: Current invoice is booked with invoice id ').$invoice_id);
                        }
                    }
                    $relation->save();
                    return true;
                }
            }else{
                $this->add_order_comment(Mage::helper('economic2')->__('e-conomic: Integrity check failed. Cannot create invoice.'));
            }
        }else{
            $this->add_order_comment(Mage::helper('economic2')->__('e-conomic: Cannot connect to e-conomic'));
        }
    }


    public function cancel_order(Mage_Sales_Model_Order $order){
        $this->order = $order;
        $this->storeId = $order->getStoreId();
        $this->connect_to_webservice();

        if ($this->save_flag) {
            $this->save_flag = false;
        }elseif($this->eapi->is_connected()){

            $relation = Mage::getModel('economic2/orderStatus')->load($order->getIncrementId(), 'magento_order_id');

            if($relation->getEconomic_order_id()){
                $order_handle = $this->eapi->order_get_by_number($relation->getEconomic_order_id());
                if($order_handle){
                    $this->eapi->order_delete($order_handle);
                    $this->add_order_comment(Mage::helper('economic2')->__('e-conomic: Order deleted from e-conomic.'));
                }else{
                    $this->add_log_entry(Zend_Log::WARN, 'Cannot delete order from e-conomic. Order '.$this->order->getIncrementId().' does not exists in e-conomic.');
                    $this->add_order_comment(Mage::helper('economic2')->__('e-conomic: Cannot delete order from e-conomic. Order does not exists.'));
                }
            }else{
                $this->add_log_entry(Zend_Log::WARN, 'Cannot delete order from e-conomic. Order '.$this->order->getIncrementId().' does not have an economic id in the economic_order_status table.');
                $this->add_order_comment(Mage::helper('economic2')->__('e-conomic: Cannot delete order from e-conomic. Cannot find the e-conomic order id.'));
            }
        }else{
            $this->add_order_comment(Mage::helper('economic2')->__('e-conomic: Cannot connect to e-conomic'));
        }
    }


    public function change_address(Mage_Sales_Model_Order $order, $address){
        $this->order = $order;
        $this->storeId = $order->getStoreId();
        $this->connect_to_webservice();

        //Create debtor or fetch existing
        $debtor_handle = $this->create_debtor();

        $relation = Mage::getModel('economic2/orderStatus')->load($order->getIncrementId(), 'magento_order_id');

        if ($relation->getEconomic_order_id()) {
            $order_handle = $this->eapi->order_get_by_number($relation->getEconomic_order_id());
        }else{
            $order_handle = false;
        }

        if ($order_handle) {
            if ($address->address_type == 'shipping') {
                $this->order_shipping_address = $address;
                $this->order_set_shipping_address($order_handle);
            }elseif ($address->address_type == 'billing'){
                $this->order_billing_address = $address;
                $this->order_set_billing_address($order_handle, $debtor_handle);
            }
        }

    }

    public function update_products_from_economic($magento_product_ids, $storeId = Mage_Core_Model_App::ADMIN_STORE_ID){

        if ($this->save_flag) {
            $this->save_flag = false;
            return false;
        }

        $this->save_flag = true;
        $this->storeId = (int)$storeId;

        $this->connect_to_webservice();

        $economic_product_ids = array();
        for($n=0; $n < count($magento_product_ids); $n++){
            $economic_product_ids[] = $this->get_product_id(Mage::getModel('catalog/product')->load($magento_product_ids[$n]));
        }


        $economic_product_handles = $this->eapi->product_find_by_number_list($economic_product_ids);
        $economic_products = $this->eapi->product_get_data_array_result($economic_product_handles);

        //Make sure it is an array of products
        if(count($economic_products) == 1){
            $economic_products = array($economic_products);
        }

        $serialized_mapping = mage::getStoreConfig('economic2_options/productConfig/field_mapping_from_e_to_m', $this->storeId);
        $tmp = @unserialize($serialized_mapping);

        $mapping = array();
        foreach ($tmp as $map) {
            $mapping[$map['economic_field']][] = $map['magento_field'];
        }

        $result = "";

        foreach ($magento_product_ids as $productId) {
            /** @var $product Mage_Catalog_Model_Product*/
            $product = Mage::getModel('catalog/product')->setStoreId($this->storeId)->load($productId);

            foreach ($economic_products as $economic_product_data){

                //Convert object to array
                $economic_product_data = get_object_vars($economic_product_data);

                if($economic_product_data['Number'] == $this->get_product_id($product)){
                    //Update product
                    $economic_fields = array('Name', 'BarCode', 'CostPrice', 'Description', 'InStock', 'IsAccessible', 'SalesPrice', 'RecommendedPrice', 'Available');
                    foreach ($economic_fields as $economic_field) {

                        if(isset($mapping[$economic_field])){
                            //Update fields
                            foreach ($mapping[$economic_field] as $magento_field_to_update) {
                                if($magento_field_to_update == 'qty' && $product->getTypeId() != 'bundle'){

                                    $location_id = mage::getStoreConfig('economic2_options/productConfig/inventory_location', $this->storeId);
                                    if($location_id>0){
                                        $inventory_data = $this->eapi->Product_GetInventoryLocationStatus($economic_product_data['Handle'], $location_id);
                                        $inventory_data = get_object_vars($inventory_data);
                                    }else{
                                        $inventory_data = $economic_product_data;
                                    }

                                    /** @var $stockItem Mage_CatalogInventory_Model_Stock_Item */
                                    $stockItem = Mage::getModel('cataloginventory/stock_item')->loadByProduct($product->getId());
                                    $stockItem->setForceReindexRequired(true);

                                    $stockItem->setData('qty', $inventory_data[$economic_field]);

                                    //Calculate if item is in stock
                                    if($stockItem->getData('min_qty')>=$stockItem->getData('qty')){
                                        $stockItem->setData('is_in_stock', 0);
                                    }else{
                                        $stockItem->setData('is_in_stock', 1);
                                    }

                                    $stockItem->save();
                                    Mage::getSingleton('index/indexer')->processEntityAction($stockItem, Mage_CatalogInventory_Model_Stock_Item::ENTITY, Mage_Index_Model_Event::TYPE_SAVE);
                                    Mage::getSingleton('index/indexer')->processEntityAction($product, Mage_Catalog_Model_Product::ENTITY, Mage_Index_Model_Event::TYPE_SAVE);
                                }else{
                                    if(!is_null($economic_product_data[$economic_field])){
                                        $product->setData($magento_field_to_update,$economic_product_data[$economic_field]);
                                    }
                                }
                            }
                        }
                    }
                    $product->save();
                    $result .= $product->getName().', ';
                }
            }
        }

        return $result;
    }

    /**
     * Creates new product if it doesn't exists and returns product_handle.
     * If product does exists then it returns handle
     *
     * @param Mage_Sales_Model_Order_Item $item
     */
    public function create_product($item, $updateProduct=false, $storeId = 0){

        $this->save_flag = true;

        $this->storeId = $storeId;
        $this->connect_to_webservice();

        //Magento product id
        $magento_product_id = $item->getProductId();
        if(!$magento_product_id){
            //If $item is not of type Mage_Sales_Model_Order_Item get product id from an alternative method
            $magento_product_id = $item->getEntityId();
        }

        //Load product model
        /** @var $item Mage_Catalog_Model_Product*/
        $item = Mage::getModel('catalog/product')->setStoreId($storeId)->load($magento_product_id);


        if($item->getTypeId() == "configurable"){
            return false;
        }

        //Product id used as key between e-conomic and magento
        $economic_product_id = $this->get_product_id($item);

        //See if product already exists in e-conomics
        $product_handle = $this->eapi->product_get_by_number($economic_product_id);

        if(!$product_handle){
            //Create product in e-conomic
            $product_handle = $this->eapi->product_create_new($economic_product_id,
                $this->get_product_group($item),
                $item->getName());
            //Set product unit
            $this->eapi->product_set_unit($product_handle, $this->get_product_unit($item));
            //Set the product as accessible in e-conomic
            $this->eapi->product_set_accessible($product_handle, true);
            //Update stock value
            $this->eapi->product_set_accessible($product_handle, Mage::getModel('cataloginventory/stock_item')->loadByProduct($item)->getQty());
            //Update product group
            $this->eapi->product_set_product_group($product_handle, $this->get_product_group($item));

            $updateProduct = true;
        }

        if ($updateProduct){

            $serialized_mapping = mage::getStoreConfig('economic2_options/productConfig/field_mapping_from_m_to_e', $this->storeId);
            $tmp = @unserialize($serialized_mapping);
            $mapping = array();
            foreach ($tmp as $map) {
                $mapping[$map['magento_field']][] = $map['economic_field'];
            }

            foreach ($mapping as $magentoattribute => $economicFieldArray) {
                if($magentoattribute == 'qty'){
                    $stockItem = Mage::getModel('cataloginventory/stock_item')->loadByProduct($item->getId());
                    $value = $stockItem->getData($magentoattribute);
                }else{
                    $value = $item->getData($magentoattribute);
                }

                //Update the chosen fields in e-conomic
                foreach ($economicFieldArray as $economicField) {
                    switch ($economicField) {
                        case 'Name':
                            $this->eapi->product_set_name($product_handle, $value);
                            break;
                        case 'BarCode':
                            $this->eapi->product_set_barcode($product_handle, $value);
                            break;
                        case 'CostPrice':
                            $this->eapi->product_set_cost_price($product_handle, $value);
                            break;
                        case 'Description':
                            $this->eapi->product_set_description($product_handle, substr($value,0,500));
                            break;
                        case 'SalesPrice':
                            $this->eapi->product_set_sales_price($product_handle, $value);
                            break;
                        case 'RecommendedPrice':
                            $this->eapi->product_set_recommended_price($product_handle, $value);
                            break;

                        default:
                            break;
                    }
                }

            }

        }

        return $product_handle;
    }


    public function delete_product(Mage_Catalog_Model_Product $item){
        $this->connect_to_webservice();
        $productId = $this->get_product_id($item);
        if($productId){
            $productHandle = $this->eapi->product_get_by_number($productId);
            if($productHandle){
                $this->eapi->product_delete($productHandle);
            }
        }
    }

/*********************************************************************************************/
/**************************************PRIVATE FUNCTIONS**************************************/
/*********************************************************************************************/

    /**
     * Checks that the order total is the same as the total in Magento
     * @param $order_handle Order handle of e-conomic order
     */
    private function check_order_integrity($order_handle){
        if($this->isCreditMemo){
            $sign = -1;
            $order = $this->creditMemo;
        }else{
            $sign = 1;
            $order = $this->order;
        }

        $economic_order_data = $this->eapi->order_get_data($order_handle);

        $errorArray['has_error'] = false;
        $errorArray['magento_order_id'] = $order->getIncrementId();

        if($economic_order_data){

            $errorArray['economic_order_id'] = $economic_order_data->Number;
            $errorArray['economic_net_amount'] = (float)($economic_order_data->NetAmount*100);
            $errorArray['economic_gross_amount'] = (float)($economic_order_data->GrossAmount*100);

            if(mage::getStoreConfig('economic2_options/orderConfig/use_base_currency', $this->storeId)){
                $errorArray['magento_net_amount'] = (($order->getBaseGrandTotal()-$order->getBaseTaxAmount()) * $sign * 100);
                $errorArray['magento_gross_amount'] =($order->getBaseGrandTotal() * $sign * 100);
            }else{

                $errorArray['magento_net_amount'] = (($order->getGrandTotal()-$order->getTaxAmount()) * $sign*100);
                $errorArray['magento_gross_amount'] = ($order->getGrandTotal() * $sign*100);
            }

            if(abs($errorArray['magento_net_amount'] - $errorArray['economic_net_amount']) > 0.009 ){
                //Discrepancy between the net total in e-conomic and the net total in Magento
                $this->add_log_entry(Zend_Log::ALERT,"Order (Magento id ".$errorArray['magento_order_id'].") integrity check failed: Discrepancy between the net total in e-conomic and the net total in Magento");
                $errorArray['has_error'] = true;
            }

            if(abs($errorArray['magento_gross_amount'] - $errorArray['economic_gross_amount']) > 0.009){
                //Discrepancy between the gross total in e-conomic and the gross total in Magento
                $this->add_log_entry(Zend_Log::ALERT,"Order (Magento id ".$errorArray['magento_order_id'].") integrity check failed: Discrepancy between the gross total in e-conomic and the gross total in Magento");
                $errorArray['has_error'] = true;
            }


        }else{
            $errorArray['economic_order_id'] = '-';
            $errorArray['economic_net_amount'] = '-';
            $errorArray['magento_net_amount'] = $order->getGrandTotal()-$order->getTaxAmount();
            $errorArray['economic_gross_amount'] = '-';
            $errorArray['magento_gross_amount'] = $order->getGrandTotal();
            $errorArray['has_error'] = true;
        }


        if($errorArray['has_error']){
            $this->integrity_notify($errorArray);
            return !$errorArray['has_error'];
        }

        return !$errorArray['has_error'];

    }

    private function integrity_notify($errorArray){


        if($this->isCreditMemo){
            $content = Mage::helper('economic2')->__('The integrity check performed for the following credit memo failed. The credit memo may need to be processed manual.')."\n\n";
        }else{
            $content = Mage::helper('economic2')->__('The integrity check performed for the following order failed. The order may need to be processed manual.')."\n\n";
        }

        if($this->isCreditMemo){
            $content .= Mage::helper('economic2')->__('Magento credit memo number').": ".$errorArray['magento_order_id']."\n";
        }else{
            $content .= Mage::helper('economic2')->__('Magento order number').": ".$errorArray['magento_order_id']."\n";
        }

        $content .= Mage::helper('economic2')->__('e-conomic order number').": ".$errorArray['economic_order_id']."\n\n";

        $content .= Mage::helper('economic2')->__('e-conomic net amount').": ".$errorArray['economic_net_amount']/100 ."\n";
        $content .= Mage::helper('economic2')->__('Magento net amount').": ".$errorArray['magento_net_amount']/100 ."\n\n";

        $content .= Mage::helper('economic2')->__('e-conomic gross amount').": ".$errorArray['economic_gross_amount']/100 ."\n";
        $content .= Mage::helper('economic2')->__('Magento gross amount').": ".$errorArray['magento_gross_amount']/100 ."\n\n\n";

        $content .= Mage::helper('economic2')->__('This message was automatically sent by the e-conomic module.');

        //Add comment to Magento order
        $this->add_order_comment($content);

        if(mage::getStoreConfig('economic2_options/order_integrity_config/integrity_check_notify', $this->storeId)){
            try {
                $mail = Mage::getModel('core/email');
                $mail->setToName('');
                $mail->setToEmail(mage::getStoreConfig('economic2_options/order_integrity_config/integrity_report_email', $this->storeId));
                $mail->setSubject(Mage::helper('economic2')->__('e-conomic module order integrity check failed. Magento order number: ').$errorArray['magento_order_id']);
                $mail->setFromEmail(Mage::getStoreConfig('trans_email/ident_general/email', $this->storeId));
                $mail->setFromName(Mage::getStoreConfig('trans_email/ident_general/name', $this->storeId));
                $mail->setType('text');// You can use Html or text as Mail format
                $mail->setBody($content);
                $mail->send();
            }
            catch (Exception $e) {
                $this->add_log_entry(Zend_Log::ALERT,"Order integrity check: Unable to send integrity mail");
            }
        }

    }

    /**
     *
     * Adds the EAN payment info from the EAN payment module from Nybohansen ApS
     * @param $order_handle
     * @param $debtor_handle
     */
    private function add_ean_info($order_handle, $debtor_handle){
        if($this->order->getPayment()->getMethod() == 'ean'){
            $this->eapi->order_set_debtor_ean($order_handle, $this->order->getPayment()->getData('ean_number'));
            $this->eapi->order_set_other_reference($order_handle, $this->order->getPayment()->getData('ean_requisition'));
            //Check if contact exist by searching for the name in the contact list for the debtor
            $debtor_contact_id = $this->eapi->debtor_contact_find_by_name($debtor_handle, $this->order->getPayment()->getData('ean_reference'));
            if(!$debtor_contact_id){
                //Create new one, since the contact does not exists...
                $debtor_contact_id = $this->eapi->debtor_contact_create($debtor_handle, $this->order->getPayment()->getData('ean_reference'));
            }
            $this->eapi->order_set_your_reference($order_handle, $debtor_contact_id);
        }
    }
	
	/**
	 * 
	 * Sets the term of payment of an order
	 * @param unknown_type $order_handle
	 */
    private function set_term_of_payment($order_handle){

        $paymentMethod = $this->order->getPayment()->getMethod();
        $read = Mage::getSingleton('core/resource')->getConnection('core_read');

        //Find card type
        switch ($paymentMethod) {
            case "epay_standard":
                //Epay
                $row = $read->fetchRow("select * from epay_order_status where orderid = '" . $this->order->getIncrementId() . "'");
                $cardType = $row['cardid'];
                break;
            case "quickpaypayment_payment":
                //Quickpay
                $resource = Mage::getSingleton('core/resource');
                $table = $resource->getTableName('quickpaypayment_order_status');
                $row = $this->paymentData = $read->fetchRow("select * from " . $table . " where ordernum = " . $this->order->getIncrementId());
                $cardType = $row['cardtype'];
                break;
        }

        $serialized_mapping = mage::getStoreConfig('economic2_options/terms_of_payment_config/terms_of_payment', $this->storeId);
        $tmp = @unserialize($serialized_mapping);

        $termOfPayment = null;
        foreach ($tmp as $mapping) {
            $map = explode(';', $mapping['payment_type_id']);
            if($map[0] == $paymentMethod){
                if(count($map)>1){
                    if($map[1] == $cardType){
                        $termOfPayment = $mapping['economic_term_of_payment_id'];
                        break;
                    }
                }else{
                    $termOfPayment = $mapping['economic_term_of_payment_id'];
                    break;
                }

            }
        }

        if($termOfPayment){
            $this->eapi->order_set_term_of_payment($order_handle, $termOfPayment);
        }
    }

    /**
     *
     * Sets the correct date of the order
     * @param $order_handle e-conomic order handle
     */
    private function set_order_date($order_handle){
        $localTime = $this->order->getCreatedAt();
        $localTime = date_create_from_format('Y#m#d H#i#s', $localTime);
        if($localTime){
            $this->eapi->order_set_date($order_handle, $localTime->format('c'));
        }

    }

    /**
     * Sets the correct currency of the order
     * @param $order_handle e-conomic order handle
     */
    private function set_currency_code($order_handle){
        if(mage::getStoreConfig('economic2_options/orderConfig/use_base_currency', $this->storeId)){
            //Find currency handle
            $currency_handle = $this->eapi->currency_find_by_code($this->order->getBaseCurrencyCode());
        }else{
            //Find currency handle
            $currency_handle = $this->eapi->currency_find_by_code($this->order->getOrderCurrency()->currency_code);
        }

		//Set currency of order
		$this->eapi->order_set_currency($order_handle, $currency_handle);
	}


	private function connect_to_webservice(){
		//Only one connection
		if(!$this->connected){
			$this->eapi = Mage::getModel('economic2/eapi');

            $connection_result = $this->eapi->connect($this->storeId);

			$this->connected = $connection_result;
			if(!$this->connected){
				$this->add_log_entry(Zend_Log::EMERG, 'Cannot connect to e-conomic');
			}
		}
	}

	private function add_shipping_cost_line($order_handle){
        if($this->order->getShippingDescription()){
			if(mage::getStoreConfig('economic2_options/shipping_item_config/use_default_shipping_item', $this->storeId)){
                $economic_item_id = mage::getStoreConfig('economic2_options/shipping_item_config/default_shipping_item', $this->storeId);
                $tmp_order_line_handle = $this->eapi->order_line_create($order_handle);
                $product_handle = $this->eapi->product_get_by_number($economic_item_id);

            }else{
                $serialized_mapping = mage::getStoreConfig('economic2_options/shipping_item_config/shipping_item_mapping', $this->storeId);
                $mapping = @unserialize($serialized_mapping);
                foreach ($mapping as $line) {
                    // Add support for matrixrate shipping methods
                    if (($this->order->getShippingCarrier() && $line['shipping_type_id']==$this->order->getShippingCarrier()->getCarrierCode()) || ($line['shipping_type_id'] == $this->order->getShippingMethod()) ) {
                        //Create line and add to order
                        $tmp_order_line_handle = $this->eapi->order_line_create($order_handle);
                        $product_handle = $this->eapi->product_get_by_number($line['economic_item_id']);
                        break;
                    }
                }
            }

            if($product_handle){
                $this->eapi->order_line_set_product($tmp_order_line_handle, $product_handle);

                if($this->isCreditMemo){
                    $this->eapi->order_line_set_quantity($tmp_order_line_handle, -1);
                }else{
                    $this->eapi->order_line_set_quantity($tmp_order_line_handle, 1);
                }

                $this->eapi->order_line_set_description($tmp_order_line_handle, $this->ConvertBreaksToNewline($this->order->getShippingDescription()));
                $this->eapi->order_line_set_unit_price($tmp_order_line_handle, $this->calculate_shipping_price());


                if(mage::getStoreConfig('economic2_options/orderConfig/default_department', $this->storeId)){
                    $this->eapi->order_line_set_department($tmp_order_line_handle, mage::getStoreConfig('economic2_options/orderConfig/default_department', $this->storeId));
                }
            }

        }
	}

    private function calculate_shipping_price(){
        if(mage::getStoreConfig('economic2_options/discount_item_config/use_row_discount', $this->storeId)){
            if($this->isCreditMemo){
                if(mage::getStoreConfig('economic2_options/orderConfig/use_base_currency', $this->storeId)){
                    return $this->creditMemo->getBaseShippingAmount()-$this->creditMemo->getBaseShippingDiscountAmount();
                }else{
                    return $this->creditMemo->getShippingAmount()-$this->creditMemo->getShippingDiscountAmount();
                }
            }else{
                if(mage::getStoreConfig('economic2_options/orderConfig/use_base_currency', $this->storeId)){
                    return $this->order->getBaseShippingAmount()-$this->order->getBaseShippingDiscountAmount();
                }else{
                    return $this->order->getShippingAmount()-$this->order->getShippingDiscountAmount();
                }
            }
        }else{
            if($this->isCreditMemo){
                if(mage::getStoreConfig('economic2_options/orderConfig/use_base_currency', $this->storeId)){
                    return $this->creditMemo->getBaseShippingAmount();
                }else{
                    return $this->creditMemo->getShippingAmount();
                }
            }else{
                if(mage::getStoreConfig('economic2_options/orderConfig/use_base_currency', $this->storeId)){
                    return $this->order->getBaseShippingAmount();
                }else{
                    return $this->order->getShippingAmount();
                }
            }
        }
    }

    /***
     * Adds payment fee to order
     * @param $order_handle
     */
    private function add_payment_fee_line($order_handle){
        $payment = $this->order->getPayment();
        $invoiceFee = 0;
        if ($payment->getMethod() == "klarna_invoice") {
            $info = $payment->getMethodInstance()->getInfoInstance();
            if ($info->getAdditionalInformation("invoice_fee")) {
                $invoiceFee = $info->getAdditionalInformation('invoice_fee_exluding_vat');
            }
        }elseif($payment->getMethod() == "vaimo_klarna_invoice"){
            $info = $payment->getMethodInstance()->getInfoInstance();
            if ($info->getAdditionalInformation("vaimo_klarna_base_fee")) {
                $invoiceFee = $info->getAdditionalInformation('vaimo_klarna_base_fee');
            }
        }elseif($payment->getMethod() == 'quickpaypayment_payment'){
            if(Mage::getSingleton('core/resource')->getConnection('core_read')->isTableExists('quickpaypayment_order_status')){
                $read = Mage::getSingleton('core/resource')->getConnection('core_read');
                $invoiceFee = $read->fetchRow("select fee from quickpaypayment_order_status where ordernum = '" . $this->order->getIncrementId()."'");
                $invoiceFee = $invoiceFee['fee']/(100.0);
            }
        }

        if($invoiceFee != 0) {
            $payment_fee_itemid = mage::getStoreConfig('economic2_options/payment_fee_item_config/payment_fee_item', $this->storeId);
            if($payment_fee_itemid){

                //Create line and add to order
                $tmp_order_line_handle = $this->eapi->order_line_create($order_handle);

                //set order e-conomic to the chosen
                $product_handle = $this->eapi->product_get_by_number($payment_fee_itemid);
                $this->eapi->order_line_set_product($tmp_order_line_handle, $product_handle);

                //If credit memo, multiply with -1
                if($this->isCreditMemo){
                    $this->eapi->order_line_set_quantity($tmp_order_line_handle, -1);
                }else{
                    $this->eapi->order_line_set_quantity($tmp_order_line_handle, 1);
                }

                //Set description of fee line
                $paymentFeeLabel = mage::getStoreConfig('economic2_options/payment_fee_item_config/payment_fee_label', $this->storeId);
                $this->eapi->order_line_set_description($tmp_order_line_handle, $paymentFeeLabel);

                //Set fee
                $this->eapi->order_line_set_unit_price($tmp_order_line_handle, $invoiceFee);

                //Set department if used
                if(mage::getStoreConfig('economic2_options/orderConfig/default_department', $this->storeId)){
                    $this->eapi->order_line_set_department($tmp_order_line_handle, mage::getStoreConfig('economic2_options/orderConfig/default_department', $this->storeId));
                }

            }
        }

    }

	private function add_discount_line($order_handle){
        if(!mage::getStoreConfig('economic2_options/discount_item_config/use_row_discount', $this->storeId)) {
            if ((!$this->isCreditMemo && ((float)$this->order->getDiscountAmount()) != 0) || ($this->isCreditMemo && ((float)$this->creditMemo->getDiscountAmount()) != 0)) {
                $discount_itemid = mage::getStoreConfig('economic2_options/discount_item_config/discount_item', $this->storeId);
                if ($discount_itemid) {
                    //Create line and add to order
                    $tmp_order_line_handle = $this->eapi->order_line_create($order_handle);
                    $product_handle = $this->eapi->product_get_by_number($discount_itemid);
                    $this->eapi->order_line_set_product($tmp_order_line_handle, $product_handle);


                    if ($this->isCreditMemo) {
                        $this->eapi->order_line_set_quantity($tmp_order_line_handle, -1);
                    } else {
                        $this->eapi->order_line_set_quantity($tmp_order_line_handle, 1);
                    }


                    if ($this->order->getDiscountDescription()) {
                        $discountLabel = Mage::helper('sales')->__('Discount (%s)', $this->order->getDiscountDescription());
                    } else {
                        $discountLabel = Mage::helper('sales')->__('Discount');
                    }

                    $this->eapi->order_line_set_description($tmp_order_line_handle, $discountLabel);


                    if ($this->isCreditMemo) {

                        $negmult = -1;
                        $magentoVersion = Mage::getVersion();
                        if (version_compare($magentoVersion, '1.9', '>=')) {
                            $negmult = 1;
                        }

                        if (mage::getStoreConfig('economic2_options/orderConfig/use_base_currency', $this->storeId)) {
                            $this->eapi->order_line_set_unit_price($tmp_order_line_handle, $negmult * ($this->creditMemo->getBaseDiscountAmount() + $this->creditMemo->getBaseHiddenTaxAmount()));
                        } else {
                            $this->eapi->order_line_set_unit_price($tmp_order_line_handle, $negmult * ($this->creditMemo->getDiscountAmount() + $this->creditMemo->getHiddenTaxAmount()));
                        }
                    } else {
                        if (mage::getStoreConfig('economic2_options/orderConfig/use_base_currency', $this->storeId)) {
                            $this->eapi->order_line_set_unit_price($tmp_order_line_handle, $this->order->getBaseDiscountAmount() + $this->order->getBaseHiddenTaxAmount());
                        } else {
                            $this->eapi->order_line_set_unit_price($tmp_order_line_handle, $this->order->getDiscountAmount() + $this->order->getHiddenTaxAmount());
                        }
                    }

                    if (mage::getStoreConfig('economic2_options/orderConfig/default_department', $this->storeId)) {
                        $this->eapi->order_line_set_department($tmp_order_line_handle, mage::getStoreConfig('economic2_options/orderConfig/default_department', $this->storeId));
                    }
                }
            }
        }
	}

    private function add_rewardpoints_line($order_handle){
        if(!mage::getStoreConfig('economic2_options/discount_item_config/use_row_discount', $this->storeId)) {
            if ($this->order->getRewardpointsBaseDiscount() && $this->order->getRewardpointsBaseDiscount() != 0) {
                $rewardPoints_itemid = mage::getStoreConfig('economic2_options/discount_item_config/reward_points_item', $this->storeId);
                $rewardPoints_text = mage::getStoreConfig('economic2_options/discount_item_config/reward_points_text', $this->storeId);
                if ($rewardPoints_itemid) {

                    //Create line and add to order
                    $tmp_order_line_handle = $this->eapi->order_line_create($order_handle);
                    $product_handle = $this->eapi->product_get_by_number($rewardPoints_itemid);
                    $this->eapi->order_line_set_product($tmp_order_line_handle, $product_handle);

                    if ($this->isCreditMemo) {
                        $this->eapi->order_line_set_quantity($tmp_order_line_handle, 1);
                    } else {
                        $this->eapi->order_line_set_quantity($tmp_order_line_handle, -1);
                    }

                    $rewardPoints_tax_rate = mage::getStoreConfig('economic2_options/discount_item_config/reward_points_tax_rate', $this->storeId)/100.0;

                    if (mage::getStoreConfig('economic2_options/orderConfig/use_base_currency', $this->storeId)) {
                        $amount = $this->order->getRewardpointsBaseDiscount() / (1+$rewardPoints_tax_rate);
                        $this->eapi->order_line_set_unit_price($tmp_order_line_handle, $amount);
                    } else {
                        $amount = $this->order->getRewardpointsDiscount() / (1+$rewardPoints_tax_rate);
                        $this->eapi->order_line_set_unit_price($tmp_order_line_handle, $amount);
                    }

                    $this->eapi->order_line_set_description($tmp_order_line_handle, $rewardPoints_text);
                }

            }
        }
    }

    private function add_credit_memo_adjustment($order_handle){
        if($this->isCreditMemo){
            if(((float)$this->creditMemo->getAdjustment()) != 0){
                $adjustment_itemid = mage::getStoreConfig('economic2_options/credit_memo_item_config/credit_memo_adjustment_item', $this->storeId);
                $adjustment_text = mage::getStoreConfig('economic2_options/credit_memo_item_config/credit_memo_adjustment_text', $this->storeId);

                //Create line and add to order
                $tmp_order_line_handle = $this->eapi->order_line_create($order_handle);
                $product_handle = $this->eapi->product_get_by_number($adjustment_itemid);
                $this->eapi->order_line_set_product($tmp_order_line_handle, $product_handle);
                $this->eapi->order_line_set_quantity($tmp_order_line_handle, -1);
                $this->eapi->order_line_set_description($tmp_order_line_handle, $adjustment_text);

                if(mage::getStoreConfig('economic2_options/orderConfig/use_base_currency', $this->storeId)){
                    $this->eapi->order_line_set_unit_price($tmp_order_line_handle, $this->creditMemo->getBaseAdjustment());
                }else{
                    $this->eapi->order_line_set_unit_price($tmp_order_line_handle, $this->creditMemo->getAdjustment());
                }

                if(mage::getStoreConfig('economic2_options/orderConfig/default_department', $this->storeId)){
                    $this->eapi->order_line_set_department($tmp_order_line_handle, mage::getStoreConfig('economic2_options/orderConfig/default_department', $this->storeId));
                }

            }
        }
    }

    private function add_order_lines($order_handle){
        //All items contained in order
        if($this->isCreditMemo){
            $items = $this->creditMemo->getAllItems();
        }else{
            $items = $this->order->getAllVisibleItems();
        }
		foreach ($items as $item) {

            /* @var $item Mage_Sales_Model_Order_Item */
            if($this->isCreditMemo){
                $productType = $item->getOrderItem()->getProductType();
            }else{
                $productType = $item->getProductType();
            }
			switch ($productType) {
				case 'simple':
					$product_handle = $this->create_product($item, false, $this->storeId);
					$this->set_order_line($order_handle, $product_handle, $item);
					break;
				case 'virtual':
					$product_handle = $this->create_product($item, false, $this->storeId);
					$this->set_order_line($order_handle, $product_handle, $item);
					break;
				case 'downloadable':
					$product_handle = $this->create_product($item, false, $this->storeId);
					$this->set_order_line($order_handle, $product_handle, $item);
					break;
				case 'grouped':
					$product_handle = $this->create_product($item, false, $this->storeId);
					$this->set_order_line($order_handle, $product_handle, $item);
					break;
				case 'configurable':
                    if($this->isCreditMemo) {
                        $qty = $item->getQty();
                        $item = $item->getOrderItem();
                        $item->setQty($qty);

                        //When configurable products are part of a credit memo, both the configurable product and the simple
                        //product are present in getAllItems. We only want the configurable product, and instead fetch
                        //the simple product later on (as with normal orders)
                        if ($item->getParentItem()) {
                            continue;
                        }
                    }

                    if($item->getChildrenItems()){
                        foreach ($item->getChildrenItems() as $subitem) {
                            /* @var $subitem Mage_Sales_Model_Order_Item */
                            $product_handle = $this->create_product($subitem, false, $this->storeId);
                            $this->set_order_line($order_handle, $product_handle, $item);
                        }
                    }
					break;
				case 'bundle':

                    $product = Mage::getModel('catalog/product')->setStoreId($this->storeId)->load($item->getProductId());
                    $priceType = $product->getPriceType();

                    $product_handle = $this->create_product($item, false, $this->storeId);

                    if($priceType == Mage_Bundle_Model_Product_Price::PRICE_TYPE_DYNAMIC){
                        $this->set_order_line($order_handle, $product_handle, $item, 0);
                    }elseif($priceType == Mage_Bundle_Model_Product_Price::PRICE_TYPE_FIXED){
                        $this->set_order_line($order_handle, $product_handle, $item);
                    }

					if($item->getChildrenItems()){
						foreach ($item->getChildrenItems() as $subitem) {
							/* @var $subitem Mage_Sales_Model_Order_Item */
							$product_handle = $this->create_product($subitem, false, $this->storeId);
							$this->set_order_line($order_handle, $product_handle, $subitem);
						}
					}
					break;
				default:
					break;
			}
		}
	}

	private function set_order_line($order_handle, $product_handle, $item, $price = null){
        if($product_handle){
			//Create orderline and add product
			$order_line_handle = $this->eapi->order_line_create($order_handle);
			$this->eapi->order_line_set_product($order_line_handle, $product_handle);

            if($this->isCreditMemo){
                $this->eapi->order_line_set_quantity($order_line_handle, $item->getQty()*-1);
            }else{
                $this->eapi->order_line_set_quantity($order_line_handle, $item->getQtyOrdered());
            }

            $this->eapi->order_line_set_description($order_line_handle, $this->get_product_name($item));
			$this->eapi->order_line_set_unit($order_line_handle, $this->get_product_unit($item));
            if($price === null){
                if($item->getQtyToInvoice()>0 || $item->getQtyInvoiced()>0 || ($this->isCreditMemo && $item->getQty()>0)){
                    $this->eapi->order_line_set_unit_price($order_line_handle, $this->calculate_row_price($item));
                }
            }else{
                $this->eapi->order_line_set_unit_price($order_line_handle, $price);
            }

            if(mage::getStoreConfig('economic2_options/orderConfig/default_department', $this->storeId)){
                $this->eapi->order_line_set_department($order_line_handle, mage::getStoreConfig('economic2_options/orderConfig/default_department', $this->storeId));
            }

            if(mage::getStoreConfig('economic2_options/productConfig/inventory_location', $this->storeId) > 0){
                $this->eapi->order_line_set_inventory_location($order_line_handle, mage::getStoreConfig('economic2_options/productConfig/inventory_location', $this->storeId));
            }

		}
	}

    private function calculate_row_price($item){
        if(mage::getStoreConfig('economic2_options/discount_item_config/use_row_discount', $this->storeId)){
            if(mage::getStoreConfig('economic2_options/orderConfig/use_base_currency', $this->storeId)){
                $rewardpointsBaseDiscount = 0;
                if($item->getRewardpointsBaseDiscount()){
                    $rewardpointsBaseDiscount = $item->getRewardpointsBaseDiscount();
                }
                return $item->getBasePrice()+$item->getBaseHiddenTaxAmount()-$item->getBaseDiscountAmount()-$rewardpointsBaseDiscount;
            }else{
                $rewardpointsDiscount = 0;
                if($item->getRewardpointsDiscount()){
                    $rewardpointsDiscount = $item->getRewardpointsDiscount();
                }
                return $item->getPrice()+$item->getHiddenTaxAmount()-$item->getDiscountAmount()-$rewardpointsDiscount;
            }
        }else{
            if(mage::getStoreConfig('economic2_options/orderConfig/use_base_currency', $this->storeId)){
                return $item->getBasePrice();
            }else{
                return $item->getPrice();
            }
        }
    }

	private function add_order_comment($comment, $new_status = ''){
        if(mage::getStoreConfig('economic2_options/orderConfig/add_comment_when_synced', $this->storeId)){
			if ($new_status){
				$this->order->addStatusHistoryComment($comment, $new_status);
			}else{
                $this->order->addStatusHistoryComment($comment, $this->order->getStatus());
			}
		}
		$this->save_flag = true;
		$this->order->save();
	}


	/**
	 *
	 * Returns debtor handle depending on configuration.
	 */
	private function create_debtor(){

        $economicDebtor = Nybohansen_Economic2_Model_EconomicDebtor::fromOrder($this->order);
        $debtor_handle = $economicDebtor->sendToEconomic();

		//Set currency of debtor - this is only done when the debtor is created through an order
		$this->eapi->debtor_set_currency($debtor_handle, $this->eapi->currency_find_by_code($this->order->getOrderCurrency()->currency_code));

        return $debtor_handle;

	}


    private function insert_debtor_contact($order_handle, $debtor_handle){
        if(mage::getStoreConfig('economic2_options/debtorConfig/use_contactpersons', $this->storeId)  && mage::getStoreConfig('economic2_options/debtorConfig/debtor_identification_field', $this->storeId) == $this->configuration->DEBTOR_IDENTIFICATION_CVR){
            //Inserts debtor contact on an order, if this feature is used
            $debtor_contact_handle = $this->create_debtor_contact($debtor_handle, array('name' => $this->getCustomerName($this->order->getBillingAddress()), 'phone' => $this->order_billing_address->getTelephone(), 'email' => $this->order->getCustomerEmail()));
            $this->eapi->order_set_attention($order_handle, $debtor_contact_handle);
        }
    }

    private function create_debtor_contact($economic_debtor_handle, $customer_data){

        //Investigate if debtor contact already exists based on name
        $debtorContactArray = $this->eapi->debtor_get_debtorContacts($economic_debtor_handle);
        foreach($debtorContactArray as $debtorContact){
            if($debtorContact->Email == $customer_data['name']){
                return $debtorContact->Handle;
            }
        }

        //Based on e-mail, the debtor has no contact with the specific e-mail address, create new
        $debtorContact_handle = $this->eapi->debtor_contact_create($economic_debtor_handle, $customer_data['name']);
        $this->eapi->debtor_contact_set_telephone($debtorContact_handle, $customer_data['phone']);
        $this->eapi->debtor_contact_set_email($debtorContact_handle, $customer_data['email']);
        //Returns debtor_contact_handle
        return $debtorContact_handle;

    }

	/**
	 *
	 * Adds the billing address to the e-conomic order
	 * @param order handle $order_handle
	 */
	private function order_set_billing_address($order_handle, $debtor_handle){
        $this->eapi->order_set_debtor_name($order_handle, $this->getCustomerName($this->order_billing_address));

        //Set attention name if company name is present on order, and
        if(mage::getStoreConfig('economic2_options/debtorConfig/use_company_name_if_available', $this->storeId) && $this->order_billing_address->getCompany()){
            $debtor_contact_handle = $this->create_debtor_contact($debtor_handle, array('name' => $this->order_billing_address->getName(), 'phone' => $this->order_billing_address->getTelephone(), 'email' => $this->order->getCustomerEmail()));
            $this->eapi->order_set_attention($order_handle, $debtor_contact_handle);
        }

        $this->eapi->order_set_debtor_address($order_handle, $this->order_billing_address->getStreetFull());
		$this->eapi->order_set_debtor_city($order_handle, $this->order_billing_address->getCity());

        $countryName = Mage::app()->getLocale()->getCountryTranslation($this->order_billing_address->getCountry());
        $this->eapi->order_set_debtor_country($order_handle, $countryName);

		$this->eapi->order_set_debtor_postal_code($order_handle, $this->order_billing_address->getPostcode());
	}

	/**
	 *
	 * Adds the shipping address to the e-conomic order
	 * @param Order handle $order_handle
	 */
	private function order_set_shipping_address($order_handle){
		if($this->order_shipping_address){

            $deliveryLocationHandle = $this->eapi->deliveryLocation_findByExternalId($this->order_shipping_address->getCustomerAddressId());
            if($deliveryLocationHandle){
                $this->eapi->order_SetDeliveryLocation($order_handle, $deliveryLocationHandle);
            }

            //set normal address without creating delivery location
            //This is done e.g. when customer is logged in as guest
            $this->eapi->order_set_delivery_address($order_handle, $this->getCustomerName($this->order_shipping_address)."\n".$this->order_shipping_address->getStreetFull());
            $this->eapi->order_set_delivery_city($order_handle, $this->order_shipping_address->getCity());

            $countryName = Mage::app()->getLocale()->getCountryTranslation($this->order_shipping_address->getCountry());
            $this->eapi->order_set_delivery_country($order_handle, $countryName);

            $this->eapi->order_set_delivery_postal_code($order_handle, $this->order_shipping_address->getPostcode());

		}
	}

    private function get_product_group($item){

        if(mage::getStoreConfig('economic2_options/productConfig/product_group_use_default', $this->storeId)){
            $product_group_number = mage::getStoreConfig('economic2_options/productConfig/default_product_group', $this->storeId);
			$product_group_handle = $this->eapi->product_group_get_by_number($product_group_number);
		}else{
            $attribute_name = mage::getStoreConfig('economic2_options/productConfig/product_group_attribute', $this->storeId);
			$product_group_id = $item->getData($attribute_name);
            $product_group_handle = $this->eapi->product_group_get_by_number($product_group_id);
		}
		if (!$product_group_handle) {
			$this->add_log_entry(Zend_log::ALERT, 'Product group not found, cannot create product');
		}
		return $product_group_handle;
	}


	/**
	 *
	 * Returns the magento product id used as primary key between the
	 * e-conomic products and magento products.
	 * @param $item
	 */
	private function get_product_id(Mage_Catalog_Model_Product $item){
		$prefix = mage::getStoreConfig('economic2_options/productConfig/product_id_prefix', $this->storeId);
		if(mage::getStoreConfig('economic2_options/productConfig/product_id_use_default', $this->storeId)){
			$id = $item->getEntityId();
		}else{
			$attribute_name = mage::getStoreConfig('economic2_options/productConfig/product_id_attribute', $this->storeId);
			$id = $item->getData($attribute_name);
		}
		//Only use the first 25 characters - limitation of e-conomic
		if(strlen($prefix.$id)>25){
			$this->add_log_entry(Zend_Log::CRIT, 'Product with prefix + id: '.$prefix.$id.' has more than 25 chars! Reducing to 25 ...');
		}
		return substr($prefix.$id, 0, 25);
	}

	/**
	 *
	 * Returns the e-conomic unit of the product
	 * @param Mage_Catalog_Model_Product $item
	 * @return unit_handle
	 */
	private function get_product_unit($item){

        //Magento product id
        $magento_product_id = $item->getProductId();

        if(!$magento_product_id){
            //If $item is not of type Mage_Sales_Model_Order_Item get product id from an alternative method
            $magento_product_id = $item->getEntityId();
        }
        //Load product model
        $item = Mage::getModel('catalog/product')->load($magento_product_id);


		if (mage::getStoreConfig('economic2_options/productConfig/product_unit_use_default', $this->storeId)) {
			$unit_name = mage::getStoreConfig('economic2_options/productConfig/product_unit_default', $this->storeId);
			$unit_handle = $this->eapi->unit_get_by_name($unit_name);
		}else{
			$attribute_name = mage::getStoreConfig('economic2_options/productConfig/product_unit_attribute', $this->storeId);
            $unit_name = $item->getResource()->getAttribute($attribute_name)->getFrontend()->getValue($item);
			$unit_handle = $this->eapi->unit_get_by_name($unit_name);
		}
		if(!$unit_handle){
			if (mage::getStoreConfig('economic2_options/productConfig/product_unit_create_if_not_exists', $this->storeId)) {
				//Create new unit
				$unit_handle = $this->eapi->unit_create_new($unit_name);
			}else{
                //Unit does not exist and we do not want to create new unit. Error!
                $this->add_log_entry(Zend_log::CRIT, 'Unit '.$unit_name.' does not exists, and user choose not to create new units in configuration');
            }
		}
		return $unit_handle;
	}


	/**
	 *
	 * Calculates product name incl. options
	 * @param $item
	 */
	private function get_product_name($item){
		$result = array();
		if ($options = $item->getProductOptions()) {
			if (isset($options['options'])) {
				$result = array_merge($result, $options['options']);
			}
			if (isset($options['additional_options'])) {
				$result = array_merge($result, $options['additional_options']);
			}
			if (!empty($options['attributes_info'])) {
				$result = array_merge($options['attributes_info'], $result);
			}
		}
		$name = $item->getName();
		foreach ($result as $choice) {
			$name .= "\n".$choice['label'].': '.$choice['value'];
		}
		return $name;
	}

    private function add_economic_comment($order_handle){
        if(mage::getStoreConfig('economic2_options/orderConfig/store_order_number_in_economic', $this->storeId)){

            $text =  mage::getStoreConfig('economic2_options/orderConfig/order_number_prefix', $this->storeId).$this->order->getIncrementId();

            if(mage::getStoreConfig('economic2_options/orderConfig/store_order_number_in', $this->storeId) == $this->configuration->STORE_ORDER_NUMBER_IN_HEADING){
                 $this->eapi->order_set_heading($order_handle, $text);
             }elseif (mage::getStoreConfig('economic2_options/orderConfig/store_order_number_in', $this->storeId) == $this->configuration->STORE_ORDER_NUMBER_IN_OTHER_REF){
                 $this->eapi->order_set_other_reference($order_handle, $text);
             }elseif (mage::getStoreConfig('economic2_options/orderConfig/store_order_number_in', $this->storeId) == $this->configuration->STORE_ORDER_COMMENT_TEXTLINE1){
                 $this->eapi->order_set_textline1($order_handle, $text);
             }elseif (mage::getStoreConfig('economic2_options/orderConfig/store_order_number_in', $this->storeId) == $this->configuration->STORE_ORDER_COMMENT_TEXTLINE2){
                 $this->eapi->order_set_textline2($order_handle, $text);
             }
        }
    }

    /**
     * Returns name as company name or customer name depending on configuration and if company name is set
     * @param $address
     */
    private function getCustomerName($address){
        if(mage::getStoreConfig('economic2_options/debtorConfig/use_company_name_if_available', $this->storeId) && $address->getCompany()){
            return $address->getCompany();
        }else{
            return $address->getName();
        }
    }

    private function ConvertBreaksToNewline($text){
        $breaks = array("<br />","<br>","<br/>");
        return str_ireplace($breaks, "\r\n", $text);
    }

	/**
	 *
	 * Add log entry to economic2.log
	 *
	 * @param $level  Zend_Log::level
	 * @param $object Message to write in log
	 */
	private function add_log_entry($level, $object){
		mage::log($object, $level, 'e-conomic2.log');
	}


}