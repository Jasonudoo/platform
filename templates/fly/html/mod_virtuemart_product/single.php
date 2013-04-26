<?php // no direct access
defined('_JEXEC') or die('Restricted access');
?>
<div class="vmgroup<?php echo $params->get( 'moduleclass_sfx' ) ?>">

<?php if ($headerText) { ?>
	<div class="vmheader"><?php echo $headerText ?></div>
<?php } ?>

<div class="vmproduct<?php echo $params->get('moduleclass_sfx'); ?>">
<?php foreach ($products as $product) { ?>
	<div style="text-align:center;"><div class="spacer">

			<div class="product_info" style="text-align:left">
				<div class="product_name"><?php echo JHTML::link($product->link, $product->product_name); ?></div>
					<?php // Product Short Description
					if(!empty($product->product_s_desc)) { ?>
					<p class="product_s_desc">
					<?php echo shopFunctionsF::limitStringByWord($product->product_s_desc, 120, '...') ?>
					</p>
					<?php } ?>
			</div>
			<div class="clear"></div>
			<div class="floatleft">
				<?php if (!empty($product->images[0]) ) $image = $product->images[0]->displayMediaThumb('class="featuredProductImage" border="0"',false) ;
				else $image = '';
				echo JHTML::_('link', JRoute::_('index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id='.$product->virtuemart_product_id.'&virtuemart_category_id='.$product->virtuemart_category_id),$image,array('title' => $product->product_name) );
				echo '<div class="clear"></div>'; ?>
			</div>
			<div class="product-sb">
				<div class="product-price marginbottom12" id="productPrice<?php echo $product->virtuemart_product_id ?>">
					<?php  if ($show_price) {
						if( $product->product_unit && VmConfig::get('vm_price_show_packaging_pricelabel')) {
							echo "<strong>". JText::_('COM_VIRTUEMART_CART_PRICE_PER_UNIT').' ('.$product->product_unit."):</strong>";
						}

						echo $currency->createPriceDiv('variantModification','COM_VIRTUEMART_PRODUCT_VARIANT_MOD',$product->prices);
						echo $currency->createPriceDiv('basePriceWithTax','COM_VIRTUEMART_PRODUCT_BASEPRICE_WITHTAX',$product->prices);
						echo $currency->createPriceDiv('discountedPriceWithoutTax','COM_VIRTUEMART_PRODUCT_DISCOUNTED_PRICE',$product->prices);
						echo $currency->createPriceDiv('salesPriceWithDiscount','COM_VIRTUEMART_PRODUCT_SALESPRICE_WITH_DISCOUNT',$product->prices);
						 if($product->prices[salesPrice] != $product->prices[priceWithoutTax]) {
							echo $currency->createPriceDiv('priceWithoutTax', 'COM_VIRTUEMART_PRODUCT_SALESPRICE_WITHOUT_TAX', $product->prices);
							echo $currency->createPriceDiv('salesPrice', 'COM_VIRTUEMART_PRODUCT_SALESPRICE', $product->prices);        
						} else {
							echo $currency->createPriceDiv('salesPrice', 'COM_VIRTUEMART_PRODUCT_SALESPRICE', $product->prices);        
							echo $currency->createPriceDiv('priceWithoutTax', '', '');
						}
						echo $currency->createPriceDiv('taxAmount','COM_VIRTUEMART_PRODUCT_TAX_AMOUNT',$product->prices);
					} ?>
				</div>
				<div class="clear"></div>
				<div>
					<?php // Product Details Button
					echo JHTML::link ( JRoute::_ ( 'index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id=' . $product->virtuemart_product_id . '&virtuemart_category_id=' . $product->virtuemart_category_id ), JText::_ ( 'COM_VIRTUEMART_PRODUCT_DETAILS' ), array ('title' => $product->product_name, 'class' => 'product-details' ) );
					?>
				</div>
				<div class="clear"></div>
				<div>
					<?php
					if ($show_addtocart) { ?>
					<div>
					<form method="post" class="product js-recalculate" action="index.php" >
						<div class="addtocart-bar">
						<?php // Display the quantity box 

						$stockhandle = VmConfig::get('stockhandle', 'none');
						if (($stockhandle == 'disableit' or $stockhandle == 'disableadd') and ($product->product_in_stock - $product->product_ordered) < 1) {
						?>
							<a href="<?php echo JRoute::_('index.php?option=com_virtuemart&view=productdetails&layout=notify&virtuemart_product_id='.$product->virtuemart_product_id); ?>"><?php echo JText::_('COM_VIRTUEMART_CART_NOTIFY') ?></a>
						<?php } else { ?>
							<!-- <label for="quantity<?php echo $product->virtuemart_product_id; ?>" class="quantity_box"><?php echo JText::_('COM_VIRTUEMART_CART_QUANTITY'); ?>: </label> -->
							<input type="hidden" class="quantity-input js-recalculate" name="quantity[]" value="1"/>
							<?php // Display the quantity box END ?>
							<?php
								// Display the add to cart button
							?>
							<span class="addtocart-button">
								<input type="submit" name="addtocart"  class="addtocart-button" value="<?php echo JText::_('COM_VIRTUEMART_CART_ADD_TO') ?>" title="<?php echo JText::_('COM_VIRTUEMART_CART_ADD_TO') ?>" />
							</span>
						<?php } ?>
						<div class="clear"></div>
						</div>
						<?php // Display the add to cart button END  ?>
						<input type="hidden" class="pname" value="<?php echo $product->product_name ?>" />
						<input type="hidden" name="option" value="com_virtuemart" />
						<input type="hidden" name="view" value="cart" />
						<noscript><input type="hidden" name="task" value="add" /></noscript>
						<input type="hidden" name="virtuemart_product_id[]" value="<?php echo $product->virtuemart_product_id ?>" />
						<?php /** @todo Handle the manufacturer view */ ?>
						<input type="hidden" name="virtuemart_manufacturer_id" value="<?php echo $product->virtuemart_manufacturer_id ?>" />
						<input type="hidden" name="virtuemart_category_id[]" value="<?php echo $product->virtuemart_category_id ?>" />
					</form>
					</div>
					<?php }
					?>
				</div>
			</div>
	<div class="clear"></div>		
	<div class="horizontal-separator"></div>
 </div></div>

	<?php } ?>
<?php if ($footerText) { ?>
	<div class="vmheader"><?php echo $footerText ?></div>
<?php } ?>
</div>
</div>