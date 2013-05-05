<?php
/**
 * @version		1.5.3
 * @package		Joomla
 * @subpackage	Event Booking
 * @author  Tuan Pham Ngoc
 * @copyright	Copyright (C) 2010 Ossolution Team
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
	$url = JRoute::_('index.php?option=com_eventbooking&view=cart&Itemid='.$this->Itemid, false, true);
} else {
	$url = JRoute::_('index.php?option=com_eventbooking&view=cart&Itemid='.$this->Itemid, false);
}
?>
<h1 class="eb_title"><?php echo JText::_('EB_ADDED_EVENTS'); ?></h1>	
<?php
if (count($this->items)) {
?>
	<form method="post" name="adminForm" id="adminForm" action="<?php echo $url; ?>">		
		<table class="table table-striped table-bordered table-condensed">
			<thead>
				<tr>					
					<th class="col_event">
						<?php echo JText::_('EB_EVENT'); ?>
					</th>
					<?php
						if ($this->config->show_event_date) {
						?>
							<th class="col_event_date">
								<?php echo JText::_('EB_EVENT_DATE'); ?>
							</th>	
						<?php	
						}
					?>
					<th class="col_action">
						<?php echo JText::_('EB_REMOVE'); ?>
					</th>
					<th class="col_price">
						<?php echo JText::_('EB_PRICE'); ?>
					</th>									
					<th class="col_quantity">
						<?php echo JText::_('EB_QUANTITY'); ?><a href="javascript:updateCart()"><img src="<?php echo JURI::base(true).'/components/com_eventbooking/assets/images/update_quantity.png' ?>" title="<?php echo JText::_("EB_UPDATE_QUANTITY"); ?>" align="top" /></a>
					</th>																
					<th class="col_quantity">
						<?php echo JText::_('EB_SUB_TOTAL'); ?>
					</th>
				</tr>
			</thead>
			<tbody>
			<?php				
				$total = 0 ;
				$k = 0 ;			
				for ($i = 0 , $n = count($this->items) ; $i < $n; $i++) {
					$item = $this->items[$i] ;					
					$total += $item->quantity*$item->rate ;						
		        	$url = JRoute::_('index.php?option=com_eventbooking&task=view_event&event_id='.$item->id.'&tmpl=component&Itemid='.$this->Itemid);			        	
				?>
					<tr>											
						<td class="col_event">
							<a href="<?php echo $url; ?>" <?php echo $popup; ?>><?php echo $item->title; ?></a>								
						</td>
						<?php
							if ($this->config->show_event_date) {
							?>
								<td class="col_event_date">
									<?php
                                        if ($item->event_date == EB_TBC_DATE) {
                                            echo JText::_('EB_TBC');
                                        } else {
                                            echo JHTML::_('date', $item->event_date, $this->config->event_date_format, null);        
                                        }    
									?>									
								</td>
							<?php	
							}	
						?>
						<td align="center" class="col_action">
							<a href="javascript:removeItem(<?php echo $item->id; ?>)"><img src="<?php echo JURI::base().'components/com_eventbooking/assets/images/remove_from_cart.png'; ?>" border="0" /></a>
							<input type="hidden" name="event_id[]" value="<?php echo $item->id; ?>" />						
						</td>
						<td class="col_price">
							<?php echo number_format($item->rate, 2); ?>
						</td>
						<td class="col_quantity">
							<input type="text" class="input-mini inputbox quantity_box" size="3" value="<?php echo $item->quantity ; ?>" name="quantity[]" <?php echo $readOnly ; ?> />
						</td>																										
						<td class="col_price">
							<?php echo number_format($item->rate*$item->quantity, 2); ?>
						</td>						
					</tr>
				<?php									
				}						
				if ($this->config->show_event_date) {
					$cols = 6 ;
				} else {
					$cols = 5 ;
				}
				?>								
				<tr>
					<td colspan="<?php echo $cols - 1 ; ?>" style="text-align: right;">
						<span class="total_amount"><?php echo JText::_('EB_TOTAL'); ?></span>
					</td>
					<td>					
						<?php echo EventBookingHelper::formatCurrency($total, $this->config); ?>													
					</td>
				</tr>			
				<?php																	
				?>		
				<tr>				
					<td colspan="<?php echo $cols ; ?>" style="text-align: right;">
						<input type="button" class="btn btn-primary" value="<?php echo JText::_('EB_ADD_MORE_EVENTS'); ?>" onclick="continueShopping();" />
						<input type="button" class="btn btn-primary" value="<?php echo JText::_('EB_UPDATE'); ?>" onclick="updateCart();" />																										
						<input type="button" class="btn btn-primary" value="<?php echo JText::_('EB_CHECKOUT'); ?>" onclick="checkout();" />						
					</td>								
				</tr>	
			</tbody>			
		</table>	
		<input type="hidden" name="Itemid" value="<?php echo $this->Itemid; ?>" />
		<input type="hidden" name="category_id" value="<?php echo $this->categoryId; ?>" />	
		<input type="hidden" name="option" value="com_eventbooking" />
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
			function continueShopping() {
				var form = document.adminForm ;
				form.task.value = 'view_category';
				form.submit();
			}								
			function updateCart() {
				var form = document.adminForm ;
				var ret = checkQuantity();
				if (ret) {
					form.task.value = 'update_cart';
					form.submit();
				}										
			}
			function removeItem(id) {
				if (confirm("<?php echo JText::_('EB_REMOVE_CONFIRM'); ?>")) {
					var form = document.adminForm ;
					form.id.value = id ;
					form.task.value = 'remove_cart' ;
					form.submit() ;
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