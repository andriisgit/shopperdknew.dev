<?php class Queldorei_Shoppercategories_Model_Css extends Mage_Core_Model_Abstract
{	
	private $_css_file;
	private $_css_path;
	private $_css_template_path;
	
	public function _construct()
	{
		$baseDir = Mage::getBaseDir();
		$this->_css_file = 'store-%STORE%_category-%CAT%.css';
		$this->_css_path = $baseDir . '/skin/frontend/shopper/default/css/categories/';
		$this->_css_template_path = $baseDir . '/app/code/local/Queldorei/Shoppercategories/css/css.php';
	}
	
	public function regenerateCatCss($data)
	{		
		$storeviews = $data['stores'];
			
		$filepath = $this->_css_path;
		$this->removeOldShemeFiles($storeviews, $this->shemeStoresFromTable($data['sheme_id']), $data['category_id']);				
		if($data['status'] == 1)
		{
			foreach ($storeviews as $_storeview) 
			{				
				ob_start();
				require($this->_css_template_path);
				$css = ob_get_clean();				
				$filename = str_replace('%CAT%', $data['category_id'], str_replace('%STORE%', $_storeview, $this->_css_file));
				try
				{					
					$file = new Varien_Io_File();					
					$file->setAllowCreateFolders(true)->open(array('path' => $this->_css_path));
					$file->streamOpen($filename, 'w+');
					$file->streamWrite($css);
					$file->streamClose();
				}
				catch (Exception $e) 
				{					
					Mage::getSingleton('adminhtml/session')->addError('Css generation error: %s', $this->_css_path . $filename . '<br/>' . $e->getMessage());
					Mage::logException($e);
				}
			}
		}		
	}
	
	protected function removeOldShemeFiles($newStoreviews, $oldStoreviews, $catId)
	{		
		if(!empty($oldStoreviews) && !empty($newStoreviews) && $catId)
		{			
			foreach($oldStoreviews as $oldStoreview)
			{
				$aFlag = false;
				foreach($newStoreviews as $newStoreview)
				{
					if($newStoreview == $oldStoreview)
					{						
						$aFlag = true;
						break;
					}
				}
				if(!$aFlag)
				{
					$this->deleteFile($this->_css_path . str_replace('%CAT%', $catId, str_replace('%STORE%', $oldStoreview, $this->_css_file)));
				}
			}
		}				
	}
	
	public function shemeStoresFromTable($shemeId)
	{
		$result = array();
		$stores = Mage::getModel('shoppercategories/shoppercategoriesstores')->getCollection()->addFieldToSelect('store_id')->addFieldToFilter('scheme_id', $shemeId)->getData();				
		if(!empty($stores))
		foreach($stores as $store)
		{
			foreach($store as $elem)
			{
				$result[] = $elem;
			}
		}		
		return $result;
	}
	
	public function removeAllShemeFiles($shemeId, $catId)
	{		
		if($shemeId && $catId)
		{			
			$shemeStores = $this->shemeStoresFromTable($shemeId);			
			if(!empty($shemeStores))
			{				
				foreach($shemeStores as $shemeStore)
				{
					$this->deleteFile($this->_css_path . str_replace('%CAT%', $catId, str_replace('%STORE%', $shemeStore, $this->_css_file)));
				}				
			}
		}
	}	
	
	protected function deleteFile($filepath)
	{
		try
		{			
			$file = new Varien_Io_File();
			$file->rm($filepath);
		}
		catch (Exception $e) 
		{					
			Mage::getSingleton('adminhtml/session')->addError('Directory clearing error: %s', $filepath . '<br/>' . $e->getMessage());
			Mage::logException($e);
		}
	}
}
?>