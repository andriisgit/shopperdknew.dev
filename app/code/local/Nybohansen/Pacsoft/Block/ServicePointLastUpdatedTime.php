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

class Nybohansen_Pacsoft_Block_ServicePointLastUpdatedTime extends Mage_Adminhtml_Block_System_Config_Form_Field
{

    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
    	$this->setElement($element);
        return $this->getHtml();
    }
    

    public function getHtml(){
        /* @var $servicePoints Nybohansen_Pacsoft_Model_ServicePoints */
        $servicePoints = Mage::getModel('pacsoft/servicePoints');
        $updateTimes = $servicePoints->getLastUpdateTime();

        $html = '<span>';
        foreach($updateTimes as $c => $t){
            $timeStr = date("d-m-Y H:i:s", Mage::getModel('core/date')->timestamp($t));
            $html .= $c .':&nbsp;&nbsp;'. $timeStr. '<br/>';
        }
        $html .= '</span>';
        return $html;
    }
}
?>