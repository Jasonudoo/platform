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

/**
 * This field was written base on the category layout of docman extension 
 * @category	DOCman
 * @package		DOCman15
 * @copyright	Copyright (C) 2003 - 2009 Johan Janssens and Mathias Verraes. All rights reserved.
 * @license	    This file can not be redistributed without the written consent of the 
 				original copyright holder. This file is not licensed under the GPL. 
 * @link     	http://www.joomladocman.org
 */
	//Load greybox lib
$greyBox = JURI::base().'components/com_eventbooking/assets/js/greybox/';
?>
	<script type="text/javascript">
    	var GB_ROOT_DIR = "<?php echo $greyBox ; ?>";
	</script>
	<script type="text/javascript" src="<?php echo $greyBox; ?>/AJS.js"></script>
	<script type="text/javascript" src="<?php echo $greyBox; ?>/AJS_fx.js"></script>
	<script type="text/javascript" src="<?php echo $greyBox; ?>/gb_scripts.js"></script>
	<link href="<?php echo $greyBox; ?>/gb_styles.css" rel="stylesheet" type="text/css" />
<?php
	$width = (int) $this->config->map_width ;
	if (!$width) {
		$width = 500 ;	
	}							
	$height = (int) $this->config->map_height ;
	if (!$height) {
		$height = 450 ;
	}			
	$getDirectionLink = 'http://maps.google.com/maps?f=d&daddr='.$this->location->lat.','.$this->location->long.'('.addslashes($this->location->address.', '.$this->location->city.', '.$this->location->state.', '.$this->location->zip.', '.$this->location->country).')' ;	 
	if ($this->config->fix_next_button) {
        $action = 'index.php?option=com_eventbooking&Itemid='.$this->Itemid ;
    } else {
        $action = 'index.php' ;
    }
?>
<form method="post" name="adminForm" id="adminForm" action="<?php echo $action ; ?>">		
	<!-- Events List -->
	<?php if(count($this->items)) { ?>	
	    <div id="eb_docs">
	    <h1 class="location_header"><?php echo JText::sprintf('EB_EVENTS_FROM_LOCATION', $this->location->name); ?><a href="<?php echo JRoute::_('index.php?option=com_eventbooking&task=view_map&location_id='.$this->location->id.'&tmpl=component&format=html'); ?>" rel="gb_page_center[<?php echo $width; ?>, <?php echo $height; ?>]" title="<?php echo $this->location->name ; ?>" class="location_link view_map_link"><?php echo JText::_('EB_VIEW_MAP'); ?></a><a class="view_map_link" href="<?php echo $getDirectionLink ; ?>" target="_blank"><?php echo JText::_('EB_GET_DIRECTION'); ?></a></h1>	    	    	   
	    <?php
	        $activateWaitingList = $this->config->activate_waitinglist_feature ;
	        for ($i = 0 , $n = count($this->items) ;  $i < $n ; $i++) {
	        	$item = $this->items[$i] ;
	        	$canRegister = EventBookingHelper::acceptRegistration($item->id) ;	        			        	        	
	        	$url = JRoute::_('index.php?option=com_eventbooking&task=view_event&event_id='.$item->id.'&Itemid='.$this->Itemid);
	        	if (($item->event_capacity > 0) && ($item->event_capacity <= $item->total_registrants) && $activateWaitingList) {
	        	    $waitingList = true ;
	        	    $waitinglistUrl = JRoute::_('index.php?option=com_eventbooking&task=waitinglist_form&event_id='.$item->id.'&Itemid='.$this->Itemid);
	        	} else {
	        	    $waitingList = false ;
	        	}	        		        	
	        	include JPATH_COMPONENT.'/views/common/event.php';
	        }
	    ?>	    
	    </div>	    
    	<?php
    		if ($this->pagination->total > $this->pagination->limit) {
    		?>
    			<div align="center">
    				<?php echo $this->pagination->getListFooter(); ?>
    			</div>
    		<?php	
    		}
    	?>	    		   
	<?php } else { ?>
	    <br />
	    <div id="eb_docs">
	        <i><?php echo JText::_('EB_NO_EVENTS'); ?></i>
	    </div>
	<?php } ?>
	<input type="hidden" name="location_id" value="<?php echo $this->location->id; ?>" />
	<input type="hidden" name="view" value="location" />	
	<input type="hidden" name="Itemid" value="<?php echo $this->Itemid ; ?>" />
	<input type="hidden" name="option" value="com_eventbooking" />	
</form>