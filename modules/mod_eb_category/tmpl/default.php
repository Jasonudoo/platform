<?php
/**
 * @version		1.5.0
 * @package		Joomla
 * @subpackage	Event Booking
 * @author  Tuan Pham Ngoc
 * @copyright	Copyright (C) 2010 Ossolution Team
 * @license		GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die ( 'Restricted access');
if (count($rows)) {		
?>
	<ul class="menu">
		<?php				
			foreach ($rows  as $row) {
				if ($row->total_categories) {
	    			$link = JRoute::_('index.php?option=com_eventbooking&task=view_categories&category_id='.$row->id.'&Itemid='.$itemId);
	    		} else {
	    			$link = JRoute::_('index.php?option=com_eventbooking&task=view_category&category_id='.$row->id.'&Itemid='.$itemId);
	    		}
			?>
				<li>
					<a href="<?php echo $link; ?>"><?php echo $row->name; ?></a>
				</li>
			<?php	
			}
		?>			
	</ul>
<?php
}
?>					

