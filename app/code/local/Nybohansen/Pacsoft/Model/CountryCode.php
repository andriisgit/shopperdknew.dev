<?php

class Nybohansen_Pacsoft_Model_CountryCode extends Mage_Core_Model_Abstract{

	private $_codes = array(  '001'=>array('France','FR'),
						      '002'=>array('Belguim','BE'),
						      '003'=>array('Netherlands','NL'),
						      '004'=>array('Germany','DE'),
						      '005'=>array('Italy','IT'),
						      '006'=>array('United Kingdom','GB'),
						      '007'=>array('Ireland','IE'),
						      '008'=>array('Denmark','DK'),
						      '009'=>array('Greece','GR'),
						      '010'=>array('Portugal','PT'),
						      '011'=>array('Spain','ES'),
						      '019'=>array('Luxembourg','LU'),
						      '028'=>array('Norway','NO'),
						      '030'=>array('Sweden','SE'),
						      '032'=>array('Finland','FI'),
						      '038'=>array('Austria','AT'),
						      '039'=>array('Switzerland','CH'),
						      '043'=>array('Andorra','AD'),
						      '045'=>array('Vatican City','VA'),
						      '046'=>array('Malta','MT'),
						      '052'=>array('Turkey','TR'),
						      '060'=>array('Poland','PL'),
						      '061'=>array('Czech Republic','CZ'),
						      '063'=>array('Slovakia','SK'),
						      '064'=>array('Hungary','HU'),
						      '066'=>array('Romania','RO'),
						      '070'=>array('Albania','AL'),
						      '072'=>array('Ukraine','UA'),
						      '073'=>array('Belarus','BY'),
						      '074'=>array('Moldova','MD'),
						      '075'=>array('Russian Federation','RU'),
						      '076'=>array('Georgia','GE'),
						      '077'=>array('Armenia','AM'),
						      '078'=>array('Azerbaijan','AZ'),
						      '079'=>array('Kazakhstan','KZ'),
						      '080'=>array('Turkmenistan','TM'),
						      '081'=>array('Uzbekistan','UZ'),
						      '082'=>array('Tajikistan','TJ'),
						      '083'=>array('Kyrgyzstan','KG'),
						      '091'=>array('Slovenia','SI'),
						      '092'=>array('Croatia','HR'),
						      '093'=>array('Bosnia and Herzegowina','BA'),
						      '096'=>array('Macedonia','MK'),
						      '097'=>array('Montenegro','ME'),
						      '098'=>array('Serbia and Montenegro','RS'),
						      '100'=>array('Bulgaria','BG'),
						      '196'=>array('Cyprus','CY'),
						      '204'=>array('Marocco','MA'),
						      '208'=>array('Algeria','DZ'),
						      '212'=>array('Tunisia','TN'),
						      '216'=>array('Libya','LY'),
						      '220'=>array('Egypt','EG'),
						      '224'=>array('Sudan','SD'),
						      '228'=>array('Mauritania','MR'),
						      '232'=>array('Mali','ML'),
						      '233'=>array('Estonia','EE'),
						      '234'=>array('Faroe Islands','FO'),
						      '236'=>array('Burkina Faso','BF'),
						      '240'=>array('Niger','NE'),
						      '244'=>array('Chad','TD'),
						      '247'=>array('Cape Verde','CV'),
						      '248'=>array('Senegal','SN'),
						      '252'=>array('Gambia','GM'),
						      '257'=>array('Guinea-Bissau','GW'),
						      '260'=>array('Guinea','GN'),
						      '264'=>array('Sierra Leone','SL'),
						      '268'=>array('Liberia','LR'),
						      '272'=>array('Cote D´Ivoire','CI'),
						      '276'=>array('Ghana','GH'),
						      '280'=>array('Togo','TG'),
						      '284'=>array('Benin','BJ'),
						      '288'=>array('Nigeria','NG'),
						      '292'=>array('Gibraltar','GI'),
						      '302'=>array('Cameroon','CM'),
						      '304'=>array('Greenland','GL'),
						      '306'=>array('Central African Repu','CF'),
						      '310'=>array('Equatorial Guinea','GQ'),
						      '314'=>array('Gabon','GA'),
						      '318'=>array('Congo','CG'),
						      '322'=>array('Congo, Dem.Rep.','CD'),
						      '324'=>array('Rwanda','RW'),
						      '328'=>array('Burundi','BI'),
						      '330'=>array('Angola','AO'),
						      '334'=>array('Ethiopia','ET'),
						      '336'=>array('Eritrea','ER'),
						      '338'=>array('Djibouti','DJ'),
						      '342'=>array('Somalia','SO'),
						      '346'=>array('Kenya','KE'),
						      '350'=>array('Uganda','UG'),
						      '352'=>array('Iceland','IS'),
						      '355'=>array('Seychelles','SC'),
						      '366'=>array('Mozambique','MZ'),
						      '370'=>array('Madagascar','MG'),
						      '373'=>array('Mauritius','MU'),
						      '378'=>array('Zambia','ZM'),
						      '382'=>array('Zimbabwe','ZW'),
						      '386'=>array('Malawi','MW'),
						      '388'=>array('South Africa','ZA'),
						      '389'=>array('Namibia','NA'),
						      '391'=>array('Botswana','BW'),
						      '393'=>array('Swaziland','SZ'),
						      '395'=>array('Lesotho','LS'),
						      '400'=>array('United States','US'),
						      '404'=>array('Canada','CA'),
						      '410'=>array('Korea','KR'),
						      '412'=>array('Mexico','MX'),
						      '413'=>array('Bermuda','BM'),
						      '416'=>array('Guatemala','GT'),
						      '421'=>array('Belize','BZ'),
						      '422'=>array('Lebanon','LB'),
						      '424'=>array('Honduras','HN'),
						      '428'=>array('Latvia','LV'),
						      '432'=>array('Nicaragua','NI'),
						      '440'=>array('Lithuania','LT'),
						      '442'=>array('Panama','PA'),
						      '446'=>array('Anguilla','AI'),
						      '449'=>array('St. Kitts & Nevis','KN'),
						      '452'=>array('Haiti','HT'),
						      '453'=>array('Bahamas','BS'),
						      '456'=>array('Dominican Republik','DO'),
						      '457'=>array('Virgin Islands (USA)','VI'),
						      '459'=>array('Antigua and Barbuda','AG'),
						      '460'=>array('Dominica','DM'),
						      '463'=>array('Cayman Islands','KY'),
						      '464'=>array('Jamaica','JM'),
						      '465'=>array('St. Lucia','LC'),
						      '467'=>array('St. Vincent','VC'),
						      '468'=>array('Virgin Islands (Brit','VG'),
						      '469'=>array('Barbados','BB'),
						      '470'=>array('Montserrat','MS'),
						      '472'=>array('Trinidad & Tobago','TT'),
						      '473'=>array('Grenada','GD'),
						      '474'=>array('Aruba','AW'),
						      '478'=>array('Netherl. Antilles','AN'),
						      '480'=>array('Colombia','CO'),
						      '484'=>array('Venezuela','VE'),
						      '488'=>array('Guyana','GY'),
						      '492'=>array('Monaco','MC'),
						      '500'=>array('Ecuador','EC'),
						      '508'=>array('Brazil','BR'),
						      '512'=>array('Chile','CL'),
						      '516'=>array('Bolivia','BO'),
						      '520'=>array('Paraguay','PY'),
						      '524'=>array('Uruguay','UY'),
						      '528'=>array('Argentina','AR'),
						      '604'=>array('Peru','PE'),
						      '608'=>array('Syrian Arab Republic','SY'),
						      '612'=>array('Iraq','IQ'),
						      '616'=>array('Iran','IR'),
						      '624'=>array('Israel','IL'),
						      '628'=>array('Jordan','JO'),
						      '632'=>array('Saudi Arabia','SA'),
						      '636'=>array('Kuwait','KW'),
						      '638'=>array('Reunion','RE'),
						      '640'=>array('Bahrain','BH'),
						      '644'=>array('Qatar','QA'),
						      '647'=>array('United Arab Emirates','AE'),
						      '649'=>array('Oman','OM'),
						      '653'=>array('Yemen','YE'),
						      '662'=>array('Pakistan','PK'),
						      '664'=>array('India','IN'),
						      '666'=>array('Bangladesh','BD'),
						      '667'=>array('Maldives','MV'),
						      '669'=>array('Sri Lanka','LK'),
						      '672'=>array('Nepal','NP'),
						      '674'=>array('San Marino','SM'),
						      '675'=>array('Bhutan','BT'),
						      '676'=>array('Myanmar','MM'),
						      '680'=>array('Thailand','TH'),
						      '684'=>array('Lao People´s Republic','LA'),
						      '690'=>array('Vietnam','VN'),
						      '696'=>array('Cambodia','KH'),
						      '700'=>array('Indonesia','ID'),
						      '701'=>array('Malaysia','MY'),
						      '703'=>array('Brunei Darussalam','BN'),
						      '706'=>array('Singapore','SG'),
						      '708'=>array('Philippines','PH'),
						      '716'=>array('Mongolia','MN'),
						      '720'=>array('China','CN'),
						      '732'=>array('Japan','JP'),
						      '736'=>array('Taiwan','TW'),
						      '740'=>array('Hong Kong','HK'),
						      '743'=>array('Macau','MO'),
						      '800'=>array('Australia','AU'),
						      '801'=>array('Papua New Guinea','PG'),
						      '804'=>array('New Zealand','NZ'),
						      '809'=>array('New Caledonia','NC'),
						      '811'=>array('Wallis & Futuna Isla','WF'),
						      '815'=>array('Fiji','FJ'),
						      '816'=>array('Vanuatu','VU'),
						      '820'=>array('Nothern Mariana Isla','MP'),
						      '822'=>array('France-Polynesia','PF'),
						      '823'=>array('Micronesia','FM'),
						      '824'=>array('Marshall Islands','MH'),
						      '825'=>array('Palau','PW'));

	public function getCountryCodeISO3166A2($id){
		if(isset($this->_codes[$id])){
			$tmp = $this->_codes[$id];
			return $tmp[1];
		}
		return NULL;
	}		
	
	public function getCountryId($code){
		foreach ($this->_codes as $key => $value){
			if($value[1] == $code){
				return $key;
			}
		}
		return NULL;
	}
	
}