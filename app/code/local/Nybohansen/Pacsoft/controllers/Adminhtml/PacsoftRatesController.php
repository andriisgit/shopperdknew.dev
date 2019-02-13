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

class Nybohansen_Pacsoft_Adminhtml_PacsoftRatesController extends Mage_Adminhtml_Controller_Action
{

    public function indexAction()
    {
        // Let's call our initAction method which will set some basic params for each action
        $this->_initAction()->renderLayout();
    }

    /**
     * Initialize action
     *
     * Here, we set the breadcrumbs and the active menu
     *
     * @return Mage_Adminhtml_Controller_Action
     */
    protected function _initAction()
    {
        $this->loadLayout()
        // Make the active menu match the menu config nodes (without 'children' inbetween)
            ->_setActiveMenu('pacsoft/nybohansen_pacsoft_pacsoftRates')
            ->_title($this->__('Pacsoft'))->_title($this->__('Manage Rates'))
            ->_addBreadcrumb($this->__('Pacsoft'), $this->__('Pacsoft'))
            ->_addBreadcrumb($this->__('Manage Rates'), $this->__('Manage Rates'));

        return $this;
    }

    public function newAction()
    {
        // We just forward the new action to a blank edit form
        $this->_forward('edit');
    }

    public function editAction()
    {
        // Get id if available
        $rate_id  = $this->getRequest()->getParam('rate_id');
        $model = Mage::getModel('pacsoft/rates');

        if ($rate_id) {
            // Load record
            $model->load($rate_id);

            // Check if record is loaded
            if (!$model->getId()) {
                Mage::getSingleton('adminhtml/session')->addError($this->__('This rate no longer exists.'));
                $this->__redirect('*/*/');
                return;
            }
        }else{
            //New rate, set default values
            $model->setRegion('*');
            $model->setCity('*');
            $model->setZipRange('*');
            $model->setShipmentType('A');
            $model->setStatus(1);
        }

        $this->_title($model->getId() ? $model->getName() : $this->__('New Rate'));

        $data = Mage::getSingleton('adminhtml/session')->getRateData(true);
        if (!empty($data)) {
            $model->setData($data);
        }

        Mage::register('pacsoft_rate', $model);

        $this->_initAction()
            ->_addBreadcrumb($rate_id ? $this->__('Edit Rate') : $this->__('New Rate'), $rate_id ? $this->__('Edit Rate') : $this->__('New Rate'))
            ->_addContent($this->getLayout()->createBlock('pacsoft/adminhtml_rates_edit')->setData('action', $this->getUrl('*/*/save')))
            ->renderLayout();
    }

    public function saveAction()
    {
        if ($postData = $this->getRequest()->getPost()) {

            $model = Mage::getSingleton('pacsoft/rates');

            if($rate_id = (int)$this->getRequest()->getParam('rate_id'))
            {
                $postData["rate_id"] = $rate_id;
                $model->load($postData["rate_id"]);
            }

            $stores = $postData['stores'];
            unset($postData['stores']);

            $postData['region'] = trim($postData['region']) == '' ? '*' : $postData['region'];
            $postData['city']   = trim($postData['city']) == '' ? '*' : $postData['city'];
            $postData['zip_range']   = trim($postData['zip_range']) == '' ? '*' : $postData['zip_range'];
            $postData['condition_range']   = trim($postData['condition_range']) == '' ? '*' : $postData['condition_range'];
            $postData['country'] = implode(",",$postData["country"]);

            if(isset($postData["addons"])){
                $postData['addons'] = implode(",",$postData["addons"]);
            }else{
                $postData['addons'] = '';
            }

            $model->setData($postData);


            try {
                $model->save();
                $model->setStores($stores);

                Mage::getSingleton('adminhtml/session')->addSuccess($this->__('The rate has been saved.'));
                $this->__redirect('*/*/');

                return;
            }
            catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
            catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($this->__('An error occurred while saving this rate.'));
            }

            Mage::getSingleton('adminhtml/session')->setRateData($postData);
            $this->__redirect('*/*/');
        }
    }

    public function messageAction()
    {
        $data = Mage::getModel('pacsoft/rates')->load($this->getRequest()->getParam('rate_id'));
        echo $data->getContent();
    }


    public function deleteAction()
    {
        if ($rateId = (int) $this->getRequest()->getParam('rate_id')) {
            try {
                $model = Mage::getSingleton('pacsoft/rates')->load($rateId);
                $model->delete();
                $this->_getSession()->addSuccess($this->__('The shipping rate has been deleted.'));
                $this->__redirect('*/*/');
                return;
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
                $this->__redirect('*/*/edit', array('id' => $rateId));
                return;
            }
        }
        $this->_getSession()->addError($this->__('Unable to find the shipping rate to delete.'));
        $this->__redirect('*/*/');
    }

    public function massDeleteAction()
    {
        $helper = Mage::helper('pacsoft/rates');
        $rateIds = $this->getRequest()->getParam('rate_id');
        if (is_array($rateIds)) {
            if (!empty($rateIds)) {
                try {
                    foreach ($rateIds as $rateId) {
                        $model = Mage::getSingleton('pacsoft/rates')->load($rateId);
                        $model->delete();
                    }
                    $this->_getSession()->addSuccess(
                        $this->__('Total of %d record(s) have been deleted.', count($rateIds))
                    );
                } catch (Exception $e) {
                    $this->_getSession()->addError($e->getMessage());
                }
            }
        } else $this->_getSession()->addError($helper->__('Please select shipping rate(s).'));
        $this->__redirect('*/*/index');
    }

    protected function __redirect($path)
    {
        return $this->_redirect($path);
    }

    public function getAddonsForServiceAction(){
        $serviceCode = $this->getRequest()->getPost('serviceCode');
        $rateId = $this->getRequest()->getPost('rateId');

        $block = Mage::getSingleton('core/layout')->createBlock('pacsoft/adminhtml_addons');
        $block->setData('serviceCode', $serviceCode);
        $block->setData('rateId', $rateId);

        $this->getResponse()->setBody(json_encode($block->toHtml()));
    }

}