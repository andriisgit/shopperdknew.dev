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


class Nybohansen_Economic2_Adminhtml_System_ConfigController extends Mage_Adminhtml_Controller_Action
{

    protected function _construct()
    {
        parent::_construct();
    }

    /**
     * Clears e-conomic log
     */
    public function clearLogAction()
    {
        $logDir = Mage::getBaseDir('log');
        $filename = $logDir."/e-conomic2.log";
        unlink($filename);
    }

    public function loadLogAction()
    {
        $logDir = Mage::getBaseDir('log');
        $fileName = $logDir."/e-conomic2.log";
        if(file_exists($fileName)){
            $logFile = file($fileName);
            $logEntries = '';
            $i = max(count($logFile)-100,0);
            for ($i; $i < count($logFile); $i++) {
                $logEntries .= $logFile[$i];
            }
            echo $logEntries;
        }

    }




}
