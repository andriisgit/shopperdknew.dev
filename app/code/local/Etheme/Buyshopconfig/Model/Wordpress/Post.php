<?php
/**
 * Created by JetBrains PhpStorm.
 * User: admin
 * Date: 26.05.14
 * Time: 13:36
 * To change this template use File | Settings | File Templates.
 */ 
class Etheme_Buyshopconfig_Model_Wordpress_Post extends Fishpig_Wordpress_Model_Post {
    /**
     * Retrieve the post teaser
     * This is the data from the post_content field upto to the MORE_TAG
     *
     * @return string
     */
    protected function _getPostTeaser($includeSuffix = true)
    {
        if ($this->hasMoreTag()) {
            $content = $this->getPostContent('excerpt');

            if (preg_match('/<!--more (.*)-->/', $content, $matches)) {
                $anchor = $matches[1];
                $split = $matches[0];
            }
            else {
                $split = '<!--more-->';
                $anchor = $this->_getTeaserAnchor();
            }

            $excerpt = trim(substr($content, 0, strpos($content, $split)));

            if ($excerpt !== '' && $includeSuffix && $anchor) {
                $excerpt .= sprintf(' <a href="%s" class="button">%s</a>', $this->getPermalink(), $anchor);
            }

            return $excerpt;
        }

        return null;
    }
}