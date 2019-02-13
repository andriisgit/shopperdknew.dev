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
 * Feed  source model
 *
 */
class  NWT_Pricecomparison_Model_Source_Feed {


    const XML_PATH_FEEDS           = "nwt/pricecomparison/feeds";


    protected $_feedTypes;



    public function getFeeds() 
    {
        if(!is_null($this->_feedTypes)) {
            return $this->_feedTypes;
        }

        $this->_feedTypes = array();

        $node = Mage::getConfig()->getNode(self::XML_PATH_FEEDS);
        if(!$node || !($feeds = $node->children())) {
            return $this->_feedTypes;
        }

        foreach($feeds as $feed) {

            if(!($attrs = $feed->children()) || (bool)$feed->getAttribute('disabled')) continue;

            $code      = $feed->getName();
            $label     = $feed->getAttribute('label');

             if(!$label) {
                $label = $this->humanize($code);
            }


            $this->_feedTypes[$code] = array(
                'label'=>$label,
                'attributes'=>array()
            );

            $i=0;

            foreach($attrs as $attr) {

                if((bool)$attr->getAttribute('disabled')) continue;

                $i += 10;

                $attribute = trim($attr->getAttribute('attribute'));
                if(!$attribute) {
                    $attribute = $attr->getName();
                }

                $this->_feedTypes[$code]['attributes']["_pc{$i}"] = array(
                    'label'     =>trim($attr->getAttribute('label')),
                    'attribute' => $attribute,
                    'default'   =>trim($attr->getAttribute('default')),
                    'filter'    =>trim($attr->getAttribute('filter')),
                    'params'    =>trim($attr->getAttribute('params')),
                    'max'       =>trim($attr->getAttribute('max')),
                    'wrap'      =>trim($attr->getAttribute('wrap')),
                    'oneline'   =>trim($attr->getAttribute('oneline'))
                );

            }
            if(!$i) {
                unset($this->_feedTypes[$code]);
            } 

        }
        
        return $this->_feedTypes;
    }


    public function toOptionArray() {


        $feeds = $this->getFeeds();

        $return = array();

        foreach($feeds as $k=>$v) {
            $return[] = array(
                'label'=>$v['label'],
                'value'=>$k
            );
        };

        return $return;
    }

    public function toOptionHash() {

        $feeds = $this->getFeeds();

        $return = array();
        foreach($feeds as $k=>$v) {
            $return[$k] = $v['label'];
        };
        return $return;

    }

    public function getFeed($feed) {

        $feeds = $this->getFeeds();
        if(empty($feeds[$feed])) {
            return array();
        }
        return $feeds[$feed];
    }


    protected function humanize($str)
    {   
        $str = strtolower(trim($str));
        $str = trim(preg_replace('/[^a-z0-9\s+]/', ' ', $str));
        $str = trim(preg_replace('/\s+/', ' ', $str));
        return ucwords($str);

    } 


}
