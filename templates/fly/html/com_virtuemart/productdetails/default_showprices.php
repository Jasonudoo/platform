<?php
/**
 *
 * Show the product details page
 *
 * @package	VirtueMart
 * @subpackage
 * @author Max Milbers, Valerie Isaksen

 * @link http://www.virtuemart.net
 * @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * @version $Id: default_showprices.php 5834 2012-04-09 12:05:33Z Milbo $
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

?>
<div class="product-price" id="productPrice<?php echo $this->product->virtuemart_product_id ?>">
    <?php
    if (empty($this->product->prices) and VmConfig::get('askprice', 1)) {
	?>
        <a class="ask-a-question bold" href="<?php echo $url ?>" ><?php echo JText::_('COM_VIRTUEMART_PRODUCT_ASKPRICE') ?></a>
    <?php
    }
    if ($this->showBasePrice) {
	echo $this->currency->createPriceDiv('basePrice', 'COM_VIRTUEMART_PRODUCT_BASEPRICE', $this->product->prices);
	echo $this->currency->createPriceDiv('basePriceVariant', 'COM_VIRTUEMART_PRODUCT_BASEPRICE_VARIANT', $this->product->prices);
    }

    echo $this->currency->createPriceDiv('variantModification', 'COM_VIRTUEMART_PRODUCT_VARIANT_MOD', $this->product->prices);
    echo $this->currency->createPriceDiv('basePriceWithTax', 'COM_VIRTUEMART_PRODUCT_BASEPRICE_WITHTAX', $this->product->prices);
    echo $this->currency->createPriceDiv('discountedPriceWithoutTax', 'COM_VIRTUEMART_PRODUCT_DISCOUNTED_PRICE', $this->product->prices);
    echo $this->currency->createPriceDiv('salesPriceWithDiscount', 'COM_VIRTUEMART_PRODUCT_SALESPRICE_WITH_DISCOUNT', $this->product->prices);
    echo $this->currency->createPriceDiv('priceWithoutTax', 'COM_VIRTUEMART_PRODUCT_SALESPRICE_WITHOUT_TAX', $this->product->prices);
    echo $this->currency->createPriceDiv('discountAmount', 'COM_VIRTUEMART_PRODUCT_DISCOUNT_AMOUNT', $this->product->prices);
    echo $this->currency->createPriceDiv('taxAmount', 'COM_VIRTUEMART_PRODUCT_TAX_AMOUNT', $this->product->prices);
    
    if($this->product->prices[salesPrice] != $this->product->prices[basePrice]) {
		echo '<div class="pr_det_price">'.$this->currency->createPriceDiv('salesPrice', 'COM_VIRTUEMART_PRODUCT_SALESPRICE', $this->product->prices).'</div>';
		echo '<div class="clear"></div>';
		echo '<div class="pr_det_price_lt"><div class="pr_det_price">'.$this->currency->createPriceDiv('basePrice', 'COM_VIRTUEMART_PRODUCT_BASEPRICE', $this->product->prices).'</div>';
		echo '<div class="pr_det_price">tax incl</div></div>';
    } else {
        echo '<div class="pr_det_price">'.$this->currency->createPriceDiv('salesPrice', 'COM_VIRTUEMART_PRODUCT_SALESPRICE', $this->product->prices).'</div>';
		echo '<div class="pr_det_price">tax incl</div>';
	}
    ?>
</div>