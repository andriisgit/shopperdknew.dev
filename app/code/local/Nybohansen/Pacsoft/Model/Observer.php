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
 * @package    Nybohansen_Pacsoft
 * @copyright  Copyright (c) 2014 Nybohansen ApS
 * @license    LICENSE.txt
 */

class Nybohansen_Pacsoft_Model_Observer extends Varien_Object
{

    const PACSOFT_FREE_SHIPPING_RULE = 'pacsoft_free_shipping_rule';

    public function salesQuoteSaveAfter($evt){
        $checkout = Mage::getSingleton('checkout/session');
        $quote = $evt->getQuote();

        $PacsoftShippingData = $checkout->getPacsoftShippingData();
        $addressId = $quote->getShippingAddress()->getAddressId();
        if(isset($PacsoftShippingData[$addressId])){
            //The shipping method used is Pacsoft
            $quote->getShippingAddress()->setShippingDescription($PacsoftShippingData[$addressId]['pacsoft_shipping_description']);
        }
    }

    public function saveShippingMethod($evt){
        $shipping_code_regexp = '/^pacsoft_pacsoft/';

        $request = $evt->getRequest();
        $quote = $evt->getQuote();

        //Methods
        $methods = $request->getPost('shipping_method');

        //Get all addresses
        $addresses = $quote->getAllShippingAddresses();

        foreach ($addresses as $address) {

            $addressId = $address->getId();

            if(is_array($methods) && isset($methods[$addressId])){
                $method = $methods[$addressId];
            }else{
                $method = $methods;
            }

            if(preg_match($shipping_code_regexp, $method)){

                //We known that the Pacsoft module has been used - fetch which of the rates that have been chosen
                $explodedRate = explode('_', $method);
                //Rate id of the rate chosen
                $rateId = end($explodedRate);

                $pacsoftPostData = $request->getParam('pacsoft',false);
                $pacsoftArray = $pacsoftPostData[$addressId.'_'.$rateId];

                $rate = Mage::getModel('pacsoft/rates')->load($rateId);

                if($rate->servicePointAddonActive()){

                    //Servicepoint addon chosen
                    $servicePointId = $pacsoftArray['parcelshopId'];


                    /** @var $webservice Nybohansen_Pacsoft_Helper_ServicePointsWebservice */
                    $webservice = Mage::helper('pacsoft/servicePointsWebservice');
                    $shopInfo = $webservice->getServicePoint($servicePointId, $address->getCountryId());
                    $pacsoftArray['servicePointInfo'] = $shopInfo;

                    $desc  = Mage::getStoreConfig('carriers/pacsoft/title', $this->getStore()).'<br/>';
                    $desc .= $rate->getTitle().'<br/>';
                    $desc .= $shopInfo->name.'<br/>';
                    $desc .= $shopInfo->visitingAddress->streetName.' '.$shopInfo->visitingAddress->streetNumber.'<br/>';
                    $desc .= $shopInfo->visitingAddress->postalCode.' '.$shopInfo->visitingAddress->city.'<br/>';


                    $PacsoftShippingData[$addressId]['pacsoft_parcel_shop_info'] = $shopInfo;

                }else{
                    $desc   = Mage::getStoreConfig('carriers/pacsoft/title', $this->getStore()).'<br/>';
                    $desc  .= $rate->getTitle().'<br/>';
                    $desc  .= $pacsoftArray['deliveryNote'].'<br/>';
                }

                if(isset($pacsoftArray['deliveryNote'])){
                    $PacsoftShippingData[$addressId]['pacsoft_delivery_note'] = $pacsoftArray['deliveryNote'];
                }

                $PacsoftShippingData[$addressId]['pacsoft_rate_info'] = $rate;
                $PacsoftShippingData[$addressId]['pacsoft_shipping_description'] = $desc;

                $address->setShippingDescription($desc);
                $address->save();
                Mage::getSingleton('checkout/session')->setPacsoftShippingData($PacsoftShippingData);

            }else{
                Mage::getSingleton('checkout/session')->unsPacsoftShippingData();
            }
        }


    }


    public function convertQuoteAddress($evt){
        $address = $evt->getAddress();
        $order = $evt->getOrder();

        if($address && $order){
            $PacsoftShippingData = Mage::getSingleton('checkout/session')->getPacsoftShippingData();
            if(isset($PacsoftShippingData[$address->getId()])){
                //Overwrite the old key with increment_id since this is stored all the way to checkout
                $PacsoftShippingData[$order->getData('increment_id')] = $PacsoftShippingData[$address->getId()];
                unset($PacsoftShippingData[$address->getId()]);
                //Save the changes
                Mage::getSingleton('checkout/session')->setPacsoftShippingData($PacsoftShippingData);
            }

        }

    }

    public function saveOrderAfter($evt){
        $order = $evt->getOrder();

        $orderId = $order->getData('increment_id');
        $PacsoftShippingDataSession = Mage::getSingleton('checkout/session')->getPacsoftShippingData();

        if(isset($PacsoftShippingDataSession[$orderId])){
            $PacsoftShippingData = $PacsoftShippingDataSession[$orderId];
            //Pacsoft has been used as the shipping provider

            //Order info relation
            $relation = Mage::getModel('pacsoft/orderInfo');
            //Rate info
            $rate = $PacsoftShippingData['pacsoft_rate_info'];

            $deliveryNote = '';
            if(isset($PacsoftShippingData['pacsoft_delivery_note'])){
                //Insert delivery note, if present
                $deliveryNote = $PacsoftShippingData['pacsoft_delivery_note'];
            }

            if($rate->servicePointAddonActive()){
                //The shipping method uses parcel shops
                $shopInfo = $PacsoftShippingData['pacsoft_parcel_shop_info'];

                $relation->setData(array('order_id'                     => $orderId,
                                         'servicePointId'               => $shopInfo->servicePointId,
                                         'name'                         => $shopInfo->name,
                                         'visitingAddress_streetName'   => $shopInfo->visitingAddress->streetName,
                                         'visitingAddress_streetNumber' => $shopInfo->visitingAddress->streetNumber,
                                         'visitingAddress_postalCode'   => $shopInfo->visitingAddress->postalCode,
                                         'visitingAddress_city'         => $shopInfo->visitingAddress->city,
                                         'visitingAddress_countryCode'  => $shopInfo->visitingAddress->countryCode,
                                         'deliveryAddress_streetName'   => $shopInfo->deliveryAddress->streetName,
                                         'deliveryAddress_streetNumber' => $shopInfo->deliveryAddress->streetNumber,
                                         'deliveryAddress_postalCode'   => $shopInfo->deliveryAddress->postalCode,
                                         'deliveryAddress_city'         => $shopInfo->deliveryAddress->city,
                                         'deliveryAddress_countryCode'  => $shopInfo->deliveryAddress->countryCode,
                                         'longitude'                    => $shopInfo->coordinates[0]->easting,
                                         'latitude'                     => $shopInfo->coordinates[0]->northing,
                                         'shipment_type'                => $rate->getShipmentType(),
                                         'addons'                       => $rate->getAddonsAsStr(),
                                         'freetext1'                    => $deliveryNote,
                                         'contents'                     => Mage::getStoreConfig('carriers/pacsoft/default_content', $order->getStoreId())));

                if(Mage::getStoreConfig('carriers/pacsoft/replaceDeliveryAddress', $this->getStore())){
                    $order->getShippingAddress()->getId();
                    $shippingAddress = Mage::getModel('sales/order_address')->load($order->getShippingAddress()->getId());

                    $shippingAddress->setFirstname($shopInfo->name)
                                    ->setMiddlename("")
                                    ->setLastname("")
                                    ->setSuffix("")
                                    ->setCompany("")
                                    ->setStreet($shopInfo->deliveryAddress->streetName. " ".$shopInfo->deliveryAddress->streetNumber)
                                    ->setCity($shopInfo->deliveryAddress->city)
                                    ->setCountry_id("")
                                    ->setRegion("")
                                    ->setRegion_id("")
                                    ->setPostcode($shopInfo->deliveryAddress->postalCode)
                                    ->setTelephone()
                                    ->setFax($shopInfo->servicePointId)->save();

                }

            }else{
                $relation->setData(array('order_id'                     => $orderId,
                                         'shipment_type'                => $rate->getShipmentType(),
                                         'addons'                       => $rate->getAddonsAsStr(),
                                         'freetext1'                    => $deliveryNote,
                                         'contents'                     => Mage::getStoreConfig('carriers/pacsoft/default_content', $order->getStoreId())));
            }
            $relation->save();

            //Save shipping description to order
            $order->setShippingDescription($PacsoftShippingData['pacsoft_shipping_description']);



            //Remove the order from the $PacsoftShippingDataSession so we only add it once to the DB
            unset($PacsoftShippingDataSession[$orderId]);
            Mage::getSingleton('checkout/session')->setPacsoftShippingData($PacsoftShippingDataSession);
        }
    }


    //Adds the combobox entries that contains the mass actions
    public function addMassAction($observer)
    {
        $block = $observer->getEvent()->getBlock();
        if($block instanceof Mage_Adminhtml_Block_Widget_Grid_Massaction && strpos($block->getRequest()->getControllerName(),'sales_order') !== false){
            $block->addItem('pacsoft_create_based_on_shipping_method',
                            array('label' => Mage::helper('pacsoft')->__('Pacsoft: Create label based on shipping method'),
                                  'url'   => Mage::helper('adminhtml')->getUrl('adminhtml/sales_pacsoftOrder/massGenerateLabels', array('' => ''))
                            ));
            $block->addItem('pacsoft_create_private',
                            array('label' => Mage::helper('pacsoft')->__('Pacsoft: Create private label'),
                                'url'   => Mage::helper('adminhtml')->getUrl('adminhtml/sales_pacsoftOrder/massGeneratePrivateLabels', array('' => ''))

                            ));
            $block->addItem('pacsoft_create_business',
                            array('label' => Mage::helper('pacsoft')->__('Pacsoft: Create business label'),
                                'url'   => Mage::helper('adminhtml')->getUrl('adminhtml/sales_pacsoftOrder/massGenerateBusinessLabels', array('' => ''))

                            ));
            $block->addItem('pacsoft_create_return',
                            array('label' => Mage::helper('pacsoft')->__('Pacsoft: Create return label'),
                                'url'   => Mage::helper('adminhtml')->getUrl('adminhtml/sales_pacsoftOrder/massGenerateReturnLabels', array('' => ''))
                            ));
        }
    }

    public function coreBlockAbstractToHtmlAfter($observer)
    {
        /* @var $block Mage_Core_Block_Abstract */
        $block = $observer->getBlock();
        $template = $block->getTemplate();

        if(strpos($template, 'sales/order/view/info.phtml') !== false){
            $parentTemplate = $block->getParentBlock()->getTemplate();
            if($parentTemplate == 'sales/order/view/tab/info.phtml'){
                $transport = $observer->getTransport();
                $html = $transport->getHtml();
                $newBlock = Mage::getSingleton('core/layout')->createBlock('pacsoft/adminhtml_shippingInfoBox');
                $html = $html . $newBlock->toHtml();
                $transport->setHtml($html);
            }
        }

        if(strpos($template, 'shipping_method/available.phtml') || $template=='onestepcheckout/shipping_method.phtml' || $template == 'onestepcheckout/onestepcheckout/shipping_method.phtml' || $template == 'onestepcheckout/onestep/shipping.phtml' || $template == 'msp_flatshipping5/available.phtml' || $template == 'onestepcheckout/flat/onestepcheckout/shipping_method.phtml'){
            if(method_exists($block, 'getShippingRates')){
                foreach ($block->getShippingRates() as $code => $_rates){
                    if($code == Mage::getModel('pacsoft/carrier_pacsoft')->getCarrierCode()){
                        foreach ($_rates as $_rate){

                            $endingToken = '</label>';
                            $rateCode = $_rate->getCode();
                            $explodedRate = explode('_', $rateCode);
                            $rateId = end($explodedRate);

                            $rate = Mage::getModel('pacsoft/rates')->load($rateId);

                            if($rate->servicePointAddonActive() || $rate->showDeliveryNote()){
                                $html = $observer->getTransport()->getHtml();
                                if($pos1 = strpos($html, 'value="'.$rateCode.'"')){
                                    $pos1 += strlen($endingToken);
                                    if($pos2 = strpos($html, $endingToken, $pos1)){

                                        $carrier = Mage::getModel('shipping/config')->getCarrierInstance($code);
                                        $injectBlock = $block->getLayout()->createBlock($carrier->getFormBlock());

                                        //Store adress id so we can use it in the ajax call done in the block
                                        $injectBlock->setAddressId($block->getAddress()->getId());
                                        $injectBlock->setCountryCode($block->getAddress()->getCountryId());
                                        $injectBlock->setMethodCode($code);
                                        $injectBlock->setRate($_rate);
                                        $injectBlock->setMethodInstance($carrier);

                                        $htmlBefore = substr($html,0,$pos2+strlen($endingToken));
                                        $htmlAfter = substr($html,$pos2);
                                        $observer->getTransport()->setHtml($htmlBefore.$injectBlock->toHtml().$htmlAfter);
                                    }
                                }
                            }

                        }
                    }
                }
            }
        }

        $this->AddSalesRuleJavascript($observer);
    }

    /* Sales rule start */

    public function salesruleRuleSaveBefore(Varien_Event_Observer $observer){
        $rule = $observer->getEvent()->getRule();
        if($rule->getSimpleAction() == self::PACSOFT_FREE_SHIPPING_RULE){
            $rule->setDiscountAmount(0);
            $rule->setDiscountQty(0);
            $rule->setDiscountStep(0);
            $rule->setApplyToShipping(0);
            $rule->setSimpleFreeShipping(0);
        }
    }


    public function salesruleRuleSaveAfter(Varien_Event_Observer $observer){
        $rule = $observer->getEvent()->getRule();

        $model = Mage::getModel('pacsoft/salesRule');
        $ruleId = $rule['rule_id'];

        $model->load($ruleId, 'rule_id');

        if($model['rule_id'] != $ruleId){
            $model->setRuleId($ruleId);
        }

        $model->setShippingAmountType($rule['pacsoft_shipping_amount_type']);
        $model->setShippingAmount($rule['pacsoft_shipping_amount']);
        $model->setShippingMethods(serialize($rule['pacsoft_shipping_methods']));

        try {
            $model->save();
        }
        catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        }
    }

    //Add new action to the
    public function adminhtmlBlockSalesruleActionsPrepareform(Varien_Event_Observer $observer){

        $ruleModel = Mage::registry('current_promo_quote_rule');

        $pacsoftSalesRuleModel = Mage::getModel('pacsoft/salesRule');
        $pacsoftSalesRuleModel->load($ruleModel->getId(), 'rule_id');


        $ruleModel->setData('pacsoft_shipping_amount_type', $pacsoftSalesRuleModel['shipping_amount_type']);
        $ruleModel->setData('pacsoft_shipping_amount', $pacsoftSalesRuleModel['shipping_amount']);
        $ruleModel->setData('pacsoft_shipping_methods', unserialize($pacsoftSalesRuleModel['shipping_methods']));


        // Extract the form field
        $field = $observer->getForm()->getElement('simple_action');
        // Extract the field values
        $options = $field->getValues();
        // Add the new value
        $options[] = array(
            'value' => self::PACSOFT_FREE_SHIPPING_RULE,
            'label' => Mage::helper('pacsoft')->__('Post Danmark free shipping')
        );
        // Set the field
        $field->setValues($options);
        $field->setOnchange($field->getOnchange().'pacsoft_hide(); ');


        $fieldSet = $observer->getForm()->getElement('action_fieldset');

        $fieldSet->addField('pacsoft_shipping_amount_type', 'select', array(
                'name'     => 'pacsoft_shipping_amount_type',
                'label' => Mage::helper('pacsoft')->__('Type'),
                'values' => array(array('value'=> 'A','label'=>Mage::helper('pacsoft')->__('Amount')),
                                  array('value'=> 'P','label'=>Mage::helper('pacsoft')->__('Percentage')))
            ),
            'simple_action'
        );

        $fieldSet->addField('pacsoft_shipping_amount', 'text', array(
                'name'     => 'pacsoft_shipping_amount',
                'label' => Mage::helper('pacsoft')->__('Amount')
            ),
            'pacsoft_shipping_amount_type'
        );

        $pacsoftRates = array();
        foreach(Mage::getModel('pacsoft/carrier_pacsoft')->getAllowedMethods() as $k => $l){
            $pacsoftRates[] = array('value'=> $k,'label'=>$l);
        }

        $fieldSet->addField('pacsoft_shipping_methods', 'multiselect', array(
                'name'     => 'pacsoft_shipping_methods',
                'label' => Mage::helper('pacsoft')->__('Pacsoft shipping methods'),
                'values' => $pacsoftRates
            ),
            'pacsoft_shipping_amount'
        );
    }

    private function AddSalesRuleJavascript($observer){
        $block = $observer->getBlock();
        if($block['type'] == 'adminhtml/promo_quote_edit'){
            $jsBlock = $block->getLayout()->createBlock('core/text')->setText('
             <script type="text/javascript">
                function pacsoft_hide() {

                    var table = $(\'rule_simple_free_shipping\').up().up().up()
                    for (var i = 0; row = table.rows[i]; i++) {
                        row.hide();
                    }
                    $(\'rule_simple_action\').up().up().show();

                    if ($(\'rule_simple_action\').value == \'gls_free_shipping_rule\') {
                        $(\'rule_gls_shipping_amount_type\').up().up().show();
                        $(\'rule_gls_shipping_amount\').up().up().show();
                        $(\'rule_gls_shipping_methods\').up().up().show();
                        $(\'rule_actions_fieldset\').up().hide();
                    }else if ($(\'rule_simple_action\').value == \'pacsoft_free_shipping_rule\') {
                        $(\'rule_pacsoft_shipping_amount_type\').up().up().show();
                        $(\'rule_pacsoft_shipping_amount\').up().up().show();
                        $(\'rule_pacsoft_shipping_methods\').up().up().show();
                        $(\'rule_actions_fieldset\').up().hide();
                    }else{
                        $(\'rule_simple_free_shipping\').up().up().show();
                        $(\'rule_discount_qty\').up().up().show();
                        $(\'rule_apply_to_shipping\').up().up().show();
                        $(\'rule_actions_fieldset\').up().show();
                        $(\'rule_apply_to_shipping\').up().up().show();
                        $(\'rule_discount_amount\').up().up().show();
                        $(\'rule_discount_step\').up().up().show();
                        $(\'rule_actions_fieldset\').up().show();
                    }
                }
                pacsoft_hide();
             </script>');
            $observer->getTransport()->setHtml($observer->getTransport()->getHtml().$jsBlock->toHtml());
        }
    }

    /* Sales rule end */



}	