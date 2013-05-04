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

$edit		= JRequest::getVar('edit',true);
$text = !$edit ? JText::_( 'EB_NEW' ) : JText::_( 'EB_EDIT' );
JToolBarHelper::title(   JText::_( 'EB_CATEGORY' ).': <small><small>[ ' . $text.' ]</small></small>' );
JToolBarHelper::save('save_category');	
JToolBarHelper::apply('apply_category');
JToolBarHelper::cancel('cancel_category');
$editor = JFactory::getEditor(); 	
?>
<script type="text/javascript">
	Joomla.submitbutton = function(pressbutton) {
		var form = document.adminForm;
		if (pressbutton == 'cancel_category') {
			Joomla.submitform( pressbutton );
			return;				
		} else {
			<?php echo $editor->save('description'); ?>
			Joomla.submitform( pressbutton );
		}
	}
</script>
<form action="index.php" method="post" name="adminForm" id="adminForm">
<div class="row-fluid">	
	<table class="admintable adminform">
		<tr>
			<td width="100" class="key">
				<?php echo  JText::_('EB_NAME'); ?>
			</td>
			<td>
				<input class="text_area" type="text" name="name" id="name" size="40" maxlength="250" value="<?php echo $this->item->name;?>" />
			</td>
		</tr>			
		<tr>
			<td class="key">
				<?php echo  JText::_('EB_PARENT'); ?>
			</td>
			<td>
				<?php echo $this->lists['parent']; ?>	
			</td>				
		</tr>
		<tr>
			<td class="key">
				<?php echo  JText::_('EB_LAYOUT'); ?>
			</td>
			<td>
				<?php echo $this->lists['layout']; ?>	
			</td>				
		</tr>	
		<tr>
			<td class="key">
				<?php echo  JText::_('EB_ACCESS_LEVEL'); ?>
			</td>
			<td>
				<?php echo $this->lists['access']; ?>	
			</td>				
		</tr>
		<tr>
			<td class="key">
				<?php echo JText::_('EB_LANGUAGE'); ?>
			</td>
			<td>
				<?php echo $this->lists['language'] ; ?>
			</td>
		</tr>             	
		<tr>
			<td class="key">
				<?php echo JText::_('EB_COLOR'); ?>
			</td>
			<td>
				<input type="text" name="color_code" class="inputbox color {required:false}" value="<?php echo $this->item->color_code; ?>" size="10" />						
				<?php echo JText::_('EB_COLOR_EXPLAIN'); ?> 
			</td>
		</tr>										
		<tr>
			<td class="key">
				<?php echo JText::_('EB_DESCRIPTION'); ?>
			</td>
			<td>
				<?php echo $editor->display( 'description',  $this->item->description , '100%', '250', '75', '10' ) ; ?>
			</td>
		</tr>				
		<tr>
			<td class="key">
				<?php echo JText::_('EB_PUBLISHED'); ?>
			</td>
			<td>
				<?php echo $this->lists['published']; ?>
			</td>
		</tr>
	</table>										
</div>		
<div class="clearfix"></div>	
	<?php echo JHTML::_( 'form.token' ); ?>
	<input type="hidden" name="option" value="com_eventbooking" />
	<input type="hidden" name="cid[]" value="<?php echo $this->item->id; ?>" />
	<input type="hidden" name="task" value="" />
</form>