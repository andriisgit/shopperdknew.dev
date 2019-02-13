<?php
class Devweb_Packslip_Model_Observer
{
	public function addMassAction($observer)
	{
		$block = $observer->getEvent()->getBlock();
		if (strpos($block->getRequest()->getControllerName(), 'sales_order') !== false && is_a($block, 'Mage_Adminhtml_Block_Widget_Grid_Massaction_Abstract')) {
			$block->addItem('pdfpackslip', array(
				'label'=> Mage::helper('sales')->__('Pre-Packingslips'),
				'url'  => $block->getUrl('adminhtml/packslip/pack'),
			));
		}
	}
}