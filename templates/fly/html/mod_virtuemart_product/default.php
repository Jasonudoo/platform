<?php // no direct access
defined ('_JEXEC') or die('Restricted access');
$col = 1;
$pwidth = ' width' . floor (100 / $products_per_row);
if ($products_per_row > 1) {
	$float = "floatleft";
} else {
	$float = "center";
}
?>
<div class="vmgroup<?php echo $params->get ('moduleclass_sfx') ?>">

	<?php if ($headerText) { ?>
	<div class="vmheader"><?php echo $headerText ?></div>
	<?php
}
	if ($display_style == "div") {
		?>
		<div class="vmproduct<?php echo $params->get ('moduleclass_sfx'); ?>">
			<?php foreach ($products as $product) { ?>
			<div class="<?php echo $pwidth ?> <?php echo $float ?>">
				<div class="spacer">
					<div class="name_prod">
					<?php
					$url = JRoute::_ ('index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id=' . $product->virtuemart_product_id . '&virtuemart_category_id=' .
						$product->virtuemart_category_id); ?>
					<a href="<?php echo $url ?>"><?php echo $product->product_name ?></a>        <?php    echo '<div class="clear"></div>';
					?>
					</div>
					<div class="img_prod">
					<?php
					if (!empty($product->images[0])) {
						$image = $product->images[0]->displayMediaThumb ('class="featuredProductImage" border="0"', FALSE);
					} else {
						$image = '';
					}
					echo JHTML::_ ('link', JRoute::_ ('index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id=' . $product->virtuemart_product_id . '&virtuemart_category_id=' . $product->virtuemart_category_id), $image, array('title' => $product->product_name));
					?>
					</div>
					<div class="clear"></div>
					<?php
					if ($show_price) {
						echo "<div class='bPrice'>";
							echo $currency->createPriceDiv ('basePrice', '', $product->prices, TRUE);
						echo "</div>";
						echo "<div class='sPrice'>";
							echo $currency->createPriceDiv ('salesPrice', '', $product->prices, TRUE);
						echo "</div>";
					}
					if ($show_addtocart) {
						echo mod_virtuemart_product::addtocart ($product);
					}
					?>
				</div>
			</div>
			<?php
			if ($col == $products_per_row && $products_per_row && $col < $totalProd) {
				echo "	</div><div style='clear:both;'>";
				$col = 1;
			} else {
				$col++;
			}
		} ?>
		</div>
		<br style='clear:both;'/>

<?php /*******************************************************************************/
} else {
$last = count($products)-1;
?>
<?php if ($headerText) { ?>
	<div class="heading">
		<div class="title">
			<?php echo $headerText ?>
		</div>
	</div>
<?php } ?>
<ul class="vmproduct<?php echo $params->get('moduleclass_sfx'); ?>">
<?php foreach ($products as $product) : ?>
	<li class="<?php echo $pwidth ?> <?php echo $float ?>">
		<?php $url = JRoute::_('index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id='.$product->virtuemart_product_id.'&virtuemart_category_id='. $product->virtuemart_category_id); ?>
		<a class="product_name" href="<?php echo $url ?>"><?php echo $product->product_name ?></a>
	</li>
<?php
	if ($col == $products_per_row && $products_per_row && $last ) {
		$col= 1 ;
	} else {
		$col++;
	}
	$last--;
	endforeach; ?>
</ul><div class="clear"></div>

<?php }
	if ($footerText) : ?>
	<div class="vmfooter<?php echo $params->get( 'moduleclass_sfx' ) ?>">
		 <?php echo $footerText ?>
	</div>
<?php endif; ?>
</div>
