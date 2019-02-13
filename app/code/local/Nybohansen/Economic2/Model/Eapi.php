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

//define ('WDSL_URL', 'https://api.e-conomic.com/secure/api1/EconomicWebService.asmx?wsdl');

class Nybohansen_Economic2_Model_Eapi {
	

	private $client;
	private $wsdlUrl; 
	private $debugging;
    private $connected;
	
	/**
	 * 
	 * Constructor, makes new connection to web service and returns object
	 * @param $agreement_number e-conomic agreement number
	 * @param $username e-conomic username
	 * @param $password e-conomic password
	 */
	function __construct(){
        $this->connected = false;
	}	
	
	function show_debug($value){
		$this->debugging = $value;
	}

    function constructUrl(){
        $this->wsdlUrl = mage::getStoreConfig('economic2_options/moduleInfo/api_url').'?wsdl';
    }

    /***
     * Automatically finds out which method to use, and connects to the e-conomic API
     */
    function connect($storeId = null){
        if(mage::getStoreConfig('economic2_options/accountInfo/use_token_based_login', $storeId)){
            $token = mage::getStoreConfig('economic2_options/accountInfo/apiToken', $storeId);
            return $this->connectWithToken($token);
        }else{
            $agreement_number = mage::getStoreConfig('economic2_options/accountInfo/agreement_number', $storeId);
            $username = mage::getStoreConfig('economic2_options/accountInfo/username', $storeId);
            $password = mage::getStoreConfig('economic2_options/accountInfo/password', $storeId);
            return $this->connectWithCredentials($agreement_number, $username, $password);
        }

    }

	function connectWithToken($token){
        if(!$this->connected){
            try {
                $this->client = $this->createSoapClient();
                $this->client->ConnectWithToken(array('token' => $token, 'appToken' => Mage::getConfig()->getNode('modules')->Nybohansen_Economic2->appToken));
                $this->connected = true;
                return true;
            } catch (Exception $e) {
                $this->connected = false;
                $this->exception_handler($e, 'Could not connect to E-Conomic API using token.');
                return false;
            }
        }
        return true;
	}
	
	function connectWithCredentials($merchant_id, $user, $password){
        if(!$this->connected){
            $this->agreement_number = $merchant_id;
            $this->username = $user;
            $this->password = $password;
            try {
                $this->client = $this->createSoapClient();
                $this->client->Connect(array('agreementNumber' => $this->agreement_number,
                                             'userName'        => $this->username,
                                             'password'        => $this->password));
                $this->connected = true;
                return true;
            } catch (Exception $e) {
                $this->connected = false;
                $this->exception_handler($e, 'Could not connect to E-Conomic API using Merchant id '.$merchant_id);
                return false;
            }
        }
        return true;
	}

    function createSoapClient(){
        $appIdentifier = 'Magentomoduler (http://magentomoduler.dk/e-conomic-modul.html); kontakt@magentomoduler.dk) PHP-SOAP';
        $context = stream_context_create(array('http' => array('header' => 'X-EconomicAppIdentifier: '.$appIdentifier)));
        $this->constructUrl();
        $client = new Zend_Soap_Client($this->wsdlUrl);
        $client->setStreamContext($context);
        return $client;
    }

    function verify_header(){
        if($this->is_connected()){
            try {
                return $debtor_group_handles = $this->client->Verify_XEconomicAppIdentifier()->Verify_XEconomicAppIdentifierResult;
            } catch (Exception $e) {
                $this->exception_handler($e, 'Could not verify header');
            }
        }
    }

	/**
	 * Returns all debtor groups available
	 */
	function debtor_groups_get_all(){
        $res = array();
        if($this->is_connected()){
            $debtor_group_handles = $this->client->DebtorGroup_GetAll()->DebtorGroup_GetAllResult->DebtorGroupHandle;
            if(count($debtor_group_handles)){
                if(count($debtor_group_handles)>1){
                    foreach ($debtor_group_handles as $debtor_group_handle) {
                        $res[$debtor_group_handle->Number] = $this->client->DebtorGroup_GetName(array('debtorGroupHandle' => $debtor_group_handle))->DebtorGroup_GetNameResult;
                    }
                }else{
                    $debtor_group_handle = $debtor_group_handles;
                    $res[$debtor_group_handle->Number] = $this->client->DebtorGroup_GetName(array('debtorGroupHandle' => $debtor_group_handle))->DebtorGroup_GetNameResult;
                }
            }
        }
		return $res;
	}
	
	
	function debtor_groups_find_by_name($name){
		$res = array();
        if($this->is_connected()){
            try {
                $debtor_group_handles = $this->client->DebtorGroup_FindByName(array('name' => $name))->DebtorGroup_FindByNameResult;
                foreach ($debtor_group_handles as $debtor_group_handle) {
                    $res[$debtor_group_handle->Number] = $this->client->DebtorGroup_GetName(array('debtorGroupHandle' => $debtor_group_handle))->DebtorGroup_GetNameResult;
                }
            } catch (Exception $e) {
                $this->exception_handler($e, 'Could not find debtor groups by name.');
            }
        }
        return $res;
	}
	
	function debtor_groups_find_by_number($number){
        if($this->is_connected()){
            try {
                return $this->client->DebtorGroup_FindByNumber(array('number' => $number))->DebtorGroup_FindByNumberResult;
            } catch (Exception $e) {
                $this->exception_handler($e, 'Could not find debtor groups by number.');
            }
        }
	}
	
	/**
	 * Returns all vat_zones available
	 */
	function vat_zones_get(){
		$res = array();
        if($this->is_connected()){
            $debtor_group_handles = $this->client->DebtorGroup_GetAll()->DebtorGroup_GetAllResult->DebtorGroupHandle;
            foreach ($debtor_group_handles as $debtor_group_handle) {
                $res[$debtor_group_handle->Number] = $this->client->DebtorGroup_GetName(array('debtorGroupHandle' => $debtor_group_handle))->DebtorGroup_GetNameResult;
            }
        }
		return $res;
	}
	
	/**
	 * Finds order by order number and returns a order handle
	 * @param Integer $number Order number
	 */	
	function order_get_by_number($number){
        if($this->is_connected()){
            try {
                $res = $this->client->Order_FindByNumber(array('number' => $number));
                if(property_exists($res, 'Order_FindByNumberResult')){
                    return $res->Order_FindByNumberResult;
                }
                return false;
            } catch (Exception $e) {
                $this->exception_handler($e, 'Could not find order by number.');
            }
        }
	} 
	
	function order_get_by_other_reference($reference){
        if($this->is_connected()){
            try {
                return $this->client->Order_FindByOtherReference(array('otherReference' => $reference))->Order_FindByOtherReferenceResult->OrderHandle;
            } catch (Exception $e) {
                $this->exception_handler($e, 'Could not find order by other reference.');
            }
        }
	}
	
	
	function order_get_number($order_handle){
        if($this->is_connected()){
            try {
                return $this->client->Order_GetNumber(array('orderHandle' => $order_handle))->Order_GetNumberResult;
            } catch (Exception $e) {
                $this->exception_handler($e, 'Could not find order by other reference.');
            }
        }
	}

    function order_get_data($order_handle){
        if($this->is_connected()){
            try {
                return $this->client->Order_GetData(array('entityHandle' => $order_handle))->Order_GetDataResult;
            } catch (Exception $e) {
                $this->exception_handler($e, 'Could not get order.');
            }
        }
    }

    function order_update_from_data($data){
        if($this->is_connected()){
            try {
                return $this->client->Order_UpdateFromData(array('data' => $data))->Order_UpdateFromDataResult;
            } catch (Exception $e) {
                $this->exception_handler($e, 'Could not get update order.');
            }
        }
    }


    /**
	 * Creates new order for debtor and returns order handle
	 * @param $debtor_handle Handle for debtor
	 */
	function order_create($debtor_handle){
        if($this->is_connected()){
            try {
                return $this->client->Order_Create(array('debtorHandle' => $debtor_handle))->Order_CreateResult;
            } catch (Exception $e) {
                $this->exception_handler($e, 'Could not create order.');
            }
        }
	}
	
	function order_delete($order_handle){
        if($this->is_connected()){
            try {
                return $this->client->Order_Delete(array('orderHandle' => $order_handle));
            } catch (Exception $e) {
                $this->exception_handler($e, 'Could not delete order.');
            }
        }
	}
	
	
	function order_set_currency($order_handle, $currency_code){
        if($this->is_connected()){
            try {
                return $this->client->Order_SetCurrency(array('orderHandle' => $order_handle,
                                                              'valueHandle' => $currency_code));
            } catch (Exception $e) {
                $this->exception_handler($e, 'Could not set currency of order.');
            }
        }
	}

    function order_set_date($order_handle, $date){
        if($this->is_connected()){
            try {
                return $this->client->Order_SetDate(array('orderHandle' => $order_handle,
                                                           'value' => $date));
            } catch (Exception $e) {
                $this->exception_handler($e, 'Could not set date of order.');
            }
        }
    }


	/**
	 * Creates new order line for order. Returns handle to orderline
	 * @param Handle $order_handle Handle for order to create new order line
	 */
	function order_line_create($order_handle){
        if($this->is_connected()){
            try {
                return $this->client->OrderLine_Create(array('orderHandle' => $order_handle))->OrderLine_CreateResult;
            } catch (Exception $e) {
                $this->exception_handler($e, 'Could not create order line.');
            }
        }
	}
	
	
	
	function order_line_set_product($order_line_handle, $product_handle){
        if($this->is_connected()){
            try {
                return $this->client->OrderLine_SetProduct(array('orderLineHandle' => $order_line_handle,
                                                                 'valueHandle' => $product_handle));
            } catch (Exception $e) {
                $this->exception_handler($e, 'Could not set product of order line.');
            }
        }
	}
	
	function order_line_set_quantity($order_line_handle, $quantity){
        if($this->is_connected()){
            try {
                return $this->client->OrderLine_SetQuantity(array('orderLineHandle' => $order_line_handle,
                                                                   'value' => $quantity));
            } catch (Exception $e) {
                $this->exception_handler($e, 'Could not set quantity of order line.');
            }
        }
	} 
	
	function order_line_set_description($order_line_handle, $description){
        if($this->is_connected()){
            try {
                return $this->client->OrderLine_SetDescription(array('orderLineHandle' => $order_line_handle,
                                                                      'value' => $description));
            } catch (Exception $e) {
                $this->exception_handler($e, 'Could not set description of order line.');
            }
        }
	}
	
	function order_line_set_unit($order_line_handle, $unit_handle){
        if($this->is_connected()){
            try {
                return $this->client->OrderLine_SetUnit(array('orderLineHandle' => $order_line_handle,
                                                              'valueHandle' => $unit_handle));
            } catch (Exception $e) {
                $this->exception_handler($e, 'Could not set unit of order line.');
            }
        }
	}
	
	function order_line_set_unit_price($order_line_handle, $unit_price){
        if($this->is_connected()){
            try {
                return $this->client->OrderLine_SetUnitNetPrice(array('orderLineHandle' => $order_line_handle,
                                                                      'value' => $unit_price));
            } catch (Exception $e) {
                $this->exception_handler($e, 'Could not set base price of order line.');
            }
        }
	}

    function order_line_set_department($order_line_handle, $department){
        if($this->is_connected()){
            try {
                return $this->client->OrderLine_SetDepartment(array('orderLineHandle' => $order_line_handle, 'valueHandle' => array('Number' => $department)));
            } catch (Exception $e) {
                $this->exception_handler($e, 'Could not set department of order line.');
            }
        }
    }

    function order_line_set_inventory_location($order_line_handle, $inventory_location_id){
        if($this->is_connected()){
            try {
                return $this->client->OrderLine_SetInventoryLocation(array('orderLineHandle' => $order_line_handle, 'valueHandle' => array('Number' => $inventory_location_id)));
            } catch (Exception $e) {
                $this->exception_handler($e, 'Could not set inventory location of order line.');
            }
        }

    }

	/**
	 * Upgrades order to invoice with the data curently present in the order. Returns handle to invoice.
	 * @param Handle $order_handle Handle of order
	 */
	function order_upgrade_to_invoice($order_handle){
        if($this->is_connected()){
            try {
                return $this->client->Order_UpgradeToInvoice(array('orderHandle' => $order_handle))->Order_UpgradeToInvoiceResult;
            } catch (Exception $e) {
                $this->exception_handler($e, 'Could not upgrade order to invoice.');
            }
        }
	}
	
	

	function order_set_heading($order_handle, $text){
        if($this->is_connected()){
            try {
                return $this->client->Order_SetHeading(array('orderHandle' => $order_handle, 'value' => $text));
            } catch (Exception $e) {
                $this->exception_handler($e, 'Could not set heading of order.');
            }
        }
	}
	
	function order_set_other_reference($order_handle, $text){
        if($this->is_connected()){
            try {
                return $this->client->Order_SetOtherReference(array('orderHandle' => $order_handle, 'value' => $text));
            } catch (Exception $e) {
                $this->exception_handler($e, 'Could not set other reference of order.');
            }
        }
	}
	
	function order_set_textline1($order_handle, $text){
        if($this->is_connected()){
            try {
                return $this->client->Order_SetTextLine1(array('orderHandle' => $order_handle, 'value' => $text));
            } catch (Exception $e) {
                $this->exception_handler($e, 'Could not set textline 1 of order.');
            }
        }
	}
	
	function order_set_textline2($order_handle, $text){
        if($this->is_connected()){
            try {
                return $this->client->Order_SetTextLine2(array('orderHandle' => $order_handle, 'value' => $text));
            } catch (Exception $e) {
                $this->exception_handler($e, 'Could not set textline 2 of order.');
            }
        }
	}

    function order_SetDeliveryLocation($order_handle, $deliveryAddress_handle){
        if($this->is_connected()){
            try {
                return $this->client->Order_SetDeliveryLocation(array('orderHandle' => $order_handle, 'valueHandle' => $deliveryAddress_handle));
            } catch (Exception $e) {
                $this->exception_handler($e, 'Could not set delivery location of order.');
            }
        }
    }

	function order_set_delivery_address($order_handle, $address){
        if($this->is_connected()){
            try {
                return $this->client->Order_SetDeliveryAddress(array('orderHandle' => $order_handle, 'value' => $address));
            } catch (Exception $e) {
                $this->exception_handler($e, 'Could not set delivery address of order.');
            }
        }
	}
	
	function order_set_delivery_city($order_handle, $city){
        if($this->is_connected()){
            try {
                return $this->client->Order_SetDeliveryCity(array('orderHandle' => $order_handle, 'value' => $city));
            } catch (Exception $e) {
                $this->exception_handler($e, 'Could not set delivery city of order.');
            }
        }
	}
	
	function order_set_delivery_country($order_handle, $country){
        if($this->is_connected()){
            try {
                return $this->client->Order_SetDeliveryCountry(array('orderHandle' => $order_handle, 'value' => $country));
            } catch (Exception $e) {
                $this->exception_handler($e, 'Could not set delivery country of order.');
            }
        }
	}
	
	function order_set_delivery_postal_code($order_handle, $postal_code){
        if($this->is_connected()){
            try {
                return $this->client->Order_SetDeliveryPostalCode(array('orderHandle' => $order_handle, 'value' => $postal_code));
            } catch (Exception $e) {
                $this->exception_handler($e, 'Could not set delivery postal code of order.');
            }
        }
	}
	
	function order_set_debtor_address($order_handle, $address){
        if($this->is_connected()){
            try {
                return $this->client->Order_SetDebtorAddress(array('orderHandle' => $order_handle, 'value' => $address));
            } catch (Exception $e) {
                $this->exception_handler($e, 'Could not set debtor address of order.');
            }
        }
	}
	
	function order_set_debtor_city($order_handle, $city){
        if($this->is_connected()){
            try {
                return $this->client->Order_SetDebtorCity(array('orderHandle' => $order_handle, 'value' => $city));
            } catch (Exception $e) {
                $this->exception_handler($e, 'Could not set debtor city of order.');
            }
        }
	}
	
	function order_set_debtor_country($order_handle, $country){
        if($this->is_connected()){
            try {
                return $this->client->Order_SetDebtorCountry(array('orderHandle' => $order_handle, 'value' => $country));
            } catch (Exception $e) {
                $this->exception_handler($e, 'Could not set debtor country of order.');
            }
        }
	}
	
	function order_set_debtor_ean($order_handle, $ean){
        if($this->is_connected()){
            try {
                return $this->client->Order_SetDebtorEan(array('orderHandle' => $order_handle, 'value' => $ean));
            } catch (Exception $e) {
                $this->exception_handler($e, 'Could not set debtor ean of order.');
            }
        }
	}
	
	function order_set_debtor_name($order_handle, $name){
        if($this->is_connected()){
            try {
                return $this->client->Order_SetDebtorName(array('orderHandle' => $order_handle, 'value' => $name));
            } catch (Exception $e) {
                $this->exception_handler($e, 'Could not set debtor name of order.');
            }
        }
	}
	
	function order_set_debtor_postal_code($order_handle, $postal_code){
        if($this->is_connected()){
            try {
                return $this->client->Order_SetDebtorPostalCode(array('orderHandle' => $order_handle, 'value' => $postal_code));
            } catch (Exception $e) {
                $this->exception_handler($e, 'Could not set debtors postal code of order.');
            }
        }
	}
	
	
	function order_set_term_of_payment($order_handle, $term_of_payment_id){
        if($this->is_connected()){
            try {
                return $this->client->Order_SetTermOfPayment(array('orderHandle' => $order_handle, 'valueHandle' => array('Id' => $term_of_payment_id)));
            } catch (Exception $e) {
                $this->exception_handler($e, 'Could not set term of payment for order.');
            }
        }
	}

    function order_set_your_reference($order_handle, $debtor_contact_id){
        try {
            return $this->client->Order_SetYourReference(array('orderHandle' => $order_handle, 'valueHandle' => $debtor_contact_id));
        } catch (Exception $e) {
            $this->exception_handler($e, 'Could not set your reference of order.');
        }
    }

    /**
     * Sets the attention of an order
     * @param $order_handle
     * @param $debtor_contact_id
     * @return mixed
     */
    function order_set_attention($order_handle, $debtor_contact_id){
        try {
            return $this->client->Order_SetAttention(array('orderHandle' => $order_handle, 'valueHandle' => $debtor_contact_id));
        } catch (Exception $e) {
            $this->exception_handler($e, 'Could not set attention of order.');
        }
    }

    function order_get_pdf($order_handle){
        try {
            return $this->client->Order_GetPdf(array('orderHandle' => $order_handle))->Order_GetPdfResult;
        } catch (Exception $e) {
            $this->exception_handler($e, 'Could not get order pdf.');
        }
    }


	/**
	 * Creates new invoice for debtor and returns a handle
	 * @param Handle $debtor_handle Handle of debtor
	 */
	function invoice_create($debtor_handle){
        if($this->is_connected()){
            try {
                return $this->client->CurrentInvoice_Create(array('debtorHandle' => $debtor_handle))->CurrentInvoice_CreateResult;
            } catch (Exception $e) {
                $this->exception_handler($e, 'Could not create invoice.');
            }
        }
	}
	
	/**
	 * Creates new invoice line for invoice. Returns handle to invoice line
	 * @param Handle $invoice_handle handle to invoice
	 */
	function invoice_line_create($invoice_handle){
        if($this->is_connected()){
            try {
                return $this->client->CurrentInvoiceLine_Create(array('currentInvoiceHandle' => $invoice_handle))->CurrentInvoiceLine_CreateResult;
            } catch (Exception $e) {
                $this->exception_handler($e, 'Could not create order line.');
            }
        }
	}

    /**
     * Books invoice with invoice_handle
     * @param $invoice_handle
     * @return mixed
     */
    function invoice_book($invoice_handle){
        if($this->is_connected()){
            try {
                return $this->client->CurrentInvoice_Book(array('currentInvoiceHandle' => $invoice_handle))->CurrentInvoice_BookResult;
            } catch (Exception $e) {
                $this->exception_handler($e, 'Could not book invoice.');
            }
        }
	}

    /**
     * Books invoice with invoice_handle with number
     * @param $invoice_handle
     * @param $number
     * @return mixed
     */
    function invoice_book_with_number($invoice_handle, $number){
        if($this->is_connected()){
            try {
                return $this->client->CurrentInvoice_BookWithNumber(array('currentInvoiceHandle' => $invoice_handle, 'number' => $number))->CurrentInvoice_BookWithNumberResult;
            } catch (Exception $e) {
                $this->exception_handler($e, 'Could not book invoice with number: '.$number);
            }
        }
    }

	
	
	/**
	 * Gets data of invoice
	 * @param Handle $invoice_handle handle to invoice
	 */
	function invoice_get_data($invoice_handle){
        if($this->is_connected()){
            try {
                return $this->client->Invoice_GetData(array('entityHandle' => $invoice_handle))->Invoice_GetDataResult;
            } catch (Exception $e) {
                $this->exception_handler($e, 'Could not get invoice data.');
            }
        }
	}
	
	/**
	 * Gets id of invoice
	 * @param Handle $invoice_handle handle to invoice
	 */
	function invoice_get_number($invoice_handle){
        if($this->is_connected()){
            try {
                return $this->client->Invoice_GetNumber(array('invoiceHandle' => $invoice_handle))->Invoice_GetNumberResult;
            } catch (Exception $e) {
                $this->exception_handler($e, 'Could not get invoice number.');
            }
        }
	}

    /**
     * Gets id of invoice
     * @param Handle $invoice_handle handle to invoice
     */
    function invoice_find_by_number($invoice_number){
        if($this->is_connected()){
            try {
                return $this->client->Invoice_FindByNumber(array('number' => $invoice_number))->Invoice_FindByNumberResult;
            } catch (Exception $e) {
                $this->exception_handler($e, 'Could not find invoice by number.');
            }
        }
    }



    /**
     * Gets id of invoice
     * @param Handle $invoice_handle handle to invoice
     */
    function current_invoice_get_pdf($invoice_handle){
        if($this->is_connected()){
            try {
                return $this->client->CurrentInvoice_GetPdf(array('currentInvoiceHandle' => $invoice_handle))->CurrentInvoice_GetPdfResult;
            } catch (Exception $e) {
                $this->exception_handler($e, 'Could not get pdf current invoice.');
            }
        }
    }

    /**
     * Gets pdf of invoice
     * @param Handle $invoice_handle handle to invoice
     */
    function invoice_get_pdf($invoice_handle){
        if($this->is_connected()){
            try {
                return $this->client->Invoice_GetPdf(array('invoiceHandle' => $invoice_handle))->Invoice_GetPdfResult;
            } catch (Exception $e) {
                $this->exception_handler($e, 'Could not get pdf invoice.');
            }
        }
    }

    function inovice_set_date($invoice_handle, $date){
        if($this->is_connected()){
            try {
                return $this->client->CurrentInvoice_SetDate(array('currentInvoiceHandle' => $invoice_handle, 'value' => $date));
            } catch (Exception $e) {
                $this->exception_handler($e, 'Could not set date of invoice.');
            }
        }
    }

	function debtor_get_all(){
        if($this->is_connected()){
            try {
                return $this->client->Debtor_GetAll()->Debtor_GetAllResult->DebtorHandle;
            } catch (Exception $e) {
                $this->exception_handler($e, 'Could not get all debtors.');
            }
        }
	}


    function debtor_get_number($debtor_handle){
        if($this->is_connected()){
            try {
                return $this->client->Debtor_GetNumber(array('debtorHandle'=>$debtor_handle))->Debtor_GetNumberResult;
            } catch (Exception $e) {
                $this->exception_handler($e, 'Could not get debtor number debtor.');
            }
        }
    }

	function debtor_get_debtor_group($debtor_handle){
        if($this->is_connected()){
            try {
                return $this->client->Debtor_GetDebtorGroup(array('debtorHandle'=>$debtor_handle))->Debtor_GetDebtorGroupResult;
            } catch (Exception $e) {
                $this->exception_handler($e, 'Could not get debtor group of debtor.');
            }
        }
	}
	
	
	function debtor_get_data_array_result($debtor_handles){
        if($this->is_connected()){
            try {
                return $this->client->Debtor_GetDataArray(array('entityHandles'=>$debtor_handles))->Debtor_GetDataArrayResult->DebtorData;
            } catch (Exception $e) {
                $this->exception_handler($e, 'Could not get data array result of debtor handles.');
            }
        }
	}
	
	
	/**
	 * Finds debtor by debtor number. If the debtor number doesn't exists null is returned
	 * @param Integer $debtor_number Debtor number
	 */
	function debtor_get_by_number($debtor_number){
        if($this->is_connected()){
            try {
                $debtor_handle = $this->client->Debtor_FindByNumber(array('number' => $debtor_number));
                if(isset($debtor_handle->Debtor_FindByNumberResult)){
                    return $debtor_handle->Debtor_FindByNumberResult;
                }else{
                    return false;
                }
            } catch (Exception $e) {
                $this->exception_handler($e, 'Could not get debtor.');
            }
        }
		
	}

	/**
	 * Finds debtor by email. If no debtor with email exists, null is returned.
	 * @param String $email email to search for
	 */
	function debtor_get_by_email($email){
        if($this->is_connected()){
            $result = $this->client->Debtor_FindByEmail(array('email' => $email))->Debtor_FindByEmailResult;
            if(isset($result->DebtorHandle)){
                return $result->DebtorHandle;
            }
            return false;
        }

	}

    /**
     * Finds debtor by CI number. If no debtor with CI number exists, null is returned.
     * @param String $email email to search for
     */
    function debtor_get_by_CI_number($CI_name){
        if($this->is_connected()){
            $debtor_handle = $this->client->Debtor_FindByCINumber(array('ciNumber' => $CI_name))->Debtor_FindByCINumberResult->DebtorHandle;
            return $debtor_handle;
        }
    }

    /**
     * Finds debtor by telephone number. If no debtor with telephone number exists, null is returned.
     * @param String $email email to search for
     */
    function debtor_get_by_telephone_number($telephone){
        if($this->is_connected()){
            $debtor_handle = $this->client->Debtor_FindByTelephoneAndFaxNumber(array('telephoneAndFaxNumber' => $telephone))->Debtor_FindByTelephoneAndFaxNumberResult->DebtorHandle;
            return $debtor_handle;
        }
    }

    function debtor_get_data($handle){
        if($this->is_connected()){
            try {
                $debtor_data = $this->client->Debtor_GetData(array('entityHandle' => $handle))->Debtor_GetDataResult;
                return $debtor_data;
            } catch (Exception $e) {
                $this->exception_handler($e, 'Could not get debtor array.');
            }
        }
    }

	function debtor_get_data_array($entityHandles){
        if($this->is_connected()){
            try {
                $debtor_data = $this->client->Debtor_GetDataArray(array('entityHandles' => $entityHandles))->Debtor_GetDataArrayResult->DebtorData;
                return $debtor_data;
            } catch (Exception $e) {
                $this->exception_handler($e, 'Could not get debtor by data array.');
            }
        }

	}
	
	function debtor_get_next_available_number(){
        if($this->is_connected()){
            return $this->client->Debtor_GetNextAvailableNumber()->Debtor_GetNextAvailableNumberResult;
        }
	}
	
	/**
	 * Creates new debtor and returns it's handle. If a debtor with the same debtor number already exists
	 * false is returned
	 * @param Integer $debtor_number debtor number
	 * @param Integer $debtor_group_number debtor group number
	 * @param String $debtor_name name of debtor
	 * @param String $vat_zone vat zone belonging to debtor i.e. EU, HomeCountry, Abroad
	 */
	function debtor_create_new($debtor_number, $debtor_group_handle, $debtor_name, $vat_zone){
        if($this->is_connected()){
            try {
                if (!is_object($this->debtor_get_by_number($debtor_number))) {
                    $debtor_handle = $this->client->Debtor_Create(array('number' => $debtor_number,
                        'debtorGroupHandle'=> $debtor_group_handle,
                        'name' => $debtor_name,
                        'vatZone' => $vat_zone));
                    return $debtor_handle->Debtor_CreateResult;
                }else{
                    return false;
                }
            } catch (Exception $e) {
                $this->exception_handler($e, 'Could not create new debtor.');
            }
        }
	}

    function debtor_createFromData($data){
        if($this->is_connected()){
            try {
                $debtor_handle = $this->client->Debtor_CreateFromData(array('data' => $data));
                return $debtor_handle->Debtor_CreateResult;
            } catch (Exception $e) {
                $this->exception_handler($e, 'Could not create debtor from data', $data);
            }
        }
    }

    function debtor_updateFromData($data){
        if($this->is_connected()){
            try {
                $debtor_handle = $this->client->Debtor_UpdateFromData(array('data' => $data));
                return $debtor_handle->Debtor_UpdateFromDataResult;
            } catch (Exception $e) {
                $this->exception_handler($e, 'Could not update debtor from data', $data);
            }
        }
    }

	/**
	 * Sets address of debtor
	 * @param Handle $debtor_handle Handle of debtor
	 * @param String $address Address of debtor
	 */
	function debtor_set_address($debtor_handle, $address){
        if($this->is_connected()){
            try {
                $this->client->Debtor_SetAddress(array('debtorHandle'=>$debtor_handle,
                    'value'=>$address));
            } catch (Exception $e) {
                $this->exception_handler($e, 'Could not set address of debtor.');
            }
        }
	}

	/**
	 * Sets name of debtor
	 * @param Handle $debtor_handle Handle of debtor
	 * @param String $name Name of debtor
	 */
	function debtor_set_name($debtor_handle, $name){
        if($this->is_connected()){
            try {
                $this->client->Debtor_SetName(array('debtorHandle'=>$debtor_handle,
                    'value'=>$name));
            } catch (Exception $e) {
                $this->exception_handler($e, 'Could not set name of debtor.');
            }
        }
	}
	
	/**
	 * Sets city of debtor
	 * @param Handle $debtor_handle Debtor handle
	 * @param String $city City
	 */
	function debtor_set_city($debtor_handle, $city){
        if($this->is_connected()){
            try {
                $this->client->Debtor_SetCity(array('debtorHandle'=>$debtor_handle,
                    'value'=>$city));
            } catch (Exception $e) {
                $this->exception_handler($e, 'Could not set the city of debtor.');
            }
        }
	}
	
	/**
	 * Sets country of debtor
	 * @param Handle $debtor_handle Debtor handle 
	 * @param String $country Country
	 */
	function debtor_set_country($debtor_handle, $country){
        if($this->is_connected()){
            try {
                $this->client->Debtor_SetCountry(array('debtorHandle'=>$debtor_handle,
                    'value'=>$country));
            } catch (Exception $e) {
                $this->exception_handler($e, 'Could not set the country of debtor.');
            }
        }
	}
	
	/**
	 * Sets email address of debtor
	 * @param Handle $debtor_handle Debtor handle
	 * @param String $email email address
	 */
	function debtor_set_email($debtor_handle, $email){
        if($this->is_connected()){
            try {
                $this->client->Debtor_SetEmail(array('debtorHandle'=>$debtor_handle,
                    'value'=>$email));
            } catch (Exception $e) {
                $this->exception_handler($e, 'Could not set the email of debtor.');
            }
        }
	}
	
	/**
	 * Sets postal code of debtor
	 * @param Handle $debtor_handle Handle of debitor
	 * @param Integer $postal_code Postal code
	 */
	function debtor_set_postal_code($debtor_handle, $postal_code){
        if($this->is_connected()){
            try {
                $this->client->Debtor_SetPostalCode(array('debtorHandle'=>$debtor_handle,
                    'value'=>$postal_code));
            } catch (Exception $e) {
                $this->exception_handler($e, 'Could not set the postal code of debtor.');
            }
        }
	}
	
	
	/**
	 * Sets phone number of debtor
	 * @param Handle $debtor_handle Handle of debtor
	 * @param Integer $number Phone number
	 */ 
	function debtor_set_phone_number($debtor_handle, $number){
        if($this->is_connected()){
            try {
                $this->client->Debtor_SetTelephoneAndFaxNumber(array('debtorHandle'=>$debtor_handle,
                    'value'=>$number));
            } catch (Exception $e) {
                $this->exception_handler($e, 'Could not set the phone number code of debtor.');
            }
        }
	}
	
	
	/**
	 * Activates og deactivates debtor
	 * @param Handle $debtor_handle Handle of debtor
	 * @param Boolean $value Boolean value. True = debtor is accessible, false = debtor is inaccessible
	 */
	function debtor_set_accessible($debtor_handle, $value){
        if($this->is_connected()){
            try {
                $this->client->Debtor_SetIsAccessible (array('debtorHandle'=>$debtor_handle,
                    'value'=>$value));
            } catch (Exception $e) {
                $this->exception_handler($e, 'Could not set accessible flag of debtor.');
            }
        }
	}

	
	function debtor_set_currency($debtor_handle, $value){
        if($this->is_connected()){
            try {
                $this->client->Debtor_SetCurrency (array('debtorHandle'=>$debtor_handle,
                    'valueHandle'=>$value));
            } catch (Exception $e) {
                $this->exception_handler($e, 'Could not set currency of debtor.');
            }
        }
	}
	
	function debtor_set_debtor_group($debtor_handle, $debtor_group_handle){
        if($this->is_connected()){
            try {
                $this->client->Debtor_SetDebtorGroup (array('debtorHandle'=>$debtor_handle,
                    'valueHandle'=>$debtor_group_handle));
            } catch (Exception $e) {
                $this->exception_handler($e, 'Could not set debtor group of debtor.');
            }
        }
	}
	

	function debtor_set_CI_number($debtor_handle, $number){
        if($this->is_connected()){
            try {
                $this->client->Debtor_SetCINumber (array('debtorHandle'=>$debtor_handle,
                    'value'=>$number));
            } catch (Exception $e) {
                $this->exception_handler($e, 'Could not set CI number of debtor.');
            }
        }
	}

    function debtor_get_debtorContacts($debtor_handle){
        if($this->is_connected()){
            try {
                $debtorContactHandles = $this->client->Debtor_GetDebtorContacts (array('debtorHandle'=>$debtor_handle))->Debtor_GetDebtorContactsResult;

                if(!$debtorContactHandles){
                    return false;
                }

                return $this->client->DebtorContact_GetDataArray (array('entityHandles'=>$debtorContactHandles))->DebtorContact_GetDataArrayResult->DebtorContactData;

            } catch (Exception $e) {
                $this->exception_handler($e, 'Could not get debtor contacts of debtor.');
            }
        }
    }


    function debtor_contact_find_by_name($debtor_handle, $name){
        try {
            $result = $this->client->DebtorContact_FindByName(array('name'=>$name))->DebtorContact_FindByNameResult;
            if(isset($result->DebtorContactHandle)){
                $debtor_contacts = $result->DebtorContactHandle;
                foreach ($debtor_contacts as $debtor_contact) {
                    if($this->client->DebtorContact_GetDebtor(array('debtorContactHandle'=>$debtor_contact))->DebtorContact_GetDebtorResult->Number==$debtor_handle->Number){
                        return $debtor_contact;
                    }
                }
            }

            return false;
        } catch (Exception $e) {
            $this->exception_handler($e, 'Could not create contact for debtor.');
        }
    }

    function debtor_contact_create($debtor_handle, $contact_name){
        try {
            return $this->client->DebtorContact_Create (array('debtorHandle' => $debtor_handle,
                                                              'name'         => $contact_name))->DebtorContact_CreateResult;
        } catch (Exception $e) {
            $this->exception_handler($e, 'Could not create contact for debtor.');
        }
    }

    function debtor_contact_set_email($debtor_contact_handle, $email){
        try {
            return $this->client->DebtorContact_SetEmail (array('debtorContactHandle' => $debtor_contact_handle,
                                                                'value'               => $email));
        } catch (Exception $e) {
            $this->exception_handler($e, 'Could not set email of debtor contact.');
        }
    }

    function debtor_contact_set_telephone($debtor_contact_handle, $phone){
        try {
            return $this->client->DebtorContact_SetTelephoneNumber (array('debtorContactHandle' => $debtor_contact_handle,
                                                                          'value'               => $phone));
        } catch (Exception $e) {
            $this->exception_handler($e, 'Could not set telephone of debtor contact.');
        }
    }


    function deliveryLocation_create($debtorHandle){
        try {
            return $this->client->DeliveryLocation_Create (array('debtorHandle' => $debtorHandle))->DeliveryLocation_CreateResult;
        } catch (Exception $e) {
            $this->exception_handler($e, 'Could not create delivery location.');
        }
    }

    function deliveryLocation_delete($handle){
        try {
            return $this->client->DeliveryLocation_Delete (array('deliveryLocationHandle' => $handle));
        } catch (Exception $e) {
            $this->exception_handler($e, 'Could not delete delivery location.');
        }
    }

    function deliveryLocation_findByExternalId($externalId){
        try {
            $tmp = $this->client->DeliveryLocation_FindByExternalId (array('externalId' => $externalId));
            if(isset($tmp->DeliveryLocation_FindByExternalIdResult)){
                //Returning the handle for the delivery location
                return $tmp->DeliveryLocation_FindByExternalIdResult;
            }
            //Could not find the delivery location by external id, returning false
            return false;

        } catch (Exception $e) {
            $this->exception_handler($e, 'Could not find delivery location by external id.');
        }
    }

    function deliveryLocation_getData($handle){
        try {
            return $this->client->DeliveryLocation_GetData (array('entityHandle' => $handle))->DeliveryLocation_GetDataResult;
        } catch (Exception $e) {
            $this->exception_handler($e, 'Could not get data of delivery lcoation.');
        }
    }

    function deliveryLocation_updateFromData($data){
        try {
            $this->client->DeliveryLocation_UpdateFromData (array('data' => $data));
            return true;
        } catch (Exception $e) {
            $this->exception_handler($e, 'Could not update delivery location from data.');
        }
    }

	/**
	 * Returns all product groups as handles
	 */
	function product_groups_get_all(){
        if($this->is_connected()){
            try {
                $res = array();
                $product_group_handles = $this->client->ProductGroup_GetAll()->ProductGroup_GetAllResult->ProductGroupHandle;
                if(count($product_group_handles)>1){
                    foreach ($product_group_handles as $product_group_handle) {
                        $res[$product_group_handle->Number] = $this->client->ProductGroup_GetName(array('productGroupHandle' => $product_group_handle))->ProductGroup_GetNameResult;
                    }
                }else{
                    $product_group_handle = $product_group_handles;
                    $res[$product_group_handle->Number] = $this->client->ProductGroup_GetName(array('productGroupHandle' => $product_group_handle))->ProductGroup_GetNameResult;
                }
                return $res;
            } catch (Exception $e) {
                $this->exception_handler($e, 'Could not get all product groups.');
            }
        }
	}
	
	function product_group_get_name($product_group_handle){
        if($this->is_connected()){
            try {
                return $this->client->ProductGroup_GetName(array('productGroupHandle' => $product_group_handle))->ProductGroup_GetNameResult;
            } catch (Exception $e) {
                $this->exception_handler($e, 'Could not get product group name.');
            }
        }
	}
	
	function product_group_get_by_number($product_group_number){
        if($this->is_connected()){
            try {
                return $this->client->ProductGroup_FindByNumber(array('number' => $product_group_number))->ProductGroup_FindByNumberResult;
            } catch (Exception $e) {
                $this->exception_handler($e, 'Could not get product group by number.');
            }
        }
	}
	
	function product_group_find_by_name($product_group_name){
        if($this->is_connected()){
            try {
                $res = array();
                $product_group_handles = $this->client->ProductGroup_FindByName(array('name' => $product_group_name))->ProductGroup_FindByNameResult;
                foreach ($product_group_handles as $product_group_handle) {
                    $res[] = $product_group_handle; //$this->client->ProductGroup_GetName(array('productGroupHandle' => $product_group_handle))->ProductGroup_GetNameResult;
                }
                return $res;
            } catch (Exception $e) {
                $this->exception_handler($e, 'Could not get product group by name.');
            }
        }
	}
	
	function product_group_account_for_vat_liable_debtor_invoices_current($product_group_handle){
        if($this->is_connected()){
            try {
                return $this->client->ProductGroup_GetAccountForVatLiableDebtorInvoicesCurrent(array('productGroupHandle' => $product_group_handle))->ProductGroup_GetAccountForVatLiableDebtorInvoicesCurrentResult;
            } catch (Exception $e) {
                $this->exception_handler($e, 'Could not get product group account for vat liable debtor.');
            }
        }
	}

	function product_group_account_for_vat_exempt_debtor_invoices_current($product_group_handle){
        if($this->is_connected()){
            try {
                return $this->client->ProductGroup_GetAccountForVatExemptDebtorInvoicesCurrent (array('productGroupHandle' => $product_group_handle))->ProductGroup_GetAccountForVatExemptDebtorInvoicesCurrentResult;
            } catch (Exception $e) {
                $this->exception_handler($e, 'Could not get product group account for vat exempt debtor.');
            }
        }
	}
	

	function product_get_by_number($product_number){
        if($this->is_connected()){
            try {
                return $this->client->Product_FindByNumber(array('number'=>$product_number))->Product_FindByNumberResult;
            } catch (Exception $e) {
                $this->exception_handler($e, 'Could not get product ('.$product_number.') by number.');
            }
        }
	}

    function product_find_by_number_list($list){
        if($this->is_connected()){
            try {
                return $this->client->Product_FindByNumberList(array('numbers'=>$list))->Product_FindByNumberListResult;
            } catch (Exception $e) {
                $this->exception_handler($e, 'Could not find products by number list.');
            }
        }
    }

	function product_get_all(){
        if($this->is_connected()){
            try {
                return $this->client->Product_GetAll()->Product_GetAllResult->ProductHandle;
            } catch (Exception $e) {
                $this->exception_handler($e, 'Could not get all products.');
            }
        }
	}
	
	function product_get_data_array_result($product_handles){
        if($this->is_connected()){
            try {
                return $this->client->Product_GetDataArray(array('entityHandles'=>$product_handles))->Product_GetDataArrayResult->ProductData;
            } catch (Exception $e) {
                $this->exception_handler($e, 'Could not get data array result.');
            }
        }
	}

    function Product_GetInventoryLocationStatus($product_handle, $inventoryId = 0){
        if($this->is_connected()){
            try {
                $inventoryData = $this->client->Product_GetInventoryLocationStatus(array('productHandle' => $product_handle))->Product_GetInventoryLocationStatusResult;
                foreach($inventoryData->ProductInventoryLocationStatusData as $inventory){
                    if(property_exists($inventory,'InventoryLocationHandle')){
                        if($inventory->InventoryLocationHandle->Number == $inventoryId){
                            return $inventory;
                        }
                    }
                }
                return $inventoryData->ProductInventoryLocationStatusData[0];
            } catch (Exception $e) {
                $this->exception_handler($e, 'Could not get inventory location status.');
            }
        }
    }

	function product_get_name($product_handle){
        if($this->is_connected()){
            try {
                return $this->client->Product_GetName(array('productHandle' => $product_handle))->Product_GetNameResult;
            } catch (Exception $e) {
                $this->exception_handler($e, 'Could not get name of product.');
            }
        }
	}
	
	function product_get_data_array($product_handle){
        if($this->is_connected()){
            try {
                return $this->client->Product_GetDataArray(array('entityHandles' => array($product_handle)))->Product_GetDataArrayResult->ProductData;
            } catch (Exception $e) {
                $this->exception_handler($e, 'Could not get data array of product.');
            }
        }
	}
	
	function product_create_new($product_number, $product_group_handle, $name){
        if($this->is_connected()){
            try {
                if (!is_object($this->product_get_by_number($product_number))) {
                    return $this->client->Product_Create(array('number' => $product_number,
                        'productGroupHandle'=>$product_group_handle,
                        'name'=>$name))->Product_CreateResult;
                }else{
                    return false;
                }
            } catch (Exception $e) {
                $this->exception_handler($e, 'Could not create new product.');
            }
        }
	}
	
	function product_set_barcode($product_handle, $barcode){
        if($this->is_connected()){
            try {
                return $this->client->Product_SetBarCode(array('productHandle' => $product_handle,
                    'value' => $barcode));
            } catch (Exception $e) {
                $this->exception_handler($e, 'Could not set barcode of product.');
            }
        }
	}
	
	function product_set_cost_price($product_handle, $cost_price){
        if($this->is_connected()){
            try {
                return $this->client->Product_SetCostPrice(array('productHandle' => $product_handle,
                    'value' => $cost_price));
            } catch (Exception $e) {
                $this->exception_handler($e, 'Could not set cost price of product.');
            }
        }
	}

    function product_set_recommended_price($product_handle, $recommended_price){
        if($this->is_connected()){
            try {
                return $this->client->Product_SetRecommendedPrice(array('productHandle' => $product_handle,
                                                                        'value' => $recommended_price));
            } catch (Exception $e) {
                $this->exception_handler($e, 'Could not set recommended price of product.');
            }
        }
    }

    function product_set_description($product_handle, $description){
        if($this->is_connected()){
            try {
                return $this->client->Product_SetDescription(array('productHandle' => $product_handle,
                    'value' => $description));
            } catch (Exception $e) {
                $this->exception_handler($e, 'Could not set description of product.');
            }
        }
	}
	
	function product_set_accessible($product_handle, $value){
        if($this->is_connected()){
            try {
                return $this->client->Product_SetIsAccessible (array('productHandle' => $product_handle,
                    'value' => $value));
            } catch (Exception $e) {
                $this->exception_handler($e, 'Could not set accessible of product.');
            }
        }
	}
	
	function product_set_name($product_handle, $name){
        if($this->is_connected()){
            try {
                return $this->client->Product_SetName (array('productHandle' => $product_handle,
                    'value' => $name));
            } catch (Exception $e) {
                $this->exception_handler($e, 'Could not set name of product.');
            }
        }
	}	
	
	
	function product_set_product_group($product_handle, $product_group_handle){
        if($this->is_connected()){
            try {
                return $this->client->Product_SetProductGroup  (array('productHandle' => $product_handle,
                    'valueHandle' => $product_group_handle));
            } catch (Exception $e) {
                $this->exception_handler($e, 'Could not set product group of product.');
            }
        }
	}
	
	function product_set_sales_price($product_handle, $sales_price){
        if($this->is_connected()){
            try {
                return $this->client->Product_SetSalesPrice  (array('productHandle' => $product_handle,
                    'value' => $sales_price));
            } catch (Exception $e) {
                $this->exception_handler($e, 'Could not set sales price of product.');
            }
        }
	}
	
	function product_set_unit($product_handle, $unit_handle){
        if($this->is_connected()){
            try {
                return $this->client->Product_SetUnit(array('productHandle' => $product_handle,
                    'valueHandle' => $unit_handle));
            } catch (Exception $e) {
                $this->exception_handler($e, 'Could not set unit of product.');
            }
        }
	}
	
	/**
	 * 
	 * Dete product from e-conomic. Product is deleted only if it is possible to delete it. 
	 * @param Product handle $product_handle Handle of product to delete
	 */
	function product_delete($product_handle){
        if($this->is_connected()){
            try {
                return $this->client->Product_Delete(array('productHandle' => $product_handle));
            } catch (Exception $e) {
                $this->exception_handler($e, 'Could not delete product.');
            }
        }
	}
	
	function vat_accounts_get_all(){
        if($this->is_connected()){
            try {
                return $this->client->VatAccount_GetAll()->VatAccount_GetAllResult->VatAccountHandle;
            } catch (Exception $e) {
                $this->exception_handler($e, 'Could not get all vat accounts.');
            }
        }
	}
	
	
	function unit_create_new($name){
        if($this->is_connected()){
            try {
                return $this->client->Unit_Create(array('name' => $name))->Unit_CreateResult;
            } catch (Exception $e) {
                $this->exception_handler($e, 'Could not create unit.');
            }
        }
	}
	
	/**
	 * 
	 * Gets all units present
	 */
	function unit_get_all(){
        if($this->is_connected()){
            try {
                return $this->client->Unit_GetAll()->Unit_GetAllResult->UnitHandle;
            } catch (Exception $e) {
                $this->exception_handler($e, 'Could not get all units.');
            }
        }
	} 
	
	/**
	 * 
	 * Gets name of unit
	 * @param $unit_handle Unit handle
	 */
	function unit_get_name($unit_handle){
        if($this->is_connected()){
            try {
                return $this->client->Unit_GetName(array('unitHandle' => $unit_handle))->Unit_GetNameResult;
            } catch (Exception $e) {
                $this->exception_handler($e, 'Could not get name of unit.');
            }
        }
	}
	
	/**
	 * 
	 * Finds all units with name
	 * @param string $name name of unit 
	 */
	function unit_get_by_name($name){
        if($this->is_connected()){
            try {
                return $this->client->Unit_FindByName(array('name' => $name))->Unit_FindByNameResult->UnitHandle;
            } catch (Exception $e) {
                $this->exception_handler($e, 'Could not find unit by name.');
            }
        }
	}
	
	
	function currency_find_by_code($currency_code){
        if($this->is_connected()){
            try {
                return $this->client->Currency_FindByCode(array('code' => $currency_code))->Currency_FindByCodeResult;
            } catch (Exception $e) {
                $this->exception_handler($e, 'Could not get currency code by code.');
            }
        }
	}
	
	
	/**
	*
	* Gets all term of payments
	*/
	function term_of_payment_get_all(){
        if($this->is_connected()){
            try {
                return $this->client->TermOfPayment_GetAll()->TermOfPayment_GetAllResult->TermOfPaymentHandle;
            } catch (Exception $e) {
                $this->exception_handler($e, 'Could not get all term of payments.');
            }
        }
	}
	
	/**
	 * Gets term of payment data for all term of payment handles 
	 * Enter description here ...
	 * @param unknown_type $term_of_payment_handles
	 */
	function term_of_payment_get_data_array_result($term_of_payment_handles){
        if($this->is_connected()){
            try {
                return $this->client->TermOfPayment_GetDataArray(array('entityHandles'=>$term_of_payment_handles))->TermOfPayment_GetDataArrayResult->TermOfPaymentData;
            } catch (Exception $e) {
                $this->exception_handler($e, 'Could not get term of payment array result.');
            }
        }
	}

    /**
     * Gets all department handles
     */
    function department_get_all(){
        if($this->is_connected()){
            try {
                $res = array();
                $department_handles = $this->client->Department_GetAll()->Department_GetAllResult->DepartmentHandle;
                if(count($department_handles)>1){
                    foreach ($department_handles as $department_handle) {
                        $res[$department_handle->Number] = $this->client->Department_GetName(array('departmentHandle' => $department_handle))->Department_GetNameResult;
                    }
                }else{
                    $department_handle = $department_handles;
                    $res[$department_handle->Number] = $this->client->Department_GetName(array('departmentHandle' => $department_handle))->Department_GetNameResult;
                }
                return $res;
            } catch (Exception $e) {
                $this->exception_handler($e, 'Could not get all departments.');
            }
        }
    }


	/**
	 * Tests that the chosen configuration exist
	 * i.e. that debtor group, product group etc. exists.
	 */
	function test_config(){
		
	}
	
	function is_connected(){
        return $this->connected;
    }

	
	/**
	 * **********************************************************************************************
	 * 									PRIVATE FUNCTIONS BELOW										*		
	 * ********************************************************************************************** 
	 */
	
	private function exception_handler($exception, $msg, $data = null){
        mage::log($msg, null, 'e-conomic2.log');
		mage::log($exception->getMessage(), null, 'e-conomic2.log');
        if($data){
            mage::log($data, null, 'e-conomic2.log');
        }
	}
	
}


?>