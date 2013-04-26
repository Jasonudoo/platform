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
<script>
function ResetElement(tabid)
{
	document.getElementById('tab_' + tabid).style.display = 'none';
	document.getElementById('link_' + tabid).style.backgroundColor = '';

	document.getElementById('link_' + tabid).onmouseover = function() {
		this.style.backgroundColor='<?php echo FSF_Settings::get('css_hl'); ?>';
	}
	document.getElementById('link_' + tabid).onmouseout = function() {
		this.style.backgroundColor='';
	}

}
function ShowTab(tabid)
{
	ResetElement('general');
//
//##NOT_TEST_END##
	ResetElement('visual');
//##NOT_TEST_START##
	ResetElement('glossary');
	ResetElement('faq');
//##NOT_TEST_END##
	
	document.getElementById('tab_' + tabid).style.display = 'inline';
	document.getElementById('link_' + tabid).style.backgroundColor = '#f0f0ff';
	
	document.getElementById('link_' + tabid).onmouseover = function() {
	}
	document.getElementById('link_' + tabid).onmouseout = function() {
	}
}
</script>

<style>
.fsf_custom_warn
{
	color: red;
}
.fsf_help
{
	border: 1px solid #CCC;
	float: left;
	padding: 3px;
	background-color: #F8F8FF;
}
.admintable td
{
	border-bottom: 1px solid #CCC;
	padding-bottom: 4px;
	padding-top: 2px;
}
</style>
<form action="index.php" method="post" name="adminForm" id="adminForm">
<input type="hidden" name="what" value="save">
<input type="hidden" name="option" value="com_fsf" />
<input type="hidden" name="task" value="save" />
<input type="hidden" name="view" value="settings" />
<input type="hidden" name="version" value="<?php echo $this->settings['version']; ?>" />
<input type="hidden" name="fsj_username" value="<?php echo $this->settings['fsj_username']; ?>" />
<input type="hidden" name="fsj_apikey" value="<?php echo $this->settings['fsj_apikey']; ?>" />
<input type="hidden" name="content_unpublished_color" value="<?php echo $this->settings['content_unpublished_color']; ?>" />
<div class='ffs_tabs'>

<!--<a id='link_general' class='ffs_tab' href='#' onclick="ShowTab('general');return false;">General</a>-->

<a id='link_general' class='ffs_tab' href='#' onclick="ShowTab('general');return false;"><?php echo JText::_("GENERAL_SETTINGS"); ?></a> 
<?php // ?>
<a id='link_visual' class='ffs_tab' href='#' onclick="ShowTab('visual');return false;"><?php echo JText::_("VISUAL"); ?></a>
<?php //##NOT_TEST_START## ?>
<a id='link_glossary' class='ffs_tab' href='#' onclick="ShowTab('glossary');return false;"><?php echo JText::_("GLOSSARY"); ?></a>
<a id='link_faq' class='ffs_tab' href='#' onclick="ShowTab('faq');return false;"><?php echo JText::_("FAQS"); ?></a> 
<?php //##NOT_TEST_END## ?>

</div>

<div id="tab_general" class="col100 width-100">

	<fieldset class="adminform">
		<legend><?php echo JText::_("GENERAL_SETTINGS"); ?></legend>

		<table class="admintable">
			<tr>
				<td align="right" class="key" style="width:250px;">
					<?php echo JText::_("HIDE_POWERED"); ?>:
				</td>
				<td style="width:250px;">
					<input type='checkbox' name='hide_powered' value='1' <?php if ($this->settings['hide_powered'] == 1) { echo " checked='yes' "; } ?>>
				</td>
				<td>
					<div class='fsf_help'><?php echo JText::_('SETHELP_hide_powered'); ?></div>
				</td>
			</tr>
		</table>
	</fieldset>
<?php // ?>
</div>

<?php // ?>
<?php //##NOT_TEST_END## ?>

<div id="tab_visual" style="display:none;" class="col100 width-100">

	<fieldset class="adminform">
		<legend><?php echo JText::_("VISUAL_SETTINGS"); ?></legend>
		<table class="admintable">
			<tr>
				<td align="right" class="key" style="width:250px;">
					
						<?php echo JText::_("USE_SKIN_STYLING_FOR_PAGEINATION_CONTROLS"); ?>:
					
				</td>
				<td style="width:250px;">
					<input type='checkbox' name='skin_style' value='1' <?php if ($this->settings['skin_style'] == 1) { echo " checked='yes' "; } ?>>
					</td>
				<td>
					<div class='fsf_help'><?php echo JText::_('SETHELP_skin_style'); ?></div>
			</td>
			</tr>
			<tr>
				<td align="right" class="key" style="width:250px;">
					
					<?php echo JText::_("USE_JOOMLA_SETTING_FOR_PAGE_TITLE_VISIBILITY"); ?>:
					
				</td>
				<td style="width:250px;">
					<input type='checkbox' name='use_joomla_page_title_setting' value='1' <?php if ($this->settings['use_joomla_page_title_setting'] == 1) { echo " checked='yes' "; } ?>>
					</td>
				<td>
					<div class='fsf_help'><?php echo JText::_('SETHELP_use_joomla_page_title_setting'); ?></div>
			</td>
			</tr>
		</table>
	</fieldset>
	<fieldset class="adminform">
		<legend><?php echo JText::_("CSS_SETTINGS"); ?></legend>
		<table class="admintable">
			<tr>
				<td align="right" class="key" style="width:250px;">
					
						<?php echo JText::_("HIGHLIGHT_COLOUR"); ?>:
					
				</td>
				<td style="width:250px;">
					<input name='css_hl' value='<?php echo $this->settings['css_hl'] ?>'>
					&nbsp;
					<input type="button" value="Color picker" onclick="showColorPicker(this,document.forms[0].css_hl)">
				</td>
				<td>
					<div class='fsf_help'><?php echo JText::_('SETHELP_css_hl'); ?></div>
				</td>
			</tr>
			<tr>
				<td align="right" class="key">
					
						<?php echo JText::_("BORDER_COLOUR"); ?>:
					
				</td>
				<td>
					<input name='css_bo' value='<?php echo $this->settings['css_bo'] ?>'>
					&nbsp;
					<input type="button" value="Color picker" onclick="showColorPicker(this,document.forms[0].css_bo)">
					</td>
				<td>
					<div class='fsf_help'><?php echo JText::_('SETHELP_css_bo'); ?></div>
			</td>
			</tr>
			<tr>
				<td align="right" class="key">
					
						<?php echo JText::_("TAB_BACKGROUND_COLOUR"); ?>:
					
				</td>
				<td>
					<input name='css_tb' value='<?php echo $this->settings['css_tb'] ?>'>
					&nbsp;
					<input type="button" value="Color picker" onclick="showColorPicker(this,document.forms[0].css_tb)">
				</td>
				<td>
					<div class='fsf_help'><?php echo JText::_('SETHELP_css_tb'); ?></div>
				</td>
			</tr>
		</table>
	</fieldset>
<?php // ?>	
</div>
<?php //##NOT_TEST_START## ?>

<div id="tab_glossary" class="col100 width-100">

	<fieldset class="adminform">
		<legend><?php echo JText::_("GLOSSARY_SETTINGS"); ?></legend>

		<table class="admintable">
			<tr>
				<td align="right" class="key" style="width:250px;">
						<?php echo JText::_("USE_GLOSSARY_ON_FAQS"); ?>:
					
				</td>
				<td style="width:250px;">
					<input type='checkbox' name='glossary_faqs' value='1' <?php if ($this->settings['glossary_faqs'] == 1) { echo " checked='yes' "; } ?>>
				</td>
				<td>
					<div class='fsf_help'><?php echo JText::_('SETHELP_glossary_faqs'); ?></div>
				</td>
			</tr>
<?php // ?>
			<tr>
				<td align="right" class="key">
						<?php echo JText::_("LINK_ITEMS_TO_GLOSSARY_PAGE"); ?>:
					
				</td>
				<td>
					<input type='checkbox' name='glossary_link' value='1' <?php if ($this->settings['glossary_link'] == 1) { echo " checked='yes' "; } ?>>
				</td>
				<td>
					<div class='fsf_help'><?php echo JText::_('SETHELP_glossary_link'); ?></div>
				</td>
			</tr>
			<tr>
				<td align="right" class="key">
						<?php echo JText::_("SHOW_GLOSSARY_WORD_IN_TOOLTIP"); ?>:
					
				</td>
				<td>
					<input type='checkbox' name='glossary_title' value='1' <?php if ($this->settings['glossary_title'] == 1) { echo " checked='yes' "; } ?>>
				</td>
				<td>
					<div class='fsf_help'><?php echo JText::_('SETHELP_glossary_title'); ?></div>
				</td>
			</tr>
			<tr>
				<td align="right" class="key">
					<?php echo JText::_("GLOSSARY_USE_CONTENT_PLUGINS"); ?>:
					
				</td>
				<td>
					<input type='checkbox' name='glossary_use_content_plugins' value='1' <?php if ($this->settings['glossary_use_content_plugins'] == 1) { echo " checked='yes' "; } ?>>
				</td>
				<td>
					<div class='fsf_help'><?php echo JText::_('SETHELP_glossary_use_content_plugins'); ?></div>
				</td>
			</tr>
		</table>
	</fieldset>
</div>

<div id="tab_faq" class="col100 width-100">

	<fieldset class="adminform">
		<legend><?php echo JText::_("FAQ_SETTINGS"); ?></legend>

		<table class="admintable">
			<tr>
				<td align="right" class="key" style="width:250px;">
						<?php echo JText::_("FAQS_POPUP_WIDTH"); ?>:
					
				</td>
				<td style="width:250px;">
					<input name='faq_popup_width' value='<?php echo $this->settings['faq_popup_width'] ?>'>
				</td>
				<td>
					<div class='fsf_help'><?php echo JText::_('SETHELP_faq_popup_width'); ?></div>
				</td>
			</tr>
			<tr>
				<td align="right" class="key" style="width:250px;">
						<?php echo JText::_("FAQS_POPUP_HEIGHT"); ?>:
					
				</td>
				<td>
					<input name='faq_popup_height' value='<?php echo $this->settings['faq_popup_height'] ?>'>
				</td>
				<td>
					<div class='fsf_help'><?php echo JText::_('SETHELP_faq_popup_height'); ?></div>
				</td>
			</tr>
			<tr>
				<td align="right" class="key" style="width:250px;">
						<?php echo JText::_("FAQS_POPUP_INNER_WIDTH"); ?>:
					
				</td>
				<td>
					<input name='faq_popup_inner_width' value='<?php echo $this->settings['faq_popup_inner_width'] ?>'>
				</td>
				<td>
					<div class='fsf_help'><?php echo JText::_('SETHELP_faq_popup_inner_width'); ?></div>
				</td>
			</tr>
			<tr>
				<td align="right" class="key">
					<?php echo JText::_("FAQ_USE_CONTENT_PLUGINS"); ?>:
					
				</td>
				<td>
					<input type='checkbox' name='faq_use_content_plugins' value='1' <?php if ($this->settings['faq_use_content_plugins'] == 1) { echo " checked='yes' "; } ?>>
				</td>
				<td>
					<div class='fsf_help'><?php echo JText::_('SETHELP_faq_use_content_plugins'); ?></div>
				</td>
			</tr>
			<tr>
				<td align="right" class="key">
					<?php echo JText::_("FAQ_USE_CONTENT_PLUGINS_LIST"); ?>:
					
				</td>
				<td>
					<input type='checkbox' name='faq_use_content_plugins_list' value='1' <?php if ($this->settings['faq_use_content_plugins_list'] == 1) { echo " checked='yes' "; } ?>>
				</td>
				<td>
					<div class='fsf_help'><?php echo JText::_('SETHELP_faq_use_content_plugins_list'); ?><div>
				</td>
			</tr>
		</table>
	</fieldset>
</div>
<?php //##NOT_TEST_END## ?>

</form>

<script>

function testreference()
{
	$('testref').innerHTML = "<?php echo JText::_('PLEASE_WAIT'); ?>";
	var format = $('support_reference').value
	var url = '<?php echo JRoute::_("index.php?option=com_fsf&view=settings&what=testref",false); ?>&ref=' + format;
	
<?php if (FSF_Helper::Is16()): ?>
	$('testref').load(url);
<?php else: ?>
	new Ajax(url, {
	method: 'get',
	update: $('testref')
	}).request();
<?php endif; ?>
}

window.addEvent('domready', function(){
	ShowTab('general');
});
 
</script>
