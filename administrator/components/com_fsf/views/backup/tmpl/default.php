<?php
/**
* @Copyright Freestyle Joomla (C) 2010
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*     
* This file is part of Freestyle FAQs
* This program is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation, either version 3 of the License, or
* (at your option) any later version.
* 
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
* 
* You should have received a copy of the GNU General Public License
* along with this program.  If not, see <http://www.gnu.org/licenses/>.
**/
?>
<?php defined('_JEXEC') or die('Restricted access'); ?>
<?php if ($this->log) : ?>
<h1>Your upgrade has been completed.</h1>
<h4>The log of this process is below.</h4>
<?php $logno = 1; ?>
<?php foreach ($this->log as &$log): ?>
	<div>
	<div style="margin:4px;font-size:115%;"><a href="#" onclick="ToggleLog('log<?php echo $logno; ?>')">+<?php echo $log['name']; ?></a></div>
	<div id="log<?php echo $logno; ?>" style="display:none;">
	<pre style="margin-left: 20px;border: 1px solid black;padding: 2px;background-color: ghostWhite;"><?php echo $log['log']; ?></pre>
	</div>
</div>
	<?php $logno++; ?>
<?php endforeach; ?>

<script>
function ToggleLog(log)
{
	if (document.getElementById(log).style.display == "inline")
	{
		document.getElementById(log).style.display = 'none';
	} else {
		document.getElementById(log).style.display = 'inline';
	}
}
</script>
<?php else: ?>

<!---->

<h1><?php echo JText::_("UPDATE"); ?></h1>
<a href='<?php echo JRoute::_("index.php?option=com_fsf&view=backup&task=update"); ?>'><?php echo JText::_("PROCESS_FREESTYLE_JOOMLA_INSTALL_UPDATE"); ?></a><br />&nbsp;<br />

<h1><?php echo JText::_("BACKUP_DATABASE"); ?></h1>
<a href='<?php echo JRoute::_("index.php?option=com_fsf&view=backup&task=backup"); ?>'><?php echo JText::_("DOWNLOAD_BACKUP_NOW"); ?></a><br />&nbsp;<br />

<h1><?php echo JText::_("RESTORE_DATABASE"); ?></h1>
<div style="color:red; font-size:150%"><?php echo JText::_("PLEASE_NOTE_THE_WILL_OVERWRITE_AND_EXISTING_DATA_FOR_FREESTYLE_FAQS"); ?></div>

<?php // ?>

<form action="<?php echo JRoute::_("index.php?option=com_fsf&view=backup&task=restore"); ?>"  method="post" name="adminForm2" id="adminForm2" enctype="multipart/form-data"></::>
<input type="file" id="filedata" name="filedata" /><input type="submit" name="Restore" value="<?php echo JText::_("RESTORE"); ?>">
</form>

<form action="index.php" method="post" name="adminForm" id="adminForm">
<input type="hidden" name="option" value="com_fsf" />
<input type="hidden" name="task" id="task" value="" />
<input type="hidden" name="view" value="backup" />
</form>
<?php endif; ?>