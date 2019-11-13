<?php
/**
 * @version   1.0 14.08.2012
 * @author    TonyEcommerce http://www.TonyEcommerce.com <support@TonyEcommerce.com>
 * @copyright Copyright (c) 2012 TonyEcommerce
 */

class Etheme_Buyshopflex_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function fileLoad($name, &$formData,$pathModule,$id,$width,$height)
    {
        if(isset($_FILES[$name]['name']) && $_FILES[$name]['name'] != null)
        {
            $fileUploader = new Varien_File_Uploader($name);
            $fileUploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
            $fileUploader->setAllowRenameFiles(false);
            $fileUploader->setFilesDispersion(false);
            $path = Mage::getBaseDir('media') . DS.$pathModule.DS ;
            $fileUploader->save($path, $_FILES[$name]['name'] );
            $formData[$name] = $pathModule. '/' .$_FILES[$name]['name'];
        }
    }


    public function createPreview($name,$pathModule,$id,$width,$height)
    {
        if(isset($_FILES[$name]['name']) && $_FILES[$name]['name'] != null)
        {
            $path = Mage::getBaseDir('media') . DS.$pathModule.DS ;
            $imageObj = new Varien_Image($path.'/'.$_FILES[$name]['name']);
            $imageObj->constrainOnly(TRUE);
            $imageObj->keepAspectRatio(FALSE);
            $imageObj->keepFrame(FALSE);
            $currentRatio = $imageObj->getOriginalWidth() / $imageObj->getOriginalHeight();
            $targetRatio = $width / $height;

            if ($targetRatio > $currentRatio) {
                $imageObj->resize($width, null);
            } else {
                $imageObj->resize(null, $height);
            }

            $diffWidth  = $imageObj->getOriginalWidth() - $width;
            $diffHeight = $imageObj->getOriginalHeight() - $height;

            //$imageObj->resize($width, $height);
            $imageObj->crop(
                floor($diffHeight * 0.5),
                floor($diffWidth / 2),
                ceil($diffWidth / 2),
                ceil($diffHeight * 0.5)
            );


            if(empty($id))
            {
                $id = Mage::getModel('buyshopflex/buyshopflex')
                    ->getCollection()
                    ->addOrder('slide_id', 'ASC')->getLastItem()->toArray();
                $id=$id['slide_id'];
            }


            $userfile_extn = explode(".", strtolower($_FILES[$name]['name']));
            $imageObj->save($path.'preview_'.$id.'.'.$userfile_extn[1]);

        }
    }

    public function getPreview($slide_id, $direction='next')
    {
        $data_slides  = Mage::getModel('buyshopflex/buyshopflex')->getCollection()
            ->addFieldToSelect('*')
            ->addFieldToFilter('status', 1);

        /*STORE FILTER*/
        $data=$data_slides->toArray();
        $filtered_data=array();
        if(count($data['items']))
        foreach($data['items'] as $slide)
        {
            $stores=explode(',',$slide['store_id']);
            if(in_array(Mage::app()->getStore()->getStoreId(),$stores) || in_array(0,$stores))$filtered_data[]=$slide;
        }
        $data_slides=$filtered_data;
        /*STORE FILTER END*/

        $data=array();

        foreach($data_slides as $slide)
        {
            $data[$slide['slide_id']]=$slide['image'];
        }

        if($direction=='next')
        {
            while(key($data)!=$slide_id)next($data);
            $current=key($data);
            if(next($data))
            {
                $out=key($data);
            } else
            {
                reset($data);
                $out=key($data);
            }
        }

        if($direction=='prev')
        {
            while(key($data)!=$slide_id)next($data);
            if(prev($data))
            {
                $out=key($data);
            } else
            {
                reset($data);
                for($i=1;$i<count($data);$i++)next($data);
                $out=key($data);
            }
        }
        $path= Mage::getBaseUrl('media').'etheme/buyshop/buyshopflex/preview_'.$out.'.jpg';
        return $path;
    }


}