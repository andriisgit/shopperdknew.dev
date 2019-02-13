<?php
class Etheme_Buyshopconfig_Block_Navigation extends Mage_Catalog_Block_Navigation
{
    public  $_columnOutput;
    public  $_type;
    public  $_counter;

    /**
     * Render category to html
     *
     * @param Mage_Catalog_Model_Category $category
     * @param int Nesting level number
     * @param boolean Whether ot not this item is last, affects list item class
     * @param boolean Whether ot not this item is first, affects list item class
     * @param boolean Whether ot not this item is outermost, affects list item class
     * @param string Extra class of outermost list items
     * @param string If specified wraps children list in div with this class
     * @param boolean Whether ot not to add on* attributes to list item
     * @return string
     */
    public function _renderCategoryMenuItemHtml($category,
                                                $level = 0,
                                                $isLast = false,
                                                $isFirst = false,
                                                $isOutermost = false,
                                                $outermostItemClass = '',
                                                $childrenWrapClass = '',
                                                $noEventAttributes = false)
    {
        if (!$category->getIsActive()) {
            return '';
        }
        $html = array();

        // get all children
        if (Mage::helper('catalog/category_flat')->isEnabled()) {
            $children = (array)$category->getChildrenNodes();
            $childrenCount = count($children);
        } else {
            $children = $category->getChildren();
            $childrenCount = $children->count();
        }
        $hasChildren = ($children && $childrenCount);

        // select active children
        $activeChildren = array();
        foreach ($children as $child) {
            if ($child->getIsActive()) {
                $activeChildren[] = $child;
            }
        }
        $activeChildrenCount = count($activeChildren);
        $hasActiveChildren = ($activeChildrenCount > 0);

        // prepare list item html classes
        $classes = array();
        $classes[] = 'level' . $level;
        $classes[] = 'nav-' . $this->_getItemPosition($level);
        if ($this->isCategoryActive($category)) {
            $classes[] = 'active';
        }
        $linkClass = '';
        if ($isOutermost && $outermostItemClass) {
            $classes[] = $outermostItemClass;
            $linkClass = ' class="'.$outermostItemClass.'"';
        }
        if ($isFirst) {
            $classes[] = 'first';
        }
        if ($isLast) {
            $classes[] = 'last';
        }
        if ($hasActiveChildren) {
            $classes[] = 'parent';
        }

        if($this->_type=='leftmenu')
        {
            if ($this->isCategoryActive($category)) {
                $classes[] = 'current';
            }
        }elseif($this->_type=='megamenu' && $level==1)
        {
            $classes[]='title';
        }

        // prepare list item attributes
        $attributes = array();
        if (count($classes) > 0) {
            $attributes['class'] = implode(' ', $classes);
        }
        if ($hasActiveChildren && !$noEventAttributes) {
            $attributes['onmouseover'] = 'toggleMenu(this,1)';
            $attributes['onmouseout'] = 'toggleMenu(this,0)';
        }

        if($this->_type=='megamenu' && ($level==0 or $level==1)) {
            $category_data=Mage::getModel('catalog/category')->load($category->getId());
        }

        // assemble list item with attributes

        $htmlLi = '<li';
        foreach ($attributes as $attrName => $attrValue) {
            $htmlLi .= ' ' . $attrName . '="' . str_replace('"', '\"', $attrValue) . '"';
        }
        $htmlLi .= '>';
        $html[] = $htmlLi;


        if($this->_type=='leftmenu')
        {
            $flag='closed';

            if ($this->isCategoryActive($category)) {
                $flag = 'opened';
            }
            $html[] = '<label class="tree-toggler nav-header '.$flag.'">
                                        <a href="'.$this->getCategoryUrl($category).'">'. $this->escapeHtml($category->getName()).'</a></label>';
        }else{
            $html[] = '<a href="'.$this->getCategoryUrl($category).'"'.$linkClass.'>';
            if($this->_type=='megamenu' && $level!='0')$html[] = $this->escapeHtml($category->getName());
            else $html[] = '<span>' . $this->escapeHtml($category->getName()) . '</span>';
            $html[] = '</a>';
            if($this->_type=='megamenu' && $level=='1')
            {
                $label= $category_data->getBs_category_lable();
                if(!empty($label))$html[]='<span class="hot"> '.$label.' </span>';
            }

        }

        // render children
        $htmlChildren = '';
        $j = 0;

        if($this->_type=='megamenu')
        {
            foreach ($activeChildren as $child) {
                $itemHtml = $this->_renderCategoryMenuItemHtml(
                    $child,
                    ($level + 1),
                    ($j == $activeChildrenCount - 1),
                    ($j == 0),
                    false,
                    $outermostItemClass,
                    $childrenWrapClass,
                    $noEventAttributes
                );
                $j++;
                if ($level==0 && $this->_type=='megamenu')
                {
                    $this->_columnOutput[] = $itemHtml;

                }
                else
                {
                    $htmlChildren.=$itemHtml;
                }
            }
        }
        else
        {
            foreach ($activeChildren as $child) {
                $htmlChildren .= $this->_renderCategoryMenuItemHtml(
                    $child,
                    ($level + 1),
                    ($j == $activeChildrenCount - 1),
                    ($j == 0),
                    false,
                    $outermostItemClass,
                    $childrenWrapClass,
                    $noEventAttributes
                );
                $j++;

            }
        }


        if($this->_type=='megamenu' && $level==0)
        {


            $descriptionTop=$category_data->getBs_top_html();
            $descriptionTop = Mage::helper('cms')->getBlockTemplateProcessor()->filter($this->helper('catalog/output')->categoryAttribute($category, $descriptionTop, 'bs_top_html'));
            $descriptionBtm = $category_data->getBs_btm_html();
            $descriptionBtm = Mage::helper('cms')->getBlockTemplateProcessor()->filter($this->helper('catalog/output')->categoryAttribute($category, $descriptionBtm, 'bs_btm_html'));
            $description = Mage::getModel('catalog/category')->load($category->getId())->getMenutopdescription1();
            $description = Mage::helper('cms')->getBlockTemplateProcessor()->filter($this->helper('catalog/output')->categoryAttribute($category, $description, 'menutopdescription1'));
            $cols = $category_data->getBs_count_columns();
            if(empty($cols))$cols=6;
            if($cols>6)$cols=6;
            if($cols<1)$cols=1;
            if(!empty($description) && $cols>4)$cols=4;

            if($hasActiveChildren || !empty($descriptionTop) || !empty($descriptionBtm) || !empty($description))
            {
                $htmlChildren .= '<ul class="shadow">';
                if(!empty($descriptionTop)) $htmlChildren .='<li class="row_top"><span class="inside">'. $descriptionTop.'</span></li>';
                $htmlChildren .= '<li class="row_middle">
                                    <ul class="rows_outer">';


                $c=0;
                $s=0;


                foreach ($this->_columnOutput as $item)
                {

                    $c++;$s++;
                    if($c==1)
                    {
                        $htmlChildren .= '<li><ul class="menu_row">';//begin row
                    }


                    $htmlChildren .= '<li class="col"><ul>'.$item.'</ul></li>';

                    if($c==$cols or $s==count($this->_columnOutput))
                    {
                        $htmlChildren .= '</ul></li>'; //end row
                        $c=0;
                    }
                }

                $htmlChildren .= '          </ul>';

                /*custom block html*/
                if (!empty($description))
                {
                    $htmlChildren.='<div class="custom">'.$description.'</div>';
                }
                /*end block html*/
                $htmlChildren .= '</li>';
                if(!empty($descriptionBtm)) $htmlChildren .= '<li class="row_bot"><span class=" inside">'.$descriptionBtm.'</span></li>';
                $htmlChildren .= '</ul>';
                $this->_columnOutput=array();
            }


        }

        /* }*/

        if (!empty($htmlChildren)) {
            if ($childrenWrapClass) {
                $html[] = '<div class="' . $childrenWrapClass . '">';
            }


            if($this->_type=='mobile')
            {
                $this->_counter++;
                $html[] = '<a class="icon-collapse" href="#level'.$this->_counter.'" title="" data-toggle="collapse" ><i class="icon-down pull-right"></i></a>';
                $html[] = '<ul class="collapse in level' . $level . '" id="level'.$this->_counter.'">';
            }
            elseif($this->_type=='leftmenu')
            {
                $html[] = '<ul class="nav nav-list tree">';
            }elseif($this->_type=='megamenu' && $level==1)
            {
                $html[] = '</li>';
            }
            else{
                $html[] = '<ul class="level' . $level . '">';
            }

            $html[] = $htmlChildren;

            if($this->_type=='megamenu' && $level==1)
            {
                $html[] = '';
            }else
            {
                $html[] = '</ul>';
            }

            if ($childrenWrapClass) {
                $html[] = '</div>';
            }
        }

        if(!($this->_type=='megamenu' && $level==1))$html[] = '</li>';

        $html = implode("\n", $html);
        return $html;
    }


    /**
     * Render categories menu in HTML
     *
     * @param int Level number for list item class to start from
     * @param string Extra class of outermost list items
     * @param string If specified wraps children list in div with this class
     * @return string
     */
    public function renderCategoriesMenuHtml($level = 0,
                                             $outermostItemClass = '',
                                             $childrenWrapClass = '',
                                             $type='simple')
    {

        $this->_type=$type;
        $this->_counter=1;

        $activeCategories = array();
        foreach ($this->getStoreCategories() as $child) {
            if ($child->getIsActive()) {
                $activeCategories[] = $child;
            }
        }
        $activeCategoriesCount = count($activeCategories);
        $hasActiveCategoriesCount = ($activeCategoriesCount > 0);

        if (!$hasActiveCategoriesCount) {
            return '';
        }

        $html = '';
        $j = 0;
        foreach ($activeCategories as $category) {
            $html .= $this->_renderCategoryMenuItemHtml(
                $category,
                $level,
                ($j == $activeCategoriesCount - 1),
                ($j == 0),
                true,
                $outermostItemClass,
                $childrenWrapClass,
                true
            );
            $j++;
        }

        return $html;
    }
}