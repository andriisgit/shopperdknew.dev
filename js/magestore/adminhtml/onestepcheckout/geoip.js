
/*
 * Magestore
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Magestore
 * @package     Magestore_Onestepcheckout
 * @copyright   Copyright (c) 2017 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

function getLinkImport(link)
{					
	var parameters = countryGrid_massactionJsObject.getCheckedValues();	
	if(!parameters){
		alert('Please select country.');
		return false;
	}
	popWin(link+'id/'+parameters,'import','top:0,left:0,width=700,height=400,resizable=yes,scrollbars=yes');
}	

function importGeoIp(link)
{
	if(link){
		popWin(link,'import','top:0,left:0,width=700,height=400,resizable=yes,scrollbars=yes');
	}
	else
		alert('GeoIP database was updated!');
		return;
}