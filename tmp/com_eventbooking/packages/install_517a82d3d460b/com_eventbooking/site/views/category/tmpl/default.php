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
$param = null ;
if ($this->config->use_https) {
    $ssl = true ;
} else {
    $ssl = false ;
}
JHTML::_('behavior.modal');
?>
<form method="post" name="adminForm" id="adminForm" action="<?php echo JRoute::_('index.php?option=com_eventbooking&view=category&layout=default&category_id='.$this->category->id.'&Itemid='.$this->Itemid); ?>">	
	<div class="eb_cat">		
		<?php					       		    		      
			if($this->category->name != '') :
		        ?><h1 class="eb_title"><?php echo $this->category->name;?></h1><?php
		    endif;		
			if($this->category->description != '') :
				?><div class="eb_description"><?php echo $this->category->description;?></div><?php
			endif;
		?>
		<div class="clr"></div>
	</div>
	<!-- Events List -->
	<?php if(count($this->items)) { ?>
	    <div id="eb_docs">
	   		<h2 class="eb_title"><?php echo JText::_('EB_EVENTS'); ?></h2>	    	    	   
		    <?php		    	
		        $activateWaitingList = $this->config->activate_waitinglist_feature ;
		        for ($i = 0 , $n = count($this->items) ;  $i < $n ; $i++) {		        	
		        	$item = $this->items[$i] ;
		        	$canRegister = EventBookingHelper::acceptRegistration($item->id) ;	        			        	        	
		        	$url = JRoute::_('index.php?option=com_eventbooking&task=view_event&event_id='.$item->id.'&Itemid='.$this->Itemid);
		        	if (($item->event_capacity > 0) && ($item->event_capacity <= $item->total_registrants) && $activateWaitingList && !$item->user_registered) {
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
    			<div align="center" class="pagination">
    				<?php echo $this->pagination->getListFooter(); ?>
    			</div>
    		<?php	
    		}    	    		  
		} else { ?>	    
		    <div id="eb_docs">
		        <i><?php echo JText::_('EB_NO_EVENTS'); ?></i>
		    </div>
	<?php } ?>
	<input type="hidden" name="category_id" value="<?php echo $this->category->id; ?>" />
	<input type="hidden" name="view" value="category" />	
	<input type="hidden" name="Itemid" value="<?php echo $this->Itemid ; ?>" />
	<input type="hidden" name="option" value="com_eventbooking" />	
	<input type="hidden" name="id" value="0" />
	<input type="hidden" name="task" value="" />	
	<script type="text/javascript">
    	function cancelRegistration(registrantId) {
    		var form = document.adminForm ;			
    		if (confirm("<?php echo JText::_('EB_CANCEL_REGISTRATION_CONFIRM'); ?>")) {
    			form.task.value = 'cancel_registration' ;
    			form.id.value = registrantId ;
    			form.submit() ;
    		}	
    	}
	</script>	
</form>