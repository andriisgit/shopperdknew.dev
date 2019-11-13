<?php
class Inchoo_Backorderreport_Adminhtml_Backorderreport_BackorderController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->loadLayout();
        $this->_title($this->__('Reports'))->_title($this->__('Backorder Report'));
        $this->_setActiveMenu('report');

        $this->_addContent($this->getLayout()->
                createBlock('backorderreport/backorderreport_backorder', 'backorderreports'));
            $this->renderLayout();
    }

    public function filterAction()
    {
        $this->getResponse()->setBody($this->getLayout()->
            createBlock('backorderreport/backorderreport_backorder', 'backorderreports')
            ->setPost($this->getRequest()->getPost())
            ->toHtml());
    }        
}