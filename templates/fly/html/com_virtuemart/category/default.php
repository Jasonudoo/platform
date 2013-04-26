<?php
/**
*
* Show the products in a category
*
* @package	VirtueMart
* @subpackage
* @author RolandD
* @author Max Milbers
* @todo add pagination
* @link http://www.virtuemart.net
* @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* @version $Id: default.php 5378 2012-02-04 12:58:01Z Milbo $
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');
JHTML::_( 'behavior.modal' );
/* javascript for list Slide
  Only here for the order list
  can be changed by the template maker
*/
$js = "
jQuery(document).ready(function () {
	jQuery('.orderlistcontainer').hover(
		function() { jQuery(this).find('.orderlist').stop().show()},
		function() { jQuery(this).find('.orderlist').stop().hide()}
	)
});
";

$document = JFactory::getDocument();
$document->addScriptDeclaration($js);

/*$edit_link = '';
if(!class_exists('Permissions')) require(JPATH_VM_ADMINISTRATOR.DS.'helpers'.DS.'permissions.php');
if (Permissions::getInstance()->check("admin,storeadmin")) {
	$edit_link = '<a href="'.JURI::root().'index.php?option=com_virtuemart&tmpl=component&view=category&task=edit&virtuemart_category_id='.$this->category->virtuemart_category_id.'">
		'.JHTML::_('image', 'images/M_images/edit.png', JText::_('COM_VIRTUEMART_PRODUCT_FORM_EDIT_PRODUCT'), array('width' => 16, 'height' => 16, 'border' => 0)).'</a>';
}

echo $edit_link; */ ?>
<div class="category_description">
	<?php echo $this->category->category_description ; ?>
</div>
<?php
/* Show child categories */

if ( VmConfig::get('showCategory',1) ) {
	if ($this->category->haschildren) {

		// Category and Columns Counter
		$iCol = 1;
		$iCategory = 1;

		// Calculating Categories Per Row
		$categories_per_row = VmConfig::get ( 'categories_per_row', 3 );
		$category_cellwidth = ' width'.floor ( 100 / $categories_per_row );

		// Separator
		$verticalseparator = " vertical-separator";
		?>

		<div class="category-view">

		<?php // Start the Output
		if(!empty($this->category->children)){
		foreach ( $this->category->children as $category ) {

			// Show the horizontal seperator
			if ($iCol == 1 && $iCategory > $categories_per_row) { ?>
			<div class="horizontal-separator"></div>
			<?php }

			// this is an indicator wether a row needs to be opened or not
			if ($iCol == 1) { ?>
			<div class="row">
			<?php }

			// Show the vertical seperator
			if ($iCategory == $categories_per_row or $iCategory % $categories_per_row == 0) {
				$show_vertical_separator = ' ';
			} else {
				$show_vertical_separator = $verticalseparator;
			}

			// Category Link
			$caturl = JRoute::_ ( 'index.php?option=com_virtuemart&view=category&virtuemart_category_id=' . $category->virtuemart_category_id );

				// Show Category ?>
				<div class="category floatleft<?php echo $category_cellwidth . $show_vertical_separator ?>">
					<div class="spacer">
						<h2>
							<a href="<?php echo $caturl ?>" title="<?php echo $category->category_name ?>">
							<?php echo $category->category_name ?>
							<br />
							<?php // if ($category->ids) {
								echo $category->images[0]->displayMediaThumb("",false);
							//} ?>
							</a>
						</h2>
					</div>
				</div>
			<?php
			$iCategory ++;

		// Do we need to close the current row now?
		if ($iCol == $categories_per_row) { ?>
		<div class="clear"></div>
		</div>
			<?php
			$iCol = 1;
		} else {
			$iCol ++;
		}
	}
	}
	// Do we need a final closing row tag?
	if ($iCol != 1) { ?>
		<div class="clear"></div>
		</div>
	<?php } ?>
</div>

<?php }
}
?>
<div class="browse-view">
    <?php
if (!empty($this->keyword)) {
	?>
	<h3><?php echo $this->keyword; ?></h3>
	<?php
} ?>
 		<?php if ($this->search !==null ) { ?>
		    <form action="<?php echo JRoute::_('index.php?option=com_virtuemart&view=category&limitstart=0&virtuemart_category_id='.$this->category->virtuemart_category_id ); ?>" method="get">

		    <!--BEGIN Search Box --><div class="virtuemart_search">
		    <?php echo $this->searchcustom ?>
		    <br />
		    <?php echo $this->searchcustomvalues ?>
		    <input name="keyword" class="inputbox" type="text" size="20" value="<?php echo $this->keyword ?>" />
		    <input type="submit" value="<?php echo JText::_('COM_VIRTUEMART_SEARCH') ?>" class="button" onclick="this.form.keyword.focus();"/>
		    </div>
				    <input type="hidden" name="search" value="true" />
				    <input type="hidden" name="view" value="category" />

		    </form>
		<!-- End Search Box -->
		<?php } ?>

<?php // Show child categories
if (!empty($this->products)) {
?>

			</div> <!-- end of orderby-displaynumber -->
<div class="header_c">
	<div class="header_c_text"><?php echo $this->category->category_name; ?></div>
</div>
	
<?php
// Category and Columns Counter
$iBrowseCol = 1;
$iBrowseProduct = 1;

// Calculating Products Per Row
$BrowseProducts_per_row = $this->perRow;
$Browsecellwidth = ' width'.floor ( 100 / $BrowseProducts_per_row );

// Separator
$verticalseparator = " vertical-separator";

// Count products
$BrowseTotalProducts = 0;
foreach ( $this->products as $product ) {
   $BrowseTotalProducts ++;
}

// Start the Output
foreach ( $this->products as $product ) {

	// Show the horizontal seperator
	if ($iBrowseCol == 1 && $iBrowseProduct > $BrowseProducts_per_row) { ?>
	<div class="horizontal-separator"></div>
	<?php }

	// this is an indicator wether a row needs to be opened or not
	if ($iBrowseCol == 1) { ?>
	<div class="row">
	<?php }

	// Show the vertical seperator
	if ($iBrowseProduct == $BrowseProducts_per_row or $iBrowseProduct % $BrowseProducts_per_row == 0) {
		$show_vertical_separator = ' ';
	} else {
		$show_vertical_separator = $verticalseparator;
	}

		// Show Products ?>
		<div class="product floatleft<?php echo $Browsecellwidth . $show_vertical_separator ?>">
			<div class="spacer">
				<div class="product_info">

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
					<?php /** @todo make image popup */
							echo $product->images[0]->displayMediaThumb('class="browseProductImage" border="0" title="'.$product->product_name.'" ',true,'class="modal"');
						?>


						<!-- The "Average Customer Rating" Part -->
						<?php if (VmConfig::get('pshop_allow_reviews') == 1) { ?>
						<span class="contentpagetitle"><?php echo JText::_('COM_VIRTUEMART_CUSTOMER_RATING') ?>:</span>
						<br />
						<?php
						 //$img_url = JURI::root().VmConfig::get('assets_general_path').'/reviews/'.$product->votes->rating.'.gif';
						 //echo JHTML::image($img_url, $product->votes->rating.' '.JText::_('COM_VIRTUEMART_REVIEW_STARS'));
						 //echo JText::_('COM_VIRTUEMART_TOTAL_VOTES').": ". $product->votes->allvotes; ?>
						<?php } ?>

				</div>
				<div style="float:right">
				<div class="product-price" id="productPrice<?php echo $product->virtuemart_product_id ?>">
					<?php
					if ($this->show_prices == '1') {
						if( $product->product_unit && VmConfig::get('vm_price_show_packaging_pricelabel')) {
							echo "<strong>". JText::_('COM_VIRTUEMART_CART_PRICE_PER_UNIT').' ('.$product->product_unit."):</strong>";
						}

						//todo add config settings
						if( $this->showBasePrice){
							echo $this->currency->createPriceDiv('basePrice','COM_VIRTUEMART_PRODUCT_BASEPRICE',$product->prices);
							echo $this->currency->createPriceDiv('basePriceVariant','COM_VIRTUEMART_PRODUCT_BASEPRICE_VARIANT',$product->prices);
						}
						echo $this->currency->createPriceDiv('variantModification','COM_VIRTUEMART_PRODUCT_VARIANT_MOD',$product->prices);
						echo $this->currency->createPriceDiv('basePriceWithTax','COM_VIRTUEMART_PRODUCT_BASEPRICE_WITHTAX',$product->prices);
						echo $this->currency->createPriceDiv('discountedPriceWithoutTax','COM_VIRTUEMART_PRODUCT_DISCOUNTED_PRICE',$product->prices);
						echo $this->currency->createPriceDiv('salesPriceWithDiscount','COM_VIRTUEMART_PRODUCT_SALESPRICE_WITH_DISCOUNT',$product->prices);
						 if($product->prices[salesPrice] != $product->prices[priceWithoutTax]) {
							echo $this->currency->createPriceDiv('priceWithoutTax', 'COM_VIRTUEMART_PRODUCT_SALESPRICE_WITHOUT_TAX', $product->prices);
							echo $this->currency->createPriceDiv('salesPrice', 'COM_VIRTUEMART_PRODUCT_SALESPRICE', $product->prices);        
						} else {
							echo $this->currency->createPriceDiv('salesPrice', 'COM_VIRTUEMART_PRODUCT_SALESPRICE', $product->prices);        
							echo $this->currency->createPriceDiv('priceWithoutTax', '', '');
						}
						echo $this->currency->createPriceDiv('taxAmount','COM_VIRTUEMART_PRODUCT_TAX_AMOUNT',$product->prices);
					} ?>
					<div class="clear"></div>
					<div>
					<?php // Product Details Button
					echo JHTML::link($product->link, JText::_('COM_VIRTUEMART_PRODUCT_DETAILS'), array('title' => $product->product_name,'class' => 'product-details'));
					?>
					</div>
					<div class="clear"></div>
				
				<div>
				<form method="post" class="product js-recalculate" action="index.php" >
					<div class="addtocart-bar">
					<?php // Display the quantity box 

					$stockhandle = VmConfig::get('stockhandle', 'none');
					if (($stockhandle == 'disableit' or $stockhandle == 'disableadd') and ($product->product_in_stock - $product->product_ordered) < 1) {
					?>
						<a href="<?php echo JRoute::_('index.php?option=com_virtuemart&view=productdetails&layout=notify&virtuemart_product_id='.$product->virtuemart_product_id); ?>"><?php echo JText::_('COM_VIRTUEMART_CART_NOTIFY') ?></a>
					<?php } else { ?>
						<!-- <label for="quantity<?php echo $this->product->virtuemart_product_id; ?>" class="quantity_box"><?php echo JText::_('COM_VIRTUEMART_CART_QUANTITY'); ?>: </label> -->
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
					
					<div class="clear"></div>
					
					
					
				</div>
				</div>
			<div class="clear"></div>
			</div>
		</div>
	<?php

   // Do we need to close the current row now?
   if ($iBrowseCol == $BrowseProducts_per_row || $iBrowseProduct == $BrowseTotalProducts) {?>
   <div class="clear"></div>
   </div> <!-- end of row -->
      <?php
      $iBrowseCol = 1;
   } else {
      $iBrowseCol ++;
   }

   $iBrowseProduct ++;
} // end of foreach ( $this->products as $product )
// Do we need a final closing row tag?
if ($iBrowseCol != 1) { ?>
	<div class="clear"></div>

<?php
}
?>
<!-- /div removed valerie -->
	<div id="bottom-pagination"><?php echo $this->vmPagination->getPagesLinks(); ?><span style="float:right"><?php echo $this->vmPagination->getPagesCounter(); ?></span></div>
<!-- /div removed valerie -->
<?php } elseif ($this->search !==null ) echo JText::_('COM_VIRTUEMART_NO_RESULT').($this->keyword? ' : ('. $this->keyword. ')' : '')
?>
</div><!-- end browse-view -->