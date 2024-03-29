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

// Set toolbar items for the page
JToolBarHelper::title(JText::_( 'EB_WAITING_MANAGEMENT' ), 'generic.png' );
JToolBarHelper::deleteList(JText::_('EB_DELETE_WAITING_CONFIRM') , 'remove_waitings');
JToolBarHelper::editList('edit_waiting');	
JToolBarHelper::addNew('add_waiting');		
?>
<form action="index.php?option=com_eventbooking&view=waitings" method="post" name="adminForm" id="adminForm">
<table width="100%">
<tr>
	<td align="left">
		<?php echo JText::_( 'Filter' ); ?>:
		<input type="text" name="search" id="search" value="<?php echo $this->lists['search'];?>" class="text_area search-query" onchange="document.adminForm.submit();" />		
		<button onclick="this.form.submit();" class="btn"><?php echo JText::_( 'Go' ); ?></button>
		<button onclick="document.getElementById('search').value='';this.form.submit();" class="btn"><?php echo JText::_( 'Reset' ); ?></button>		
	</td >	
	<td style="text-align: right;">
		<?php echo $this->lists['event_id']; ?>
	</td>
</tr>
</table>
<div id="editcell">
	<table class="adminlist table table-striped">
	<thead>
		<tr>
			<th width="2%">
				<?php echo JText::_( 'NUM' ); ?>
			</th>
			<th width="2%">
				<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $this->items ); ?>);" />
			</th>			
			<th class="title" style="text-align: left;" width="8%">
				<?php echo JHTML::_('grid.sort',  JText::_('EB_FIRST_NAME'), 'a.first_name', $this->lists['order_Dir'], $this->lists['order'] ); ?>
			</th>						
			<th class="title" style="text-align: left;" width="8%">
				<?php echo JHTML::_('grid.sort',  JText::_('EB_LAST_NAME'), 'a.last_name', $this->lists['order_Dir'], $this->lists['order'] ); ?>
			</th>
			<th class="title" style="text-align: left;" width="15%">
				<?php echo JHTML::_('grid.sort',  JText::_('EB_EVENT'), 'b.title', $this->lists['order_Dir'], $this->lists['order'] ); ?>
			</th>			
			<th width="8%" class="title" nowrap="nowrap">
				<?php echo JHTML::_('grid.sort',  JText::_('EB_PHONE'), 'a.phone', $this->lists['order_Dir'], $this->lists['order'] ); ?>
			</th>			
			<th width="10%" class="title" nowrap="nowrap">
				<?php echo JHTML::_('grid.sort',  JText::_('EB_EMAIL'), 'a.email', $this->lists['order_Dir'], $this->lists['order'] ); ?>
			</th>
			<th width="7%" class="title" nowrap="nowrap">
				<?php echo JHTML::_('grid.sort',  JText::_('EB_NUMBER_REGISTRANTS'), 'a.number_registrants', $this->lists['order_Dir'], $this->lists['order'] ); ?>
			</th>
			<th width="10%" class="title" nowrap="nowrap">
				<?php echo JHTML::_('grid.sort',  JText::_('EB_REGISTRATION_DATE'), 'a.register_date', $this->lists['order_Dir'], $this->lists['order'] ); ?>
			</th>																																																				
			<th width="3%" class="title" nowrap="nowrap">
				<?php echo JHTML::_('grid.sort',  JText::_('EB_ID'), 'a.id', $this->lists['order_Dir'], $this->lists['order'] ); ?>
			</th>
		</tr>
	</thead>
	<tfoot>
		<tr>			
			<td colspan="10">
				<?php echo $this->pagination->getListFooter(); ?>
			</td>							
		</tr>
	</tfoot>
	<tbody>
	<?php
	$k = 0;
	for ($i=0, $n=count( $this->items ); $i < $n; $i++)
	{
		$row = &$this->items[$i];
		$link 	= JRoute::_( 'index.php?option=com_eventbooking&task=edit_waiting&cid[]='. $row->id );
		$checked 	= JHTML::_('grid.id',   $i, $row->id );								
		?>
		<tr class="<?php echo "row$k"; ?>">
			<td align="center">
				<?php echo $this->pagination->getRowOffset( $i ); ?>
			</td>
			<td align="center">
				<?php echo $checked; ?>
			</td>				
			<td>
				<a href="<?php echo $link; ?>">
					<?php echo $row->first_name ?>
				</a>
			</td>			
			<td>
				<?php echo $row->last_name ; ?>
			</td>
			<td>
				<a href="index.php?option=com_eventbooking&task=edit_event&cid[]=<?php echo $row->event_id; ?>"><?php echo $row->title ; ?></a>
			</td>	
			<td>
				<?php echo $row->phone ; ?>
			</td>					
			<td style="text-align: center;">
				<?php echo $row->email; ?>
			</td>							
			<td align="center" style="font-weight: bold;">
				<?php echo $row->number_registrants; ?>				
			</td>								
			<td style="text-align: center;">
				<?php echo JHTML::_('date', $row->register_date, $this->config->date_format, null); ?>
			</td>							
			<td style="text-align: center;">
				<?php echo $row->id; ?>
			</td>
		</tr>
		<?php
		$k = 1 - $k;
	}
	?>
	</tbody>
	</table>
	</div>
	<input type="hidden" name="option" value="com_eventbooking" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />	
	<?php echo JHTML::_( 'form.token' ); ?>			
</form>