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

class Nybohansen_Pacsoft_Helper_Rates extends Mage_Core_Helper_Abstract
{

    public function getStoreIds()
    {
        return Mage::app()->getFrontController()->getRequest()->getParam('store');
    }

    public function getStore()
    {
        return Mage::app()->getStore((int) $this->getStoreIds());
    }

    public function getConditionCodes(){
        return array('weight' => $this->__('Weight vs. Destination'),
                     'price'  => $this->__('Price vs. Destination'),
                     'number' => $this->__('Number of Items vs. Destination'));
    }

    public function getServices(){
        return array(
            'PDK332' => 'PostNord Business Priority (Samsending)',
            'PDK330' => 'PostNord Business Priority (Single)',
            'PDK17' => 'PostNord MyPack Home',
            'PDK17BP' => 'PostNord MyPack Home (Samsending)',
            'PDKEP'  => 'PostNord Parcel',
            'P52DK'  => 'PostNord Pallet',
            'PDK359' => 'PostNord Parcel Economy',
            'PDK340' => 'PostNord Private Priority ',
            'P19DK'  => 'PostNord MyPack Collect DK',
            'P19DKDPD' => 'Post Danmark Business Return',
            'P19DKNO' => 'PostNord MyPack Collect (Norway)',
            'P19DKBP' => 'PostNord MyPack Collect (Samsending) ',
            'PDKWAY'  => 'PostNord Waybill '
        );
    }

    public function getAddons(){
        return array(
            'AIR'      => 'Fly',
            'BSPLIT'   => 'BulkSplit',
            'COD'      => 'Postopkr??vning',
            'DLV'      => 'Omdeling',
            'DLVFLEX'  => 'Flexlevering',
            'DLVIN'    => 'Indb??ring',
            'DLVT'     => 'Tidsbestemt levering',
            'DLVT10'   => 'Levering f??r 10',
            'DNG'      => 'Farligt indhold i begr??nset m??ngde',
            'GREEN'    => 'Pakke med omtanke',
            'INSU'     => 'Transportforsikring',
            'NOTEMAIL' => 'Email-advisering',
            'NOTLTR'   => 'Brevadvisering',
            'NOTPHONE' => 'Tlf. varsel',
            'NOTSMS'   => 'SMS-advisering',
            'POD'      => 'Modtagerkvittering',
            'PUPOPT'   => 'Valgfrit afhentningssted',
            'RETCAR'   => 'Returpakkelabel udskrives af PDK',
            'RETPUP'   => 'Returpakke afh. Erhverv',
            'SPXL'     => 'XL-omdeling',
            'VALUE'    => 'V??rdi'
        );
    }

    public function getAddonsForService($service){
        switch ($service){
            case 'PDK332':
                return array('COD'      => 'Postopkr??vning',
                             'DLVFLEX'  => 'Flexlevering',
                             'INSU'     => 'Transportforsikring');
                break;
            case 'PDK330':
                return array('COD'      => 'Postopkr??vning',
                             'INSU'     => 'Transportforsikring');
                break;
            case 'PDKEP':
                return array('NOTEMAIL' => 'Email-advisering',
                             'DNG'      => 'Farligt indhold i begr??nset m??ngde',
                             'DLVFLEX'  => 'Flexlevering',
                             'DLVT10'   => 'Levering f??r 10',
                             'POD'      => 'Modtagerkvittering',
                             'GREEN'    => 'Pakke med omtanke',
                             'NOTSMS'   => 'SMS-advisering',
                             'INSU'     => 'Transportforsikring',
                             'VALUE'    => 'V??rdi');
                break;
            case 'PDKEPR':
                return array('RETPUP'   => 'Returpakke afh. Erhverv',
                             'RETCAR'   => 'Returpakkelabel udskrives af PDK');
                break;
            case 'P52DK':
                return array('DNG'      => 'Farligt indhold i begr??nset m??ngde',
                             'DLVFLEX'  => 'Flexlevering',
                             'DLVIN'    => 'Indb??ring',
                             'DLVT'     => 'Tidsbestemt levering',
                             'NOTPHONE' => 'Tlf. varsel'
                );
                break;
            case 'P52DKR':
                return array('DNG'      => 'Farligt indhold i begr??nset m??ngde');
                break;
            case 'PDK359':
                return array('COD'      => 'Postopkr??vning',
                             'INSU'     => 'Transportforsikring');
                break;
            case 'PDK340':
                return array('INSU'     => 'Transportforsikring',
                             'VALUE'    => 'V??rdi');
                break;
            case 'P19DK':
                return array('NOTLTR'   => 'Brevadvisering',
                             'NOTEMAIL' => 'Email-advisering',
                             'DLVFLEX'  => 'Flexlevering',
                             'AIR'      => 'Fly',
                             'DLV'      => 'Omdeling',
                             'GREEN'    => 'Pakke med omtanke',
                             'COD'      => 'Postopkr??vning',
                             'NOTSMS'   => 'SMS-advisering',
                             'INSU'     => 'Transportforsikring',
                             'PUPOPT'   => 'Valgfrit afhentningssted',
                             'VALUE'    => 'V??rdi',
                             'SPXL'     => 'XL-omdeling');
                break;
            case 'P19DKNO':
                return array('INSU'     => 'Transportforsikring');
                break;
            case 'P19DKBP':
                return array('NOTLTR'   => 'Brevadvisering',
                             'BSPLIT'   => 'BulkSplit',
                             'NOTEMAIL' => 'Email-advisering',
                             'AIR'      => 'Fly',
                             'COD'      => 'Postopkr??vning',
                             'NOTSMS'   => 'SMS-advisering',
                             'INSU'     => 'Transportforsikring',
                             'PUPOPT'   => 'Valgfrit afhentningssted',
                             'VALUE'    => 'V??rdi');
                break;
            case 'PDKLASTMILE':
                return array(
                    'DLVFLEX'  => 'Flexlevering',
                    'DLVT10'   => 'Levering f??r 10');
                break;
            case 'PDK17':
                return array('NOTEMAIL' => 'Email-advisering',
                             'DLVFLEX'  => 'Flexlevering',
                             'NOTSMS'   => 'SMS-advisering',
                             'AIR'      => 'Fly',
                             'COD'      => 'Postopkr??vning',
                             'INSU'     => 'Transportforsikring',
                             'VALUE'    => 'V??rdi');
                break;
            case 'PDK17BP':
                return array('NOTEMAIL' => 'Email-advisering',
                            'DLVFLEX'  => 'Flexlevering',
                            'NOTSMS'   => 'SMS-advisering',
                            'INSU'     => 'Transportforsikring',
                            'VALUE'    => 'V??rdi');
                break;

            default:
                return array();
        }

    }

}