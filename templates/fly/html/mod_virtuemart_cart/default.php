<?php // no direct access
defined('_JEXEC') or die('Restricted access');

//dump ($cart,'mod cart');
// Ajax is displayed in vm_cart_products
// ALL THE DISPLAY IS Done by Ajax using "hiddencontainer" ?>

<!-- Virtuemart 2 Ajax Card -->

<div id="shoping_cart">
	<b>Shoping cart: </b>
	<?php echo  $data->totalProductTxt ?>
	<?php if ($data->totalProduct) echo  $data->billTotal; ?>
	<?php if ($data->totalProduct) echo  $data->cart_show; ?>

</div>