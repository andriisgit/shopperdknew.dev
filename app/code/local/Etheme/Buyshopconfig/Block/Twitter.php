<?php
/**
 * @version   1.0 14.08.2012
 * @author    TonyEcommerce http://www.TonyEcommerce.com <support@TonyEcommerce.com>
 * @copyright Copyright (c) 2012 TonyEcommerce
 */

class Etheme_Buyshopconfig_Block_Twitter extends Mage_Core_Block_Template
{

    /*protected function _construct()
    {
        parent::_construct();

        $this->addData(array(
            'cache_lifetime'    => 86400,
            'cache_tags'        => array(Mage_Catalog_Model_Product::CACHE_TAG),
        ));
    }
*/
    function getTwitts()
    {
        if(Mage::getStoreConfig('facebookfree/settings/tw_access_token') && Mage::getStoreConfig('facebookfree/settings/tw_access_token_secret') && Mage::getStoreConfig('facebookfree/settings/twappid') && Mage::getStoreConfig('facebookfree/settings/twsecret'))
        {
            $ExternalLibPath=Mage::getModuleDir('', 'Etheme_Buyshopconfig') . DS . 'lib' . DS .'TwitterAPIExchange.php';
            require_once ($ExternalLibPath);
            $settings = array(
                'oauth_access_token' => Mage::getStoreConfig('facebookfree/settings/tw_access_token'),
                'oauth_access_token_secret' => Mage::getStoreConfig('facebookfree/settings/tw_access_token_secret'),
                'consumer_key' => Mage::getStoreConfig('facebookfree/settings/twappid'),
                'consumer_secret' => Mage::getStoreConfig('facebookfree/settings/twsecret')
            );
            $url = 'https://api.twitter.com/1.1/statuses/user_timeline.json';
            $getfield = '?screen_name='.Mage::getStoreConfig('buyshopconfig/social_accounts/twitter_account');
            $requestMethod = 'GET';

            $twitter = new TwitterAPIExchange($settings);
            $response = $twitter->setGetfield($getfield)
                ->buildOauth($url, $requestMethod)
                ->performRequest();
            return (json_decode($response));
        } else return array();
    }

}
