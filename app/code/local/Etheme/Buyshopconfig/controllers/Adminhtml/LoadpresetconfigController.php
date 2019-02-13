<?php
/**
 * @version   1.0 14.08.2012
 * @author    TonyEcommerce http://www.TonyEcommerce.com <support@TonyEcommerce.com>
 * @copyright Copyright (c) 2012 TonyEcommerce
 */

class Etheme_Buyshopconfig_Adminhtml_LoadpresetconfigController extends Mage_Adminhtml_Controller_Action
{
	public function indexAction()
	{
        $this->loadLayout();
        $this->_addLeft($this->getLayout()->createBlock('core/text', 'leftside')->setText('<h2>Theme Maintenance</h2><h4>Add description later.</h4>'));
        $this->_addContent($this->getLayout()->createBlock('buyshopconfig/adminhtml_loadpresetconfig_edit'));
        $this->_setActiveMenu('etheme');

        $this->renderLayout();
	}

    public function configinstallAction()
    {
        $configset=$this->getRequest()->getParam('configset');
        $store=$this->getRequest()->getParam('store');
        $scope = $store ? 'stores' : 'default';
        $configxml=new Varien_Simplexml_Config(Mage::getBaseDir().'/Configsets/'.$configset.'.xml');

        foreach($configxml->getNode('sections')->children() as $section)
        {
            foreach($section->children() as $group)
            {
                foreach($group->children() as $param)
                {
                    Mage::getConfig()->saveConfig($section->getName().'/'.$group->getName().'/'.$param->getName(), (string)$param, $scope, $store);
                }
            }
        }

        Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('buyshopconfig')->__('Configset installed successfully. If you do not see the changes please clean the cache. <br /><b>ATTENTION!</b> To see changes on storefront you must also to do Refresh CSS files in Buyshop settings - Colors,Fonts'));
        $this->getResponse()->setRedirect($this->getUrl("*/*/"));
    }
}



