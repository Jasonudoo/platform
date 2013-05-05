<?php
/**
 * @version		1.0.0
 * @package		Joomla
 * @subpackage	Schedule Order
 * @author      Jason<jason@netwebx.com>
 * @copyright	Copyright (C) 2013 NetWebX.COM
 * @license		GNU/GPL, see LICENSE.php
 */
// no direct access
defined( '_JEXEC' ) or die ;		
JHTML::_('behavior.modal');
$popup = 'class="modal" rel="{handler: \'iframe\', size: {x: 800, y: 500}}"';			
if ($this->config->prevent_duplicate_registration) {
	$readOnly = ' readonly="readonly" ' ;
} else {
	$readOnly = '' ;
}
if ($this->config->use_https) {
	$url = JRoute::_('index.php?option=com_scheduleorder&view=cart&Itemid='.$this->Itemid, false, true);
} else {
	$url = JRoute::_('index.php?option=com_scheduleorder&view=cart&Itemid='.$this->Itemid, false);
}
?>
<h1 class="sch_title">View My Cart</h1>	
<?php
if (count($this->items)) {
?>
	<form method="post" name="adminForm" id="adminForm" action="<?php echo $url; ?>">		
		<table class="table table-striped table-bordered table-condensed" width='80%'>
			<thead>
				<tr>					
					<th class="col_image">&nbsp;</th>
					<th class="col_event">Product Name</th>
					<th class="col_weight">Weight</th>
					<th class="col_price">Price</th>	
				</tr>
			</thead>
			<tbody>	
<?php
$total = 0 ;
$k = 0 ;
for ($i = 0 , $n = count($this->items) ; $i < $n; $i++) {
	$item = $this->items[$i] ;
	$total += $item->quantity;
	
	echo "<tr>";
	//images
	echo "<td><img src='/" . $item->image_file_url_thumb . "' style='width:120px;height:120px'></td>";
	echo "<td width='50%'>" . $item->product_name . "</td>";
	echo "<td width='20%'>" . $item->quantity * 1000 . " g </td>";
	echo "<td width='20%'>" . $item->currency_symbol. number_format($item->price, 2, '.', '') .  "<br/>";
	echo $item->custom_value . "</td>";
	echo "</tr>";
}
?>
				<tr>				
					<td colspan="4" style="text-align: right;">
						<input type="button" class="btn btn-primary" value="Update" onclick="updateCart();" />																										
						<input type="button" class="btn btn-primary" value="Checkout" onclick="checkout();" />						
					</td>								
				</tr>	
			</tbody>			
		</table>	
		<input type="hidden" name="Itemid" value="<?php echo $this->Itemid; ?>" />
		<input type="hidden" name="option" value="com_scheduleorder" />
		<input type="hidden" name="task" value="" />		
		<input type="hidden" name="id" value="" />							
		<script type="text/javascript">	
			<?php echo $this->jsString ; ?>
			function checkout() {
				var form = document.adminForm ;
				ret = checkQuantity() ;
				if (ret) {						
					form.task.value = 'checkout';
					form.submit() ;
				}					
			}												
			function updateCart() {
				var form = document.adminForm ;
				var ret = checkQuantity();
				if (ret) {
					form.task.value = 'update_cart';
					form.submit();
				}										
			}
			function checkQuantity() {
				var form = document.adminForm ;
				var eventId ;
				var quantity ;
				var enteredQuantity ;
				var index ;
				var availableQuantity ;
				if (form['event_id[]'].length) {
					var length = form['event_id[]'].length ;
					//There are more than one events
					for (var  i = 0 ; i < length ; i++) {
						eventId = form['event_id[]'][i].value ;
						enteredQuantity = form['quantity[]'][i].value ;
						index = findIndex(eventId, arrEventIds);
						availableQuantity = arrQuantities[index] ;
						if ((availableQuantity != -1) && (enteredQuantity >availableQuantity)) {
							alert("<?php echo JText::_("EB_INVALID_QUANTITY"); ?>" + availableQuantity);
							form['event_id[]'][i].focus();
							return false ;
						}							
					}
				} else {
					//There is only one event						
					enteredQuantity = form['quantity[]'].value ;
					availableQuantity = arrQuantities[0] ;
					if ((availableQuantity != -1) && (enteredQuantity >availableQuantity)) {
						alert("<?php echo JText::_("EB_INVALID_QUANTITY"); ?>" + availableQuantity);
						form['event_id[]'].focus();
						return false ;
					}
				}					
				return true ;
			}

			function findIndex(eventId, eventIds) {
				for (var i = 0 ; i < eventIds.length ; i++) {
					if (eventIds[i] == eventId) {
						return i ;
					}
				}
				return -1 ;
			}														
		</script>	
	</form>					
<?php	
} else {
?>
	<p class="message"><?php echo JText::_('EB_NO_EVENTS_IN_CART'); ?></p>
<?php	
}
?>