<?php
/**
 * @version   1.0 14.08.2012
 * @author    TonyEcommerce http://www.TonyEcommerce.com <support@TonyEcommerce.com>
 * @copyright Copyright (c) 2012 TonyEcommerce
 */
include('app/Mage.php');
$app = Mage::app('default');
		
 $tags = Mage::getModel('tag/tag')->getPopularCollection()
                ->joinFields(Mage::app()->getStore()->getId())
                ->limit(20)
                ->load()
                ->getItems();
?>
<tags>

<?php  foreach ($tags as $tag) { ?>
	<a href="<?php echo str_replace('tagflash.php', 'index.php', $tag->getTaggedProductsUrl()); ?>" class="tag-link-200" title="1" rel="tag" style="font-size: 20pt;" color="0x<?php echo str_replace('#','',Mage::getStoreConfig('buyshopcolors/general/link_color'))?>"><?php echo $tag->getName(); ?></a>
<?php  } ?>
</tags>


