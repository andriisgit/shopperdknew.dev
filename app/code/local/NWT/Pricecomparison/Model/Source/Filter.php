<?php

class NWT_Pricecomparison_Model_Source_Filter
{

    const XML_PATH_FILTERS           = "nwt/pricecomparison/filters";


    protected $_filters;



    public function getFilters() 
    {
        if(!is_null($this->_filters)) {
            return $this->_filters;
        }

        $this->_filters = array();

        $node = Mage::getConfig()->getNode(self::XML_PATH_FILTERS);
        if(!$node || !($filters = $node->children())) {
            return $this->_filters;
        }

        foreach($filters as $filter) {

                if((bool)$filter->getAttribute('disabled')) continue;
                $code  = $filter->getName();
                ;
                $this->_filters[$code] = array(
                    'label'=>$filter->getAttribute('label')?$filter->getAttribute('label'):$this->humanize($filter->getName()),
                    'value'=>$code,
                    'help'=>$filter->getAttribute('help')?$filter->getAttribute('help'):(string)$filter,
                    'model'=>$filter->getAttribute('model')?$filter->getAttribute('model'):'pricecomparison/feed_filter'
                );
        }
        return $this->_filters;
    }


    public function getFilter($filter) 
    {
        $filters = $this->getFilters();
        if(empty($filters[$filter])) {
            return array();
        } else {
            return $filters[$filter];
        } 
    }



    public function toOptionArray($addEmpty = true) {

        $filters = $this->getFilters();
        $options = array();
        if($addEmpty) {
            $options[] = array(
                    'label'=>'',
                    'value'=>''
            );
        }
        foreach($filters as $filter) {
            $options[] = array(
                'label'=>$filter['label'],
                'value'=>$filter['value']
            );
        }
        return $options;
    }

    public function toOptionHash() {

        $filters = $this->getFilters();

        $return = array();
        foreach($filters as $k=>$v) {
            $return[$k] = $v['label'];
        };
        return $return;

    }

    protected function humanize($str)
    {   
        $str = strtolower(trim($str));
        $str = trim(preg_replace('/[^a-z0-9\s+]/', ' ', $str));
        $str = trim(preg_replace('/\s+/', ' ', $str));
        return ucwords($str);

    } 

}