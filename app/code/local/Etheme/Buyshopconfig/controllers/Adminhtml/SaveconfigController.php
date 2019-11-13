<?php
/**
 * @version   1.0 14.08.2012
 * @author    TonyEcommerce http://www.TonyEcommerce.com <support@TonyEcommerce.com>
 * @copyright Copyright (c) 2012 TonyEcommerce
 */

class Etheme_Buyshopconfig_Adminhtml_SaveconfigController extends Mage_Adminhtml_Controller_Action
{
	public function indexAction()
	{
        $this->loadLayout();
        $this->_addLeft($this->getLayout()->createBlock('core/text', 'leftside')->setText('<h2>Theme Maintenance</h2><h4>Save your configuration from choosen store to xml file (as preset  Configset). Your saved configuration after can be loaded in menu "Load preset configuration".</h4>'));
        $this->_addContent($this->getLayout()->createBlock('buyshopconfig/adminhtml_saveconfig_edit'));
        $this->_setActiveMenu('etheme');


        $this->renderLayout();
	}

    public function saveconfigAction()
    {

        require_once(Mage::getBaseDir().'/app/code/local/Etheme/Buyshopconfig/lib/Array2Xml.php');
        $name=$this->getRequest()->getParam('name');
        //$image=$this->getRequest()->getParam('image');
        $store=$this->getRequest()->getParam('store');
        $scope = $store ? 'stores' : 'default';

        $sections=array('buyshopconfig','buyshopcolors','buyshoplayout','buyshoparallax','buyshopflex','lightboxes','buyshopimage','facebookfree');

        $data=array();
        $data['name']=$name;
        $data['image']='';

        foreach($sections as $section)
        {
            $data['sections'][$section]=Mage::getStoreConfig($section,$store);
        }

        if(isset($_FILES['image']['name']) && $_FILES['image']['name'] != null)
        {
            $formData=array('name'=>$_FILES['image']['name']);
            Mage::helper('buyshopconfig')->fileLoad('image',$formData,'Configsets');
            //var_dump($_FILES);
            //die($_FILES['image']['name']);
            $newPath = Mage::getBaseDir().DS.'skin'.DS.'adminhtml'.DS.'base'.DS.'default'.DS.'buyshop'.DS.'images'.DS.'Configsets'.DS.$_FILES['image']['name'];
            $data['image']=Mage::helper('buyshopconfig')->cropResizeImg($_FILES['image']['name'], $newPath ,72,52);
        }

        $xml = Array2XML::createXML('config', $data);


        $file = Mage::getBaseDir().'/Configsets/'.$data['name'].'.xml';
        $content = $xml->saveXML();
        @file_put_contents($file, $content);

        Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('buyshopconfig')->__('New preset configuration saved successfully. You can use it from "Load preset configuration" '));
        $this->getResponse()->setRedirect($this->getUrl("*/*/"));



    }
}



