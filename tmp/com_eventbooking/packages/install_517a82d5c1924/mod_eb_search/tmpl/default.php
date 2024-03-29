<?php
/**
 * @version		1.5.0
 * @package		Joomla
 * @subpackage	Event Booking
 * @author  Tuan Pham Ngoc
 * @copyright	Copyright (C) 2010 Ossolution Team
 * @license		GNU/GPL, see LICENSE.php
 */	
defined('_JEXEC') or die ;
$output = '<input name="search" id="search_eb_box" maxlength="50"  class="inputbox" type="text" size="20" value="'.$text.'"  onblur="if(this.value==\'\') this.value=\''.$text.'\';" onfocus="if(this.value==\''.$text.'\') this.value=\'\';" />';		
?>					
<form method="post" name="eb_search_form" id="eb_search_form" action="index.php?option=com_eventbooking&Itemid=<?php echo $itemId ; ?>">
    <table width="100%" class="search_table">
    	<tr>
    		<td>
    			<?php echo $output ; ?>	
    		</td>
    	</tr>
    	<?php
    	    if ($showCategory) {
    	    ?>
    	    	<tr>
    	    		<td>
    	    			<?php echo $lists['category_id'] ; ?>
    	    		</td>	    		
    	    	</tr>
    	    <?php    
    	    }
    	    if ($showLocation) {
    	    ?>
    	    	<tr>
    	    		<td>
    	    			<?php echo $lists['location_id'] ; ?>
    	    		</td>	    		
    	    	</tr>
    	    <?php    
    	    }
    	?>	
    	<tr>
    		<td>
    			<input type="button" class="button search_button" value="<?php echo JText::_('EB_SEARCH'); ?>" onclick="searchData();" /> 
    		</td>
    	</tr>
    </table>
    <input type="hidden" name="option" value="com_eventbooking" />
    <input type="hidden" name="Itemid" value="<?php echo $itemId ; ?>" />
    <input type="hidden" name="view" value="search" />
    
    <script language="javascript">
    	function searchData() {
        	var form = document.eb_search_form ;
        	if (form.search.value == '<?php echo $text ?>') {
            	form.search.value = '' ;
        	}
        	form.submit();
    	}
    </script>
    
</form>
