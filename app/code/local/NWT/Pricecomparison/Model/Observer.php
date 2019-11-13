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
 * Observer model
 *
 */

class NWT_Pricecomparison_Model_Observer 
{
    public function generate() {
        $feeds = Mage::getResourceModel('pricecomparison/feed_collection');
        
        $success = array();
        $errors = array();
        $skipped = array();

        foreach($feeds as $feed) {
            try {
                if($feed->getStatus()>0) {
                    Mage::getModel('pricecomparison/feed_generator')->generate($feed);
                    $success[$feed->getId()] = $feed->getName();
                } else {
                    $skipped[$feed->getId()] = $feed->getName();
                } 
            } catch(Exception $e) {
                $errors[$feed->getId()] = $feed->getName();
                Mage::logException($e);
            }
        }

        return "Success: [".($success?implode(', ',$success):'-')
                ."]; Skipped (not enabled): [".($skipped?implode(', ',$skipped):'-')
                ."]; Errors (see exception.log): ".($errors?implode(', ',$errors):'-')
                ."];"
        ;

    }

}