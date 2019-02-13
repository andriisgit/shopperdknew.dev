<?php
/**
 * NWT_Pricecomparison extension
 *
 * @category    NWT
 * @package     NWT_Pricecomparison
 * @copyright   Copyright (c) 2014 Nordic Web Team ( http://nordicwebteam.se/ )
 * @license     NWT Commercial License (NWTCL 1.0)
 * @author      Emil [carco] Sirbu (emil.sirbu@gmail.com)
 *
 */
/**
 * Feed admin controller
 *
 */
class NWT_Pricecomparison_Adminhtml_Pricecomparison_FeedController extends Mage_Adminhtml_Controller_Action {

    const NWT_PC_PATH = 'nwt/pricecomparison';

    /**
     * init the feed
     * @access protected
     * @return NWT_Pricecomparison_Model_Feed
     */
    protected function _initFeed(){
        $feedId  = (int) $this->getRequest()->getParam('id');
        $feed    = Mage::getModel('pricecomparison/feed');
        if ($feedId) {
            $feed->load($feedId);
        }
        Mage::register('current_feed', $feed);
        return $feed;
    }
     /**
     * default action
     * @access public
     * @return void
     */
    public function indexAction() {
        
        $this->loadLayout();
        $this->_setActiveMenu(self::NWT_PC_PATH);
        $this->renderLayout();
    }
    /**
     * grid action
     * @access public
     * @return void
     */
    public function gridAction() {
        $this->loadLayout()->renderLayout();
    }
    /**
     * edit feed - action
     * @access public
     * @return void
     */
    public function editAction() {

        $feedId    = $this->getRequest()->getParam('id');
        $feed      = $this->_initFeed();
        if ($feedId && !$feed->getId()) {
            $this->_getSession()->addError(Mage::helper('pricecomparison')->__('This feed no longer exists.'));
            $this->_redirect('*/*/');
            return;
        }
        $data = $this->_getSession()->getFormData(true);
        if (!empty($data)) {
            $feed->addData($data);
        } else {
            if(!$feed->getId()) {
                $feed->setDefaultData($this->getRequest()->getParam('type'),$this->getRequest()->getParam('store'));
            }
        } 
        $this->loadLayout();
        $this->_setActiveMenu(self::NWT_PC_PATH);
        $this->renderLayout();
    }
    /**
     * new feed action
     * @access public
     * @return void
     */
    public function newAction() {
        $this->_forward('edit');
    }

    /**
     * save feed - action
     * @access public
     * @return void
     */
    public function saveAction() {
        if ($data = $this->getRequest()->getPost('feed')) {

//             echo '<pre>',var_export($this->getRequest()->getPost(),true),'</pre>';
//             exit();
            try {
                $feed = $this->_initFeed();
                $feed->addData($data);
                $feed->save();
                $this->_getSession()->addSuccess(Mage::helper('pricecomparison')->__('Feed was successfully saved'));
                $this->_getSession()->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $feed->getId()));
                    return;
                }
                $this->_redirect('*/*/');
                return;
            } 
            catch (Mage_Core_Exception $e){
                $this->_getSession()->addError($e->getMessage());
                $this->_getSession()->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
            catch (Exception $e) {
                $this->_getSession()->addError(Mage::helper('pricecomparison')->__('There was a problem saving the feed.'));
                $this->_getSession()->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        $this->_getSession()->addError(Mage::helper('pricecomparison')->__('Unable to find feed to save.'));
        $this->_redirect('*/*/');
    }

    /**
     * generate feed - action
     * @access public
     * @return void
     */
    public function generateAction() {
        $feed      = $this->_initFeed();
        if (!$feed->getId()) {
            $this->_getSession()->addError(Mage::helper('pricecomparison')->__('This feed no longer exists.'));
            $this->_redirect('*/*/');
            return;
        }
        try {
            $feedModel = Mage::getModel('pricecomparison/feed_generator');

            $feedModel->generate($feed);
            $this->_getSession()->addSuccess(Mage::helper('pricecomparison')->__('Feed %s has successfully generated',$feed->getName()));
            $this->_redirect('*/*/');
        } catch(Exception $e) {
            $this->_getSession()->addError($e->getMessage());
            $this->_redirect('*/*/');
        } 

    }

    /**
     * mass generate feed - action
     * @access public
     * @return void
     */
    public function massGenerateAction() {
        $feedIds = $this->getRequest()->getParam('feed');
        if(!is_array($feedIds)) {
            $this->_getSession()->addError(Mage::helper('pricecomparison')->__('Please select feeds to generate.'));
        } else {
                $errors  = array();
                $success = array();
                foreach ($feedIds as $feedId) {
                    try {
                        $feed =  Mage::getModel('pricecomparison/feed')->load($feedId);
                        if(!$feed->getId()) {
                            Mage::throwException(Mage::helper('pricecomparison')->__('Invalid feed #%s',$feedId));
                        }
                        Mage::getModel('pricecomparison/feed_generator')->generate($feed);
                        $success[] = $feed->getName();
                    } catch(Exception $e) {
                        $errors[] = ($feed->getId()?$feed->getName():('#'.$feedId)).' - '.$e->getMessage();
                    } 
                }
                if($success) {
                    $this->_getSession()->addSuccess(Mage::helper('pricecomparison')->__('Feed(s): [ %s ] has successfully generated',implode(', ',$success)));
                }
                if($errors) {
                    $this->_getSession()->addError(Mage::helper('pricecomparison')->__('There was an error generating feeds:<br />%s',implode('<br />',$errors)));
                }
        }
        $this->_redirect('*/*/index');
    }


    /**
     * delete feed - action
     * @access public
     * @return void
     */
    public function deleteAction() {
        if( $this->getRequest()->getParam('id') > 0) {
            try {
                $feed = Mage::getModel('pricecomparison/feed');
                $feed->setId($this->getRequest()->getParam('id'))->delete();
                $this->_getSession()->addSuccess(Mage::helper('pricecomparison')->__('Feed was successfully deleted.'));
                $this->_redirect('*/*/');
                return; 
            }
            catch (Mage_Core_Exception $e){
                $this->_getSession()->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            }
            catch (Exception $e) {
                $this->_getSession()->addError(Mage::helper('pricecomparison')->__('There was an error deleteing feed.'));
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                Mage::logException($e);
                return;
            }
        }
        $this->_getSession()->addError(Mage::helper('pricecomparison')->__('Could not find feed to delete.'));
        $this->_redirect('*/*/');
    }
    /**
     * mass delete feed - action
     * @access public
     * @return void
     */
    public function massDeleteAction() {
        $feedIds = $this->getRequest()->getParam('feed');
        if(!is_array($feedIds)) {
            $this->_getSession()->addError(Mage::helper('pricecomparison')->__('Please select feeds to delete.'));
        }
        else {
            try {
                foreach ($feedIds as $feedId) {
                    $feed = Mage::getModel('pricecomparison/feed');
                    $feed->setId($feedId)->delete();
                }
                $this->_getSession()->addSuccess(Mage::helper('pricecomparison')->__('Total of %d feeds were successfully deleted.', count($feedIds)));
            }
            catch (Mage_Core_Exception $e){
                $this->_getSession()->addError($e->getMessage());
            }
            catch (Exception $e) {
                $this->_getSession()->addError(Mage::helper('pricecomparison')->__('There was an error deleteing feeds.'));
                Mage::logException($e);
            }
        }
        $this->_redirect('*/*/index');
    }
    /**
     * mass status change - action
     * @access public
     * @return void
     */
    public function massStatusAction(){
        $feedIds = $this->getRequest()->getParam('feed');
        if(!is_array($feedIds)) {
            $this->_getSession()->addError(Mage::helper('pricecomparison')->__('Please select feeds.'));
        } 
        else {
            try {
                foreach ($feedIds as $feedId) {
                $feed = Mage::getSingleton('pricecomparison/feed')->load($feedId)
                            ->setStatus($this->getRequest()->getParam('status'))
                            ->setIsMassupdate(true)
                            ->save();
                }
                $this->_getSession()->addSuccess($this->__('Total of %d feeds were successfully updated.', count($feedIds)));
            }
            catch (Mage_Core_Exception $e){
                $this->_getSession()->addError($e->getMessage());
            }
            catch (Exception $e) {
                $this->_getSession()->addError(Mage::helper('pricecomparison')->__('There was an error updating feeds.'));
                Mage::logException($e);
            }
        }
        $this->_redirect('*/*/index');
    }


    protected function _isAllowed()
    {
        return  Mage::getSingleton('admin/session')->isAllowed(self::NWT_PC_PATH);
    }
 
}