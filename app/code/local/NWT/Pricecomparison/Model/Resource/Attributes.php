<?php  
class NWT_Pricecomparison_Model_Resource_Attributes{
	
    public function toOptionArray()
    {
        $attributes = Mage::getModel('catalog/product')->getAttributes();
        $attributeArray = array();
                $attributeArray[]=array(
                    'label' => "Default value",
                    'value' => ""
                );
        foreach($attributes as $a){

            foreach ($a->getEntityType()->getAttributeCodes() as $attributeName) {

                //$attributeArray[$attributeName] = $attributeName;
                $attributeArray[] = array(
                    'label' => $attributeName,
                    'value' => $attributeName
                );
            }
            break;
        }
        return $attributeArray;
    }
}
?>