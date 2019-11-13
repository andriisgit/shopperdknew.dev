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
 * Feed  Generator
 *
 */

class NWT_Pricecomparison_Model_Feed_Generator {

    
    const XML_DEFAULT_CONFIG_PATH  = 'pccustom/defaultmenu/%s';
    const XML_WRAP_TEMPLATE_FILTER = 'nwt/pricecomparison/wrap/template_filter';


    protected $_attributes;
    protected $_product;

    protected $_templateModel;
    protected $_attributeValueName;


   /**
     * Retrieve Template processor for wrap collumn
     *
     * @return Varien_Filter_Template
     */
    public function getWrapTemplateProcessor()
    {
        if(is_null($this->_templateModel)) {
            $model = (string)Mage::getConfig()->getNode(self::XML_WRAP_TEMPLATE_FILTER);
            if($model) {
                try {
                    $this->_templateModel =  Mage::getModel($model);
                } catch(Exception $e) {
                    $this->_templateModel =  null;
                    Mage::logException($e);
                } 
            }
            if(is_null($this->_templateModel)) {
                $this->_templateModel =  Mage::getModel('pricecomparison/template_filter');
            }
        }
        return $this->_templateModel;
    }


   /**
     * Get all CSV columns
     *
     * @return array
     */

    protected function _getAttributes() {

        if(is_null($this->_attributes)) {

            $customAttributes = Mage::getSingleton('pricecomparison/source_attribute')->getCustomAttributes();
            $filters          = Mage::getSingleton('pricecomparison/source_filter')->getFilters();

            $models = array();

            foreach($this->_fields as $options) {

                    if(empty($options['attribute'])) {
                        continue;
                    }
                    $field = trim($options['attribute']);

                    $oAttribute = new Varien_Object(array('field_code'=>$field));
                    $oAttribute->setOptions($options);
                    
                    if(!empty($options['label'])) {
                        $oAttribute->setLabel($options['label']); 
                    } else {
                        $oAttribute->setLabel($field); 
                    } 


                    if(!empty($customAttributes[$field]['model'])) {

                        $modelName = trim($customAttributes[$field]['model']);

                        $model  = null;
                        if(!isset($models[$modelName])) {
                            try {
                                $model = Mage::getModel($modelName);
                                $models[$modelName] = $model;
                            } catch(Exception $e) {
                                $model = null;
                                Mage::logException($e);
                                //idiot programmer
                            }
                        } else {
                            $model = $models[$modelName];
                        }
                        if($model) {

                            $method = 'get_'.$field;
                            $callable = array($model,$method);
                            if(is_callable($callable)) {
                                $oAttribute->setGetter($callable);
                            } else{
                                Mage::logException(new Exception('['.$field.'] Method '.get_class($model).'::'.$method.' does not exists'));
                            } 
                        }


                    }



                    if(!empty($options['filter'])) {
                            $filter = trim($options['filter']);
                            if(!empty($filters[$filter]['model'])) {
                                $modelName = $filters[$filter]['model'];

                                $model  = null;
                                if(!isset($models[$modelName])) {
                                    try {
                                        $model = Mage::getSingleton($modelName);
                                        $models[$modelName] = $model;
                                    } catch(Exception $e) {
                                        Mage::logException($e);
                                        //idiot programmer
                                    }
                                } else {
                                    $model = $models[$modelName];
                                }
                                if($model) {
                                    $method = 'filter_'.$filter;
                                    $callable = array($model,$method);
                                    if(is_callable($callable)) {
                                        $oAttribute->setFilter($callable);
                                    } 
                                }

                            }
                    }

                    if(!empty($options['params'])) {
                        $oAttribute->setParams(explode(',',trim($options['params'])));
                    } else {
                        $oAttribute->setParams(array());
                    }
                    if(!empty($options['default'])) {
                        $oAttribute->setDefault(trim($options['default']));
                    }
                    if(!empty($options['max'])) {
                        $oAttribute->setMax(abs((int)$options['max']));
                    }
                    if(!empty($options['wrap'])) {
                        $oAttribute->setWrap(trim($options['wrap']));
                    }
                    if(!empty($options['oneline'])) {
                        $oAttribute->setOneline(trim($options['oneline']));
                    }


                    $attribute  = null;

                    $attribute = Mage::getModel('catalog/resource_eav_attribute')->loadByCode(Mage_Catalog_Model_Product::ENTITY, $field);
                    if($attribute && $attribute->getId()) {
                        $oAttribute->setCatalogAttribute($attribute);
                    }
                    if($oAttribute->getDefault()) {
                        $oAttribute->setDefault(trim(Mage::getStoreConfig(sprintf(self::XML_DEFAULT_CONFIG_PATH,$field))));
                    }

                    $this->_attributes[] = $oAttribute;
            }
            //Mage::log($this->__removeObj($this->_attributes));
        }

        return $this->_attributes;
    }



    public function getProductAttributes() {

        $return = Mage::getSingleton('catalog/config')->getProductAttributes();

        $_attrs = $this->_getAttributes();

        foreach($_attrs as $_attr) {
            $return[] = $_attr->getAttributeCode();
        }
        return array_unique($return);
    }


    public function getColumn($attribute,$product) {

        if(!$product || !$product->getId()) {
            return '';
        }

        
        $field        = $attribute->getFieldCode();
        $catAttribute = $attribute->getCatalogAttribute();
        
        

        $val = null;


        if(($getter  = $attribute->getGetter())) { // =!== 
            $val = call_user_func_array($getter,array($product,$attribute));
        } elseif($catAttribute && $catAttribute->usesSource() && ($source  = $catAttribute->getSource())) { // =!==
            $val = implode('; ',(array)$source->getOptionText($product->getData($field)));
        } else {
            //try to get field value
            $val = $product->getDataUsingMethod($field);
            if(!is_scalar($val)) { //for example group price is array, I don't know to handle this
                //$val = $product->getData($field);
                $val = '';
            }
        }

        if(!$val) {
            $val = $attribute->getDefault();
        }
        
        

        if(($filter  = $attribute->getFilter())) { // =!==
            $params = $attribute->getParams()?$attribute->getParams():array();
            array_unshift($params,$val); //add $val as first param
            $val = call_user_func_array($filter,$params);
        }


        if($val && is_string($val) && $attribute->getMax()>0) {
            $val = Mage::helper('core/string')->truncate($val,$attribute->getMax());
        }

        if($val && is_string($val) && $attribute->getOneline()>0) {
            $val = preg_replace('/[\n\r]+/', ' ', trim($val));
        }

        if(($wrap = $attribute->getWrap())) { // =!==

            //replace placeholder with real value
            //$val = str_replace('$val',addslashes($val),$wrap);
            if(strpos($wrap,'{{') !== false && strpos($wrap,'{{') !== false ) {
                $val = $this->getWrapTemplateProcessor()
                        ->setVariables(array(
                            'val'=>$val,
                            'product'=>$product,
                            'attribute'=>$attribute
                        ))
                        ->filter($wrap);
            }
        }

        return $val;
    }


    public function getHead() {
        $return = array();

        if($attributes = $this->_getAttributes()) { // = != ==
            foreach($attributes as $attr) {
                $return[] = $attr->getLabel();
            }
        }
        return $return;
    }

    public function getRow($product) {

        $return = array();
        if($attributes = $this->_getAttributes()) { // = != ==
            foreach($attributes as $attr) {
                $return[] = $this->getColumn($attr,$product);
            }
        }
        return $return;
    }



    public function generate($feed) {

        if(!($feed instanceof NWT_Pricecomparison_Model_Feed) || !$feed->getId()) {
            Mage::throwException(Mage::helper('pricecomparison')->__('[%s] Invalid call',__METHOD__));
        }


        $filename   = $feed->getFilename();
        $attributes = $feed->getAttributes();
        if(!is_array($attributes)) {
            $attributes = $attributes?json_decode($attributes,true):array();
        }
        $store      = (int)$feed->getStoreId();

        $missing = array();
        if(!$filename) {
            $missing[] = 'filename';
        }
        if(!$attributes) {
            $missing[] = 'attributes';
        }
        if($store<=0) {
            $missing[] = 'store';
        }
        if($missing) {
            Mage::throwException(Mage::helper('pricecomparison')->__('Invalid feed, missing [%s] field(s)!',implode(', ',$missing)));
        }


        $this->_fields = $attributes;

        $delimiter = $feed->getCsvSeparator()?chr($feed->getCsvSeparator()):'|';
        $enclosure = $feed->getCsvEnclosure()?chr($feed->getCsvEnclosure()):'"';
        if($delimiter == $enclosure) {
            $delimiter = '|';
            $enclosure = '"';
        }



        $currentStore = Mage::app()->getStore();
        Mage::app()->setCurrentStore($store);


        


        $exception = null;
        $fp        = null;
        
        try {
            $file = Mage::helper('pricecomparison')->getFeedPath($filename);
            $fp = fopen($file, 'w');
            if(!$fp) {
                Mage::throwException( Mage::helper('pricecomparison')->__('Cannot create %s file.',str_replace(Mage::getBaseDir().DS,'',$file)));
            }

            if($feed->getCsvHeader()) {
               fputcsv($fp,$this->getHead(),$delimiter,$enclosure);
            }


            $store = Mage::app()->getStore();

            $collection =  Mage::getResourceModel('pricecomparison/catalog_product_collection')
                ->setStoreId($store)
                ->addAttributeToSelect('*')
                ->addStoreFilter($store)
                ->addCategoryIds()
                ->addPriceData() //this filter OUT disabled/out of stock products, but we rewrite catalog_product_collection to use LEFT join, see NWT_Pricecomparison_Model_Resource_Catalog_Product_Collection    
                ->addTaxPercents()
                ->addUrlRewrite(0)
            ;

            $disabled        = ((int)$feed->getListDisabled()>0);

            if(!$disabled) { //if not list disabled, set enabled filter
                $collection->addAttributeToFilter('status', Mage_Catalog_Model_Product_Status::STATUS_ENABLED);
            }

            $visibility = $feed->getListVisibility();
            if($visibility && !is_array($visibility)) {
                $visibility = explode(',',$visibility);
            }

            if($visibility) {
                //$collection->setVisibility($visibility); //NOT used, this will filter out disabled/out of stock products (INNER JOIN with catalog_category_product_index)
                $collection->addAttributeToFilter('visibility',$visibility); //this will use LEFT join with catalog_product_entity_int
            }

            $types = $feed->getListTypes();
            if($types && !is_array($types)) {
                $types = explode(',',$types);
            }
            if($types) { 
                $collection->addFieldToFilter('type_id',array('in'=>$types));
            }

            

            //Mage::getSingleton('cataloginventory/stock_item')->addCatalogInventoryToProductCollection($collection);
            $collection->addCatalogInventory(); //custom joins, to add also qty (along is_saleable/is_in_stock) @see NWT_Pricecomparison_Model_Resource_Catalog_Product_Collection

            if((int)$feed->getListOutofstock()<=0) { //if NOT list out of stock
                //NOT use this, this will filter out disabled products
                //Mage::getSingleton('cataloginventory/stock')->addInStockFilterToCollection($collection);
                
                //instead, use addCatalogInventoryToProductCollection and filter by is_saleable
                $collection->addAttributeToFilter('is_saleable',1);
    
            }

            //exit($collection->getSelect());

            $collection->setPageSize(100);

            $pages = $collection->getLastPageNumber();
            $currentPage = 1;

            do {
                $collection->setCurPage($currentPage)->load();

                //Mage::getSingleton('cataloginventory/stock')->addItemsToProducts($collection);  //don't need this (?)

                foreach ($collection as $product) {
                    $data = $this->getRow($product);
                    fputcsv($fp,$data,$delimiter,$enclosure);
                }
                $currentPage++;
                //clear collection and free memory
                $collection->clear();
            } while ($currentPage <= $pages);


        } catch(Exception $e) {
            $exception = $e;
        }

        if($fp) {
            fclose($fp);
        }
        if(!is_null($currentStore)) {
            Mage::app()->setCurrentStore($currentStore);
        }

        if($exception) {
            Mage::throwException($exception);
        }


    }

 


    private function __removeObj($data,$recursion = true) {

        if(!is_array($data) || !($data instanceof Varien_Data_Collection)) {
            return $data;
        }


        foreach($data as $idx=>$val) {
            if(is_object($val)) {
                
                if($recursion && ($val instanceof Varien_Object)) {
                    $data[$idx] = array_merge(array('__class'=>get_class($val)),$this->__removeObj($val->getData(),$recursion));
                } else {
                    $data[$idx] = '__'.get_class($val);
                } 
            }
        }

        return $data;
    }


  

}
