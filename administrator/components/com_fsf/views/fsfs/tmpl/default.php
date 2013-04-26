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

<table width="100%">
	<tr>
		<td width="55%" valign="top">
		
			<fieldset class="adminform">
				<legend><?php echo JText::_("GENERAL"); ?></legend>
		
				<?php $this->Item("SETTINGS","index.php?option=com_fsf&view=settings","settings","FSF_HELP_SETTINGS"); ?>
				<?php $this->Item("TEMPLATES","index.php?option=com_fsf&view=templates","templates","FSF_HELP_TEMPLATES"); ?>
<!---->
			</fieldset>
		
<!--##NOT_TEST_START##-->
<!---->		
			<fieldset class="adminform">
				<legend><?php echo JText::_("FAQS"); ?></legend>
				<?php $this->Item("FAQ_CATS","index.php?option=com_fsf&view=faqcats","categories","FSF_HELP_FAQ_CATS"); ?>
				<?php $this->Item("FAQS","index.php?option=com_fsf&view=faqs","faqs","FSF_HELP_FAQS"); ?>
	
			</fieldset>
		
<!---->

			<fieldset class="adminform">
				<legend><?php echo JText::_("GLOSSARY"); ?></legend>
				<?php $this->Item("GLOSSARY_ITEMS","index.php?option=com_fsf&view=glossarys","glossary","FSF_HELP_GLOSSARY_ITEMS"); ?>
	
			</fieldset>
		
<!---->	
<!--##NOT_TEST_END##-->
	
		</td>
		<td width="45%" valign="top">
		
<?php

$pane = &JPane::getInstance('tabs', array('allowAllClose' => true));
echo $pane->startPane("content-pane");


$title = "Version";
echo $pane->startPanel( $title, 'cpanel-panel-'.$title );

$ver_inst = FSFAdminHelper::GetInstalledVersion();
$ver_files = FSFAdminHelper::GetVersion();

?>
<?php if (FSFAdminHelper::IsFAQs()) :?>
<h3>If you like Freestyle FAQs please vote or review us at the <a href='http://extensions.joomla.org/extensions/directory-a-documentation/faq/11910' target="_blank">Joomla extensions directory</a></h3>
<?php elseif (FSFAdminHelper::IsTests()) :?>
<h3>If you like Freestyle Testimonials please vote or review us at the <a href='http://extensions.joomla.org/extensions/contacts-and-feedback/testimonials-a-suggestions/11911' target="_blank">Joomla extensions directory</a></h3>
<?php else: ?>
<h3>If you like Freestyle Support, please vote or review us at the <a href='http://extensions.joomla.org/extensions/clients-a-communities/help-desk/11912' target="_blank">Joomla extensions directory</a></h3>
<?php endif; ?>
<?php
echo "<h4>Currently Installed Verison : <b>$ver_files</b></h4>";
if ($ver_files != $ver_inst)
	echo "<h4>".JText::sprintf('INCORRECT_VERSION',JRoute::_('index.php?option=com_fsf&view=backup&task=update'))."</h4>";

?>
<div id="please_wait">Please wait while fetching latest version information...</div>

<iframe id="frame_version" height="300" width="100%" frameborder="0" border="0"></iframe>	
<?php
echo $pane->endPanel();

$title = "Announcements";
echo $pane->startPanel( $title, 'cpanel-panel-'.$title );
?>
<iframe id="frame_announce" height="600" width="100%" frameborder="0" border="0"></iframe>
<?php
echo $pane->endPanel();

$title = "Help";
echo $pane->startPanel( $title, 'cpanel-panel-'.$title );
?>
<iframe id="frame_help" height="600" width="100%" frameborder="0" border="0"></iframe>
<?php
echo $pane->endPanel();

echo $pane->endPane();

?>
		
		</td>	
	</tr>
</table>

<script>
jQuery(document).ready(function () {
	jQuery('#frame_version').attr('src',"http://freestyle-joomla.com/latestversion-fsf?ver=<?php echo FSFAdminHelper::GetVersion();?>");
	jQuery('#frame_version').load(function() 
    {
        jQuery('#please_wait').remove();
    });

	jQuery('.fsf_main_item').mouseenter(function () {
		jQuery(this).css('background-color', '<?php echo FSF_Settings::get('css_hl'); ?>');
	});
	jQuery('.fsf_main_item').mouseleave(function () {
		jQuery(this).css('background-color' ,'transparent');
	});

	jQuery('#frame_announce').attr('src',"http://freestyle-joomla.com/support/announcements?tmpl=component");
	jQuery('#frame_help').attr('src',"http://freestyle-joomla.com/comhelp/fsf-main-help");
});
</script>