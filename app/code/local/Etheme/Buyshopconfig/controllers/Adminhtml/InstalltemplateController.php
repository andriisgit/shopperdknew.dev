<?php
/**
 * @version   1.0 14.08.2012
 * @author    TonyEcommerce http://www.TonyEcommerce.com <support@TonyEcommerce.com>
 * @copyright Copyright (c) 2012 TonyEcommerce
 */

class Etheme_Buyshopconfig_Adminhtml_InstalltemplateController extends Mage_Adminhtml_Controller_Action
{
	public function indexAction()
	{
        $this->loadLayout();
        $this->_addLeft($this->getLayout()->createBlock('core/text', 'leftside')->setText('<h2>Theme Maintenance</h2><h4>Add description later.</h4>'));
        $this->_addContent($this->getLayout()->createBlock('buyshopconfig/adminhtml_installtemplate_edit'));
        $this->_setActiveMenu('etheme');
        $this->_addContent($this->getLayout()->createBlock('core/text', 'faq')->setText('
        <h2>Auto Install</h2>
        <h4>If you click button \'Auto install\' automatically will be installed theme and imported needed buyshop cms blocks and pages.</h4>
        <h6 style="color:red">Auto install will rewrite buyshop cms blocks and pages if they were installed early.</h6>
        <h6 style="color:red">Don\'t forget disable System/Tools/Compilation before install if this mode enabled</h6>
        <br />
        <h2>Manual Install</h2>
        <ol style="list-style-type:">
            <li>1. Check that System/Tools/Compilation disabled</li>
            <li>2. Goto Etheme-Buyshop/Install/Install cms block/pages and Click \'Submit action\'</li>
            <li>3. Goto System/Configuration/Design<br/>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;3.1 In field <b>Current Package Name</b> write <b>buyshop</b><br/>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;3.2 In field <b>Themes/Default</b> write <b>default</b><br/>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;3.3 Click \'Save\'
            </li>
            <li>4. Goto System/Configuration/Web<br/>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;4.1 In field <b>CMS Home Page</b> select one of pages you want <b>Buyshop Home page</b> / <b>Buyshop Home page - Amaizing style</b> / <b>Buyshop Home page - Clean style</b> / <b>Buyshop Home page - iStyle</b> / <b>Buyshop Home page - Dark style</b> / <b>Buyshop Home page - Vsecret style</b> / <b>Buyshop Home page - Advanced style</b> <br/>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;4.2 In field <b>CMS No Route Page</b> select <b>Buyshop 404</b><br/>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;4.3 Click \'Save\'
            </li>
            <li>5. Goto System/Cache Management/ Click \'Flush Cache\'</li>
        </ol>
        '));
        $this->renderLayout();
	}

    public function installAction()
    {
        $store = 0;
        Mage::getModel('buyshopconfig/content')->setPermissions();
        Mage::getModel('buyshopconfig/content')->installTemplateResources();
        Mage::getModel('buyshopconfig/content')->installTemplateBlocks();
        Mage::getModel('buyshopconfig/content')->installTemplateConfig($store);
        Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('buyshopconfig')->__("Buyshop Template installed successfully. If you do not see the changes please clean the cache.<br /><b>ATTENTION!!</b> Log out from magento admin panel if you are logged in. That is required step for final theme installation. Also this will avoid 404 page in theme settings"));
        $this->getResponse()->setRedirect($this->getUrl("*/*/"));
    }
}



