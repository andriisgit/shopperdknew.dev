<?php
/**
 * @version   1.0 12.0.2012
 * @author    Queldorei http://www.queldorei.com <mail@queldorei.com>
 * @copyright Copyright (C) 2010 - 2012 Queldorei
 */

class Queldorei_Shoppercategories_Model_Mysql4_Shoppercategoriesstores extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the scheme_id refers to the key field in your database table.
        $this->_init('shoppercategories/scheme_store', 'scheme_id');
    }
}