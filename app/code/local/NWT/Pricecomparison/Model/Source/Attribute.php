<?php

class NWT_Pricecomparison_Model_Source_Attribute
{


  const XML_PATH_FILTERS           = "nwt/pricecomparison/attributes";


    protected $_attributes; //custom attributes
    protected $_options  = null;



    public function getCustomAttributes() 
    {
        if(!is_null($this->_attributes)) {
            return $this->_attributes;
        }

        $this->_attributes = array();

        $node = Mage::getConfig()->getNode(self::XML_PATH_FILTERS);
        if(!$node || !($attributes = $node->children())) {
            return $this->_attributes;
        }

        foreach($attributes as $attribute) {

                if((bool)$attribute->getAttribute('disabled')) continue;

                $code  = $attribute->getName();

                $model = trim($attribute->getAttribute('model'));
                if($model && !strpos($model,'/')) {
                    $model = null;
                } else {
                    $model = 'pricecomparison/feed_attribute';
                }

                $this->_attributes[$code] = array(
                    'label'=>$attribute->getAttribute('label')?$attribute->getAttribute('label'):$this->humanize($attribute->getName()),
                    'value'=>$code,
                    'help'=>$attribute->getAttribute('help')?$attribute->getAttribute('help'):(string)$attribute,
                    'model'=>$model
                );
        }
        return $this->_attributes;
    }


    public function getCustomAttribute($code) 
    {
        $customAttributes = $this->getCustomAttributes();
        if(!isset($customAttributes[$code])) {
            return array();
        } else {
            return $customAttributes;
        } 
    }



 

    public function toOptionArray($addEmpty = true) {

        if(!is_null($this->_options)) {
            return $this->_options;
        }
        
        
        $this->_options = array();
        if($addEmpty) {
            $this->_options[] = array(
                    'label'=>'',
                    'value'=>''
            );
        }

        $customAttributes = $this->getCustomAttributes();

        $customoptions = array();
        foreach($customAttributes as $code=>$attr) {
            $customoptions[] = array(
                'label'=>$attr['label'],
                'value'=>$code
            );
        }
        
        if($customoptions) {
            $this->_options[] = array(
                'label'=>'Special attributes',
                'value'=>$customoptions
            );
        }


        $attrOptions = array();
        $attributes = Mage::getResourceModel('catalog/product_attribute_collection');
        foreach($attributes as $attr) {
            if($attr->getAttributeCode() && !isset($customAttributes[$attr->getAttributeCode()])) {
                $attrOptions[] = array(
                    'label'=>$attr->getFrontendLabel()?$attr->getFrontendLabel():$attr->getAttributeCode(),
                    'value'=>$attr->getAttributeCode()
                );
            }
        }

        if($attrOptions) {
            $this->_options[] = array(
                'value'=>$attrOptions,
                'label'=>'Attributes',
            );
        }
        
        $magentoVersion = Mage::getVersion();
        if(version_compare($magentoVersion, '1.9.2', '<')) {
            //jsQuoteEscape was added starting with magento 1.9.2
            //echo Mage::helper('core')->jsQuoteEscape($this->_renderCellTemplate($columnName))
            //@see app/design/adminhtml/default/default/template/system/config/form/field/array.phtlm        \
            //before this we need to escape values

            foreach($this->_options as $idx=>$g) {
            
                if(empty($g['value'])) continue;
                $this->_options[$idx]['label'] = addslashes($g['label']);
                
                if(is_array($g['value'])) {
                    foreach($g['value'] as $idx2=>$opt) {
                        $this->_options[$idx]['value'][$idx2]['value'] = addslashes($opt['value']);
                        $this->_options[$idx]['value'][$idx2]['label'] = addslashes($opt['label']);
                    }
                } else {
                    $this->_options[$idx]['value'] = addslashes($g['value']);
                } 
            }
        }
        
        
        return $this->_options;
    }




    protected function humanize($str)
    {   
        $str = strtolower(trim($str));
        $str = trim(preg_replace('/[^a-z0-9\s+]/', ' ', $str));
        $str = trim(preg_replace('/\s+/', ' ', $str));
        return ucwords($str);

    } 
}