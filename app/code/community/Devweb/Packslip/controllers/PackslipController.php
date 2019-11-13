<?php

class Devweb_Packslip_PackslipController extends Mage_Adminhtml_Controller_Action
{
	protected function _isAllowed()
	{
		return Mage::getSingleton('admin/session')->isAllowed('sales/order');
	}
	
	public function packAction()
	{
		$orderIds = $this->getRequest()->getPost('order_ids');
		$flag = false;
		
		if (!empty($orderIds)) {
			foreach ($orderIds as $orderId) {
				if (!$orderId) {
					continue;
				}
				$order = Mage::getModel('sales/order');
				$order->load($orderId);
				$shipment = Mage::getModel('packslip/order_shipment');
				$shipment->setOrder($order);
				$shipment->setIncrementId('PRE-'. $order->getRealOrderId());
				$shipment->setCreatedAt($order->getCreatedAt());
				$shipments[] = $shipment;
				$flag = true;
			}
			
			if ($flag) {
				$pdf = Mage::getModel('sales/order_pdf_shipment')->getPdf($shipments);
				return $this->_prepareDownloadResponse(
					'packingslip'.Mage::getSingleton('core/date')->date('Y-m-d_H-i-s').'.pdf', $pdf->render(),
					'application/pdf'
				);
			} else {
				$this->_getSession()->addError($this->__('There are no printable documents related to selected orders.'));
				$this->_redirect('adminhtml/sales_order/');
			}
		}
		
		$this->_redirect('adminhtml/sales_order/');
	}
}
