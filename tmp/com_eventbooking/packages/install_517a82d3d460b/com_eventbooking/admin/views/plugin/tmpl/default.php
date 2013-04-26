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
	
JToolBarHelper::title(   JText::_( 'EB_PLUGIN' ).': <small><small>[edit]</small></small>' );
JToolBarHelper::save('save_plugin');	
JToolBarHelper::cancel('cancel_plugin');

JHTML::_('behavior.tooltip');
JHTML::_( 'behavior.modal' ) ;	
?>
<script type="text/javascript">
	Joomla.submitbutton = function(pressbutton) {
		var form = document.adminForm;
		if (pressbutton == 'cancel_plugin') {
			Joomla.submitform( pressbutton );
			return;				
		} else {
			Joomla.submitform( pressbutton );
		}
	}
</script>
<form action="index.php" method="post" name="adminForm" id="adminForm">
<div class="row-fluid">
<div class="span7">
	<fieldset class="adminform">
		<legend><?php echo JText::_('EB_PLUGIN_DETAIL'); ?></legend>
			<table class="admintable">
				<tr>
					<td width="100" class="key">
						<?php echo  JText::_('EB_NAME'); ?>
					</td>
					<td>
						<?php echo $this->item->name ; ?>
					</td>
				</tr>
				<tr>
					<td width="100" class="key">
						<?php echo  JText::_('EB_TITLE'); ?>
					</td>
					<td>
						<input class="text_area" type="text" name="title" id="title" size="40" maxlength="250" value="<?php echo $this->item->title;?>" />
					</td>
				</tr>					
				<tr>
					<td class="key">
						<?php echo JText::_('EB_AUTHOR'); ?>
					</td>
					<td>
						<input class="text_area" type="text" name="author" id="author" size="40" maxlength="250" value="<?php echo $this->item->author;?>" />
					</td>
				</tr>
				<tr>
					<td class="key">
						<?php echo JText::_('Creation date'); ?>
					</td>
					<td>
						<?php echo $this->item->creation_date; ?>
					</td>
				</tr>
				<tr>
					<td class="key">
						<?php echo JText::_('Copyright') ; ?>
					</td>
					<td>
						<?php echo $this->item->copyright; ?>
					</td>
				</tr>	
				<tr>
					<td class="key">
						<?php echo JText::_('License'); ?>
					</td>
					<td>
						<?php echo $this->item->license; ?>
					</td>
				</tr>							
				<tr>
					<td class="key">
						<?php echo JText::_('Author email'); ?>
					</td>
					<td>
						<?php echo $this->item->author_email; ?>
					</td>
				</tr>
				<tr>
					<td class="key">
						<?php echo JText::_('Author URL'); ?>
					</td>
					<td>
						<?php echo $this->item->author_url; ?>
					</td>
				</tr>				
				<tr>
					<td class="key">
						<?php echo JText::_('Version'); ?>
					</td>
					<td>
						<?php echo $this->item->version; ?>
					</td>
				</tr>
				<tr>
					<td class="key">
						<?php echo JText::_('Description'); ?>
					</td>
					<td>
						<?php echo $this->item->description; ?>
					</td>
				</tr>
				<tr>
					<td class="key">
						<?php echo JText::_('Published'); ?>
					</td>
					<td>
						<?php					
							echo $this->lists['published'];					
						?>						
					</td>
				</tr>
		</table>
	</fieldset>				
</div>						
<div class="span5">
	<fieldset class="adminform">
		<legend><?php echo JText::_('Plugins Parameter'); ?></legend>
		<?php
			foreach ($this->form->getFieldset('basic') as $field) {
			?>
			<div class="control-group">
				<div class="control-label">
					<?php echo $field->label ;?>
				</div>					
				<div class="controls">
					<?php echo  $field->input ; ?>
				</div>
			</div>	
		<?php
			}					
		?>				
	</fieldset>				
</div>
</div>		
<div class="clearfix"></div>	
	<?php echo JHTML::_( 'form.token' ); ?>
	<input type="hidden" name="option" value="com_eventbooking" />
	<input type="hidden" name="cid[]" value="<?php echo $this->item->id; ?>" />	
	<input type="hidden" name="task" value="" />
</form>