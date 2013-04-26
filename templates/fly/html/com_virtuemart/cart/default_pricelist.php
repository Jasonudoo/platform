<?php defined('_JEXEC') or die('Restricted access');
/**
 *
 * Layout for the shopping cart
 *
 * @package	VirtueMart
 * @subpackage Cart
 * @author Max Milbers
 * @author Patrick Kohl
 * @link http://www.virtuemart.net
 * @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 *
 */

// Check to ensure this file is included in Joomla!

// jimport( 'joomla.application.component.view');
// $viewEscape = new JView();
// $viewEscape->setEscape('htmlspecialchars');

?>

<fieldset>
	<table
		class="cart-summary"
		cellspacing="1px"
		cellpadding="10px">
		<tr>
			<th align="left" style="width:34%;"><?php echo JText::_('COM_VIRTUEMART_CART_NAME') ?></th>
			<th align="left" style="width:10%;"><?php echo JText::_('COM_VIRTUEMART_CART_SKU') ?></th>
			<th align="center" style="width:10%;"><?php echo JText::_('COM_VIRTUEMART_CART_PRICE') ?></th>
			<th align="center" style="width:19%;"><?php echo JText::_('COM_VIRTUEMART_CART_QUANTITY') ?>
				/ <?php echo JText::_('COM_VIRTUEMART_CART_ACTION') ?></th>
			<?php if ( VmConfig::get('show_tax')) { ?>
				<th align="right"><?php  echo "<span  class='priceColor2'>".JText::_('COM_VIRTUEMART_CART_SUBTOTAL_TAX_AMOUNT')."</span>" ?></th>
			<?php } ?>

			<th align="right" style="width:13%;"><?php echo JText::_('COM_VIRTUEMART_CART_SUBTOTAL') ?></th>
		</tr>


		<?php
		$i=1;
// 		vmdebug('$this->cart->products',$this->cart->products);
		foreach( $this->cart->products as $pkey =>$prow ) { ?>
			<tr valign="top" class="sectiontableentry<?php echo $i ?> cart-tr-bg">
				<td align="left" >
					<?php if ( $prow->virtuemart_media_id) {  ?>
						<span class="cart-images">
						 <?php
						 if(!empty($prow->image)) echo $prow->image->displayMediaThumb('',false);
						 ?>
						</span>
					<?php } ?>
					<?php echo JHTML::link($prow->url, $prow->product_name).$prow->customfields; ?>

				</td>
				<td align="left" ><?php  echo $prow->product_sku ?></td>
				<td align="center" >
				<?php
// 					vmdebug('$this->cart->pricesUnformatted[$pkey]',$this->cart->pricesUnformatted[$pkey]['priceBeforeTax']);
					echo $this->currencyDisplay->createPriceDiv('basePrice','', $this->cart->pricesUnformatted[$pkey],false);
// 					echo $prow->salesPrice ;
					?>
				</td>
				<td align="center" >
					<form action="<?php echo JRoute::_('index.php'); ?>" method="post" class="inline">
						<input type="hidden" name="option" value="com_virtuemart" />
						<input type="text" title="<?php echo  JText::_('COM_VIRTUEMART_CART_UPDATE') ?>" class="inputbox" size="3" maxlength="4" name="quantity" value="<?php echo $prow->quantity ?>" />
						<input type="hidden" name="view" value="cart" />
						<input type="hidden" name="task" value="update" />
						<input type="hidden" name="cart_virtuemart_product_id" value="<?php echo $prow->cart_item_id  ?>" />
						<input type="submit" class="vmicon vm2-add_quantity_cart" name="update" title="<?php echo  JText::_('COM_VIRTUEMART_CART_UPDATE') ?>" value=" "/>
					</form>
					<a class="vmicon vm2-remove_from_cart" title="<?php echo JText::_('COM_VIRTUEMART_CART_DELETE') ?>" href="<?php echo JRoute::_('index.php?option=com_virtuemart&view=cart&task=delete&cart_virtuemart_product_id='.$prow->cart_item_id  ) ?>"> </a>
				</td>

				<?php if ( VmConfig::get('show_tax')) { ?>
				<td align="right"><?php echo "<span class='priceColor2'>".$this->currencyDisplay->createPriceDiv('taxAmount','', $this->cart->pricesUnformatted[$pkey],false,false,$prow->quantity)."</span>" ?></td>
                                <?php } ?>

				<td colspan="1" align="right">
				<?php
				if (VmConfig::get('checkout_show_origprice',1) && !empty($this->cart->pricesUnformatted[$pkey]['basePriceWithTax']) && $this->cart->pricesUnformatted[$pkey]['basePriceWithTax'] != $this->cart->pricesUnformatted[$pkey]['salesPrice'] ) {
					echo '<span class="line-through">'.$this->currencyDisplay->createPriceDiv('basePriceWithTax','', $this->cart->pricesUnformatted[$pkey],true,false,$prow->quantity) .'</span><br />' ;
				}
				echo $this->currencyDisplay->createPriceDiv('salesPrice','', $this->cart->pricesUnformatted[$pkey],false,false,$prow->quantity) ?></td>
			</tr>
		<?php
			$i = 1 ? 2 : 1;
		} ?>
		
		<tr class="cart-tr-bg">
			<td colspan="4" align="right"><?php echo JText::_('COM_VIRTUEMART_CART_SUBTOTAL') ?>: </td>
			<td align="right"><?php echo $this->currencyDisplay->createPriceDiv('salesPrice','', $this->cart->pricesUnformatted,false) ?></td>
		</tr>
		<!--Begin of SubTotal, Tax, Shipment, Coupon Discount and Total listing -->
		<?php if ( VmConfig::get('show_tax')) { $colspan=3; } else { $colspan=2; } ?>
		<tr>
			<td colspan="5">&nbsp;</td>
		</tr>


		<tr class="sectiontableentry2 cart-tr-bg">
			<td colspan="4" align="right"><?php echo JText::_('COM_VIRTUEMART_CART_TOTAL') ?>: </td>

			<?php if ( VmConfig::get('show_tax')) { ?>
				<td align="right"><?php echo "<span  class='priceColor2'>".$this->currencyDisplay->createPriceDiv('billTaxAmount','', $this->cart->pricesUnformatted['billTaxAmount'],false)."</span>" ?></td>
			<?php } ?>
			<td align="right"><?php echo $this->currencyDisplay->createPriceDiv('billTotal','', $this->cart->pricesUnformatted['billTotal'],false); ?></td>
		</tr>
		<tr align="right" class="cart-tr-bg">
			<td colspan="4" align="right"><?php echo JText::_('COM_VIRTUEMART_ORDER_PRINT_TOTAL_TAX') ?>: </td>
			<td align="right"> <?php echo $this->currencyDisplay->createPriceDiv('billDiscountAmount','', $this->cart->pricesUnformatted['billDiscountAmount'],false) ?></td>
		</tr>
						
						
		<?php
		if ( $this->totalInPaymentCurrency) {
		?>
			<tr class="sectiontableentry2">
				<td colspan="4" align="right"><?php echo JText::_('COM_VIRTUEMART_CART_TOTAL_PAYMENT') ?>: </td>
				<?php if ( VmConfig::get('show_tax')) { ?>
				<td align="right">  </td>
				<?php } ?>
				<td align="right">  </td>
				<td align="right"><strong><?php echo $this->totalInPaymentCurrency;   ?></strong></td>
			</tr>
			<?php
		}?>

	</table>
	
	<br />
		
	<?php
	if (VmConfig::get('coupons_enable')) {
	?>
		<table class="cart-summary-coupon">
			<tr class="sectiontableentry2">
				<td style="width:45%;">
					<div class="enter_coupon_text">If you have a coupone code, please enter it below:</div>
				</td>
				<td align="left" style="width:40%;">
				    <?php if(!empty($this->layoutName) && $this->layoutName=='default') {
					   // echo JHTML::_('link', JRoute::_('index.php?view=cart&task=edit_coupon',$this->useXHTML,$this->useSSL), JText::_('COM_VIRTUEMART_CART_EDIT_COUPON'));
					    echo $this->loadTemplate('coupon');
				    }
				?>

				<?php if (!empty($this->cart->cartData['couponCode'])) { ?>
					 <?php
						echo $this->cart->cartData['couponCode'] ;
						echo $this->cart->cartData['couponDescr'] ? (' (' . $this->cart->cartData['couponDescr'] . ')' ): '';
						?>

					</td >

					<td align="right">&nbsp;</td>
					<td align="right"><?php echo $this->currencyDisplay->createPriceDiv('salesPriceCoupon','', $this->cart->pricesUnformatted['salesPriceCoupon'],false); ?> </td>
				<?php } else { ?>
					</td >
					<td colspan="6" align="left">&nbsp;</td>
				<?php }

				?>
			</tr>
		</table>
	<?php } ?>
		

</fieldset>
