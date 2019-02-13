<?php
class NWT_KCO_Model_System_Config_Source_Customertype {


    public function toOptionArray()
    {
        $options = array();
        $options[] = array(
               'value' => 'person',
               'label' => Mage::helper('nwtkco')->__('B2C')
            );
        $options[] = array(
               'value' => 'organization',
               'label' => Mage::helper('nwtkco')->__('B2B')
        );

        return $options;
    }

}
