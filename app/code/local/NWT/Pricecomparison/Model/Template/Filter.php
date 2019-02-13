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
 *
 */


/**
 * Wrap Template Filter Model
 *
 * Remove block, layout directive
 */
class NWT_Pricecomparison_Model_Template_Filter extends Mage_Core_Model_Email_Template_Filter
{
    /**
     * Use absolute links flag
     *
     * @var bool
     */
    protected $_useAbsoluteLinks = true;

    /**
     * Whether to allow SID in store directive: NO
     *
     * @var bool
     */
    protected $_useSessionInUrl = false;

    /**
     * Retrieve Block html directive
     * do not want this, removed it
     *
     * @param array $construction
     * @return string
     */
    public function blockDirective($construction)
    {
        return '';
    }

   /**
     * Retrieve layout html directive
     * do not want this, removed it
     *
     * @param array $construction
     * @return string
     */
    public function layoutDirective($construction)
    {
        return '';
    }


    /* From Varien_Filter_Template */

    public function includeDirective($construction)
    {
        return '';
    }



}
