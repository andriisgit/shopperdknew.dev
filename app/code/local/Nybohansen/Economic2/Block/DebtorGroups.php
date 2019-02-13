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

class Nybohansen_Economic2_Block_DebtorGroups extends Mage_Core_Block_Html_Select{
    
    /**
     * Economic debtor groups cache
     *
     * @var array
     */
    private $_debtorGroups;


    /**
     * 
     * Fetches all customer groups and returns array
     */
	protected function _getEconomicDebtorGroups(){

        if(is_null($this->_debtorGroups)){

            $debtor_group = new Nybohansen_Economic2_Model_System_Config_Source_DebtorGroupList();
            $allGroups  = $debtor_group->toOptionArray();
            foreach($allGroups as $group){
                $this->_debtorGroups[$group['value']]= htmlspecialchars($group['label'], ENT_NOQUOTES | ENT_QUOTES);
            }
    	}
        return $this->_debtorGroups;
    }
    
	public function setInputName($value){
        return $this->setName($value);
    }
    
    
    /**
     * Render block HTML
     *
     * @return string
     */
    public function _toHtml()
    {
    	if (!$this->getOptions()) {
            foreach ($this->_getEconomicDebtorGroups() as $key => $label) {
                $this->addOption($key, $label);
            }
        }
        
        return parent::_toHtml();
    }
}
