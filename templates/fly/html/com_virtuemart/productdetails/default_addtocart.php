<?php
/**
 *
 * Show the product details page
 *
 * @package	VirtueMart
 * @subpackage
 * @author Max Milbers, Valerie Isaksen
 * @todo handle child products
 * @link http://www.virtuemart.net
 * @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * @version $Id: default_addtocart.php 6180 2012-06-28 15:36:17Z openglobal $
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');
?>
<div class="addtocart-area">

    <form method="post" class="product js-recalculate" action="index.php" >
	<?php // Product custom_fields
	if (!empty($this->product->customfieldsCart)) { ?>
    	<div class="product-fields">
		<?php foreach ($this->product->customfieldsCart as $field) { ?>
		    <div class="product-field product-field-type-<?php echo $field->field_type ?>">
			<span class="product-fields-title" ><strong><?php echo JText::_($field->custom_title) ?></strong></span>
			<?php if ($field->custom_tip)
			    echo JHTML::tooltip($field->custom_tip, JText::_($field->custom_title), 'tooltip.png'); ?>
			<span class="product-field-display"><?php echo $field->display ?></span>

			<span class="product-field-desc"><?php echo $field->custom_field_desc ?></span>
		    </div><br />
		    <?php
		}
		?>
    	</div>
	<?php
	}
	/* Product custom Childs
	 * to display a simple link use $field->virtuemart_product_id as link to child product_id
	 * custom_value is relation value to child
	 */

	if (!empty($this->product->customsChilds)) {
	    ?>
    	<div class="product-fields">
    <?php foreach ($this->product->customsChilds as $field) { ?>
		    <div class="product-field product-field-type-<?php echo $field->field->field_type ?>">
			<span class="product-fields-title" ><strong><?php echo JText::_($field->field->custom_title) ?></strong></span>
			<span class="product-field-desc"><?php echo JText::_($field->field->custom_value) ?></span>
			<span class="product-field-display"><?php echo $field->display ?></span>

		    </div><br />
		<?php } ?>
    	</div>
<?php } ?>

	<div class="addtocart-bar">

<?php // Display the quantity box

    $stockhandle = VmConfig::get('stockhandle', 'none');
    if (($stockhandle == 'disableit' or $stockhandle == 'disableadd') and ($this->product->product_in_stock - $this->product->product_ordered) < 1) {
 ?>
		<a href="<?php echo JRoute::_('index.php?option=com_virtuemart&view=productdetails&layout=notify&virtuemart_product_id='.$this->product->virtuemart_product_id); ?>" class="notify"><?php echo JText::_('COM_VIRTUEMART_CART_NOTIFY') ?></a>

<?php } else { ?>
						<!-- <label for="quantity<?php echo $this->product->virtuemart_product_id; ?>" class="quantity_box"><?php echo JText::_('COM_VIRTUEMART_CART_QUANTITY'); ?>: </label> -->
	    <span class="quantity-box">
		Quantity: 
		<input type="text" class="quantity-input js-recalculate" name="quantity[]" value="1" />
		</span>
		<span class="addtocart-button">
		<input type="submit" name="addtocart" class="addtocart-button" value="<?php echo JText::_('COM_VIRTUEMART_CART_ADD_TO') ?>" title="<?php echo JText::_('COM_VIRTUEMART_CART_ADD_TO') ?>" />
		</span>
<?php } ?>
	

	<?php // Display the add to cart button END  ?>
	<input type="hidden" class="pname" value="<?php echo $this->product->product_name ?>" />
	<input type="hidden" name="option" value="com_virtuemart" />
	<input type="hidden" name="view" value="cart" />
	<input type="hidden" name="task" value="add" />
	<input type="hidden" name="virtuemart_product_id[]" value="<?php echo $this->product->virtuemart_product_id ?>" />
<?php /** @todo Handle the manufacturer view */ ?>
	<input type="hidden" name="virtuemart_manufacturer_id" value="<?php echo $this->product->virtuemart_manufacturer_id ?>" />
	<input type="hidden" name="virtuemart_category_id[]" value="<?php echo $this->product->virtuemart_category_id ?>" />
	</div>
    </form>

    <div class="clear"></div>
</div>
