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

class Nybohansen_Economic2_Block_LogView extends Mage_Adminhtml_Block_System_Config_Form_Field
{

    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {

        $logDir = Mage::getBaseDir('log');
        $logFile = $logDir."/e-conomic2.log";

        $logEntries = '';
        if(file_exists($logFile)){
            $logFile = $this->readFileBackwards($logFile, 100);
            foreach($logFile as $l){
                $logEntries .= $l."\n";
            }
        }


        $js = "<script type=\"text/javascript\">
                function updateLogView(){
                        var url = '".Mage::helper("adminhtml")->getUrl('economic2/adminhtml_system_config/loadLog', array('' => ''))."';
                        new Ajax.Request(url, {method: 'post',
				                            onComplete: function(transport) {
                                                                                $('economic2_log_file_viewer').innerHTML = transport.responseText;
									                                        }
								            }
                                      );
                     }

                     function clearLog(){
                        var url = '".Mage::helper("adminhtml")->getUrl('economic2/adminhtml_system_config/clearLog', array('' => ''))."';
                        new Ajax.Request(url, {method: 'post',
				                            onComplete: function(transport) {
				                                                                updateLogView();
									                                        }
								            }
                                      );
                     }
             </script>";



        $html = $js.'<style>
                        #row_economic2_options_log_config_logview .label {display: none;}
                    </style>
                    <textarea id="economic2_log_file_viewer" rows="4" cols="200" style="width: 800px; height: 40em;" wrap="off" readonly="yes">'.$logEntries.'</textarea>';

        $updateBtn = $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setType('button')
            ->setClass('scalable')
            ->setLabel(Mage::helper('economic2')->__('Update'))
            ->setOnClick('javascript: updateLogView();');


        $clearBtn = $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setType('button')
            ->setClass('scalable')
            ->setLabel(Mage::helper('economic2')->__('Clear log!'))
            ->setOnClick('javascript: clearLog();');

        $html .= $updateBtn->toHtml().'<span>  </span>'.$clearBtn->toHtml();

        return $html;
    }

    private function readFileBackwards($logfile, $numberOfLines){
        $fl = fopen($logfile, "r");
        for($x_pos = 0, $ln = 0, $output = array(); fseek($fl, $x_pos, SEEK_END) !== -1; $x_pos--) {
            $char = fgetc($fl);
            if ($char === "\n") {
                // analyse completed line $output[$ln] if need be
                $ln++;
                if($ln>$numberOfLines){
                    return $output;
                }else{
                    continue;
                }
            }
            $output[$ln] = $char . ((array_key_exists($ln, $output)) ? $output[$ln] : '');
        }
        fclose($fl);
        return $output;
    }



}
?>