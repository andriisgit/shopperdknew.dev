<?php
$adminVersion = Mage::getConfig()->getModuleConfig('Mage_Admin')->version;
if (version_compare($adminVersion, '1.6.1.2', '>=')) 
{
	$blockNames = array(
        'cms/block',
        'catalog/product_list',
		'catalog/product_new',
		'shoppersettings/product_sale',
        'shoppersettings/product_list',
        'page/html',
        'newsletter/subscribe',        
    );
	$installer = $this;
	$installer->startSetup();
	foreach ($blockNames as $blockName) 
	{
		if (!$installer->getConnection()->fetchOne("select * from {$this->getTable('permission_block')} where `block_name`='$blockName'")) 
		{
			$installer->run("insert  into {$this->getTable('permission_block')} (`block_name`,`is_allowed`) values ('$blockName','1');");
		}
	}
	$installer->endSetup();	
}
Mage::getSingleton('shoppersettings/css')->regenerate();
?>
