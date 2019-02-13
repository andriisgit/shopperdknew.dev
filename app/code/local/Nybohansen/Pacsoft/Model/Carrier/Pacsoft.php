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

class Nybohansen_Pacsoft_Model_Carrier_Pacsoft extends Mage_Shipping_Model_Carrier_Abstract implements Mage_Shipping_Model_Carrier_Interface
{
    /**
     * unique internal shipping method identifier
     *
     * @var string [a-z0-9_]
     */
    protected $_code = 'pacsoft';
    protected $_canFindMatchingRates = true;
    const PACSOFT_FREE_SHIPPING_RULE = 'pacsoft_free_shipping_rule';

    public function collectRates(Mage_Shipping_Model_Rate_Request $request){

        //Check if module is active
        if (!$this->getConfigData('active')) {
            return false;
        }

        if(!$this->_canFindMatchingRates){
            return false;
        }


        $applicableRates = $this->getApplicableRates($request);
        $result = Mage::getModel('shipping/rate_result');

        foreach($applicableRates as $rate){

            $method = Mage::getModel('shipping/rate_result_method');
            /** @var method Mage_Shipping_Model_Rate_Result_Method */

            $method->setCarrier($this->_code);
            $method->setMethod($this->_code.'_'.$rate->getId());
            $method->setCarrierTitle($this->getConfigData('title'));
            $method->setMethodTitle($rate->getTitle());

            $shippingPrice = $this->getShippingPrice($request, $rate);

            $method->setPrice($shippingPrice);
            $method->setCost($shippingPrice);

            $result->append($method);
        }

        return $result;
    }

    public function getAllowedMethods(){
        $allTableRates = $this->getTableRates();

        $matchingRates = array();

        foreach ($allTableRates as $rate){
            $matchingRates[$this->_code.'_'.$rate->getId()] = $rate->getTitle();
        }
        return $matchingRates;
    }


    public function getFormBlock(){
        return 'pacsoft/pacsoft';
    }

    public function sortRatesByPrice(){

    }

    private function getApplicableRates(Mage_Shipping_Model_Rate_Request $request){
        /** @var $request Mage_Shipping_Model_Rate_Request */

        //Check if module is applicable in website, country, region, city and zip
        $matchingRates = $this->getMatchingRates($request);
        $items = $request->getAllItems();
        $candidates = array();

        foreach ($matchingRates as $rate){
            switch ($rate->getFunction()) {
                case 'number':
                    //Count the number of items
                    $numberOfItems = 0;
                    foreach ($items as $item) {
                        //Multiply with quantity, and remove virtual products
                        if ($item->getProduct()->isVirtual() || $item->getParentItem()) {
                            continue;
                        }
                        $numberOfItems += $item->getQty();
                    }
                    if($rate->conditionCheck($numberOfItems)){
                        $candidates[] = $rate;
                    }
                    break;
                case 'weight':
                    //Sum the weight
                    $weight = 0;
                    foreach ($items as $item) {
                        //Multiply with quantity, and remove virtual products
                        if ($item->getProduct()->isVirtual() || $item->getParentItem()) {
                            continue;
                        }
                        $weight += $item->getWeight()*$item->getQty();
                    }
                    if($rate->conditionCheck($weight)){
                        $candidates[] = $rate;
                    }
                    break;
                case 'price':
                    //Subtotal
                    if($rate->conditionCheck(max(0,$request->getPackageValueWithDiscount()))){
                        $candidates[] = $rate;
                    }
                    break;
            }
        }

        if(count($candidates) == 0){
            $this->_canFindMatchingRates = false;
        }

        return $candidates;
    }



    /**
     *
     * Returns all rules that match website, country, region, city and zip
     * @param Mage_Shipping_Model_Rate_Request $request
     */
    private function getMatchingRates(Mage_Shipping_Model_Rate_Request $request){
        /** @var $request Mage_Shipping_Model_Rate_Request */

        $allTableRates = $this->getTableRates();

        $matchingRates = array();
        foreach ($allTableRates as $line){
            if($line->getStatus()){
                if(in_array($request->getStoreId(),$line->getStoresAsArray()) || in_array(0, $line->getStoresAsArray())){
                    if(in_array($request->getDestCountryId(),explode(',',$line->getCountry())) || $line->getCountry() == '*' ){
                        if($line->getRegion() == $request->getDestRegionCode() || $line->getRegion() == '*' ){
                            if($line->getCity() == $request->getDestCity() || $line->getCity() == '*' ){
                                if($line->rateApplicableInZip($request->getDestPostcode())){
                                    $matchingRates[] = $line;
                                }
                            }
                        }
                    }
                }
            }
        }

        return $matchingRates;
    }



    /**
     *
     * Extracts the tablererates from the configuration and returns an array of tablerates objects
     */
    private function getTableRates(){
        $tableRates = Mage::getModel('pacsoft/rates')->getCollection()->setOrder('sort_order', 'ASC');
        return $tableRates;
    }

    public function getTrackingInfo($barcode)
    {
        $obj = new Varien_Object();
        $obj->setData('tracking',$barcode);
        $obj->setData('url','http://www.postdanmark.dk/tracktrace/TrackTrace.do?i_stregkode='.$barcode);
        return $obj;
    }

    public function isTrackingAvailable()
    {
        return true;
    }

    private function getShippingPrice(Mage_Shipping_Model_Rate_Request $request, $rate){
        $freeBoxes = 0;

        $appliedRuleIds = array();

        if ($request->getAllItems()) {
            foreach ($request->getAllItems() as $item) {

                foreach(explode(',', $item->getAppliedRuleIds()) as $appliedRuleId){
                    //This is not an error, just a way to make the array associative and unique..
                    $appliedRuleIds[$appliedRuleId] = $appliedRuleId;
                }

                if ($item->getProduct()->isVirtual() || $item->getParentItem()) {
                    continue;
                }

                if ($item->getHasChildren() && $item->isShipSeparately()) {
                    foreach ($item->getChildren() as $child) {
                        if ($child->getFreeShipping() && !$child->getProduct()->isVirtual()) {
                            $freeBoxes += $item->getQty() * $child->getQty();
                        }
                    }
                } elseif ($item->getFreeShipping()) {
                    $freeBoxes += $item->getQty();
                }
            }
        }
        $this->setFreeBoxes($freeBoxes);

        $shippingPrice = $this->getFinalPriceWithHandlingFee($rate->getPrice());

        $shippingPrice = $this->getRulePrice($appliedRuleIds, $rate, $shippingPrice);

        if ($request->getFreeShipping() || ($request->getPackageQty() == $this->getFreeBoxes())){
            $shippingPrice = '0.00';
        }

        return $shippingPrice;
    }


    //Returns price if a shipping cart rule containing the pacsoft rules has been used
    private function getRulePrice($appliedRuleIds, $rate, $initialPrice){

        foreach($appliedRuleIds as $appliedRuleId){
            $salesRule = Mage::getModel('salesrule/rule')->load($appliedRuleId);
            if($salesRule->getSimpleAction() == self::PACSOFT_FREE_SHIPPING_RULE) {
                $pacsoftSalesRuleModel = Mage::getModel('pacsoft/salesRule');
                $pacsoftSalesRuleModel->load($appliedRuleId, 'rule_id');

                $applicableToRates = array();
                foreach(unserialize($pacsoftSalesRuleModel->getShippingMethods()) as $r){
                    $applicableToRates[$r] = $r;
                }

                if($pacsoftSalesRuleModel && isset($applicableToRates[$this->_code.'_'.$rate->getRateId()])){
                    $type = $pacsoftSalesRuleModel->getShippingAmountType();
                    $amount = $pacsoftSalesRuleModel->getShippingAmount();
                    switch($type){
                        case 'P':
                            $initialPrice = $initialPrice*($amount/100);
                            break;
                        case 'A':
                            $initialPrice = $initialPrice+$amount;
                            break;
                    }
                }
            }
        }

        return $initialPrice;

    }

}