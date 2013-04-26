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
	ResetElement('visual');
//
	
	document.getElementById('tab_' + tabid).style.display = 'inline';
	document.getElementById('link_' + tabid).style.backgroundColor = '#f0f0ff';
	
	document.getElementById('link_' + tabid).onmouseover = function() {
	}
	document.getElementById('link_' + tabid).onmouseout = function() {
	}
}

window.addEvent('domready', function(){
	ShowTab('visual');
//
});

function setup_comments(cset)
{
	jQuery('#comments_' + cset + '_reset').click(function (ev) { 
		ev.stopPropagation();
		ev.preventDefault();
		if (confirm("Are you sure you wish to reset this custom template"))
		{
			jQuery('#comments_' + cset).val(jQuery('#comments_' + cset + '_default').val());
		}
	});
	
	jQuery('#comments_' + cset + '_use_custom').change(function (ev) {
		showhide_comments(cset);
	});
	showhide_comments(cset);
}

function showhide_comments(cset)
{
	var value = jQuery('#comments_' + cset + '_use_custom').attr('checked');
	if (value == "checked")
	{
		jQuery('#comments_' + cset + '_row').css('display','table-row');
		
		if (jQuery('#comments_' + cset).val() == "")
			jQuery('#comments_' + cset).val(jQuery('#comments_' + cset + '_default').val());
	} else {
		jQuery('#comments_' + cset + '_row').css('display','none');
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
}</style>
<form action="index.php" method="post" name="adminForm" id="adminForm">
<input type="hidden" name="what" value="save">
<input type="hidden" name="option" value="com_fsf" />
<input type="hidden" name="task" value="save" />
<input type="hidden" name="view" value="templates" />
<div class='ffs_tabs'>

<!--<a id='link_general' class='ffs_tab' href='#' onclick="ShowTab('general');return false;">General</a>-->

<?php // ?>

<a id='link_visual' class='ffs_tab' href='#' onclick="ShowTab('visual');return false;"><?php echo JText::_("VISUAL"); ?></a>

</div>

<?php // ?>
<?php //##NOT_TEST_END## ?>

<div id="tab_visual" style="display:none;" class="col100 width-100">


	<fieldset class="adminform">
		<legend><?php echo JText::_("CSS_SETTINGS"); ?></legend>
		<table class="admintable">
			<tr>
				<td align="right" class="key" style="width:250px;">	
						<?php echo JText::_("MAIN_CSS"); ?>:
				</td>
				<td>
					<textarea name="display_style" rows="10" cols="60"><?php echo $this->settings['display_style'] ?></textarea>
				</td>
				<td>
					<div class="fsf_help"><?php echo JText::_('TMPLHELP_display_style'); ?>
					

					</div>
				</td>
			</tr>
			<tr>
				<td align="right" class="key">	
						<?php echo JText::_("POPUP_CSS"); ?>:
				</td>
				<td>
					<textarea name="display_popup_style" rows="10" cols="60"><?php echo $this->settings['display_popup_style'] ?></textarea>
				</td>
				<td>
					<div class="fsf_help"><?php echo JText::_('TMPLHELP_display_popup_style'); ?>
					
					</div>
				</td>
			</tr>
			<tr>
				<td align="right" class="key">	
						<?php echo JText::_("PAGE_HEADER"); ?>:
				</td>
				<td>
					<textarea name="display_head" rows="10" cols="60"><?php echo $this->settings['display_head'] ?></textarea>
				</td>
				<td>
					<div class="fsf_help"><?php echo JText::_('TMPLHELP_display_head'); ?>
						
					</div>
				</td>
			</tr>
			<tr>
				<td align="right" class="key">	
						<?php echo JText::_("PAGE_FOOTER"); ?>:
				</td>
				<td>
					<textarea name="display_foot" rows="10" cols="60"><?php echo $this->settings['display_foot'] ?></textarea>
				</td>
				<td>
					<div class="fsf_help"><?php echo JText::_('TMPLHELP_display_foot'); ?></div>
				</td>
			</tr>
		</table>
	</fieldset>

	<fieldset class="adminform">
		<legend><?php echo JText::_("PAGE_TITLE_SETTINGS"); ?></legend>
		<table class="admintable">
			<tr>
				<td align="right" class="key" style="width:250px;">
					
						<?php echo JText::_("H1_STYLE"); ?>:
					
				</td>
				<td>
					<textarea name="display_h1" rows="5" cols="60"><?php echo $this->settings['display_h1'] ?></textarea>
				</td>
				<td>
					<div class="fsf_help"><?php echo JText::_('TMPLHELP_display_h1'); ?></div>
				</td>
			</tr>
			<tr>
				<td align="right" class="key">
					
						<?php echo JText::_("H2_STYLE"); ?>:
					
				</td>
				<td>
					<textarea name="display_h2" rows="5" cols="60"><?php echo $this->settings['display_h2'] ?></textarea>
				</td>
				<td>
					<div class="fsf_help"><?php echo JText::_('TMPLHELP_display_h2'); ?></div>
				</td>
			</tr>
			<tr>
				<td align="right" class="key">
					
						<?php echo JText::_("H3_STYLE"); ?>:
					
				</td>
				<td>
					<textarea name="display_h3" rows="5" cols="60"><?php echo $this->settings['display_h3'] ?></textarea>
				</td>
					<td>
					<div class="fsf_help"><?php echo JText::_('TMPLHELP_display_h3'); ?></div>
				</td>
			</tr>
			<tr>
				<td align="right" class="key">	
						<?php echo JText::_("POPUP_STYLE"); ?>:	
				</td>
				<td>
					<textarea name="display_popup" rows="5" cols="60"><?php echo $this->settings['display_popup'] ?></textarea>
				</td>
				<td>
					<div class="fsf_help"><?php echo JText::_('TMPLHELP_display_popup'); ?></div>
				</td>
			</tr>
		</table>
	</fieldset>
</div>

</form>

<form action="<?php echo JURI::root(); ?>/index.php?view=admin&layout=support&option=com_fsf&preview=1" method="post" name="adminForm2" id="adminForm2" target="_blank">
<input type="hidden" name="list_template" id="list_template" value="" />
<textarea style='display:none;' name="list_head" id="list_head"></textarea>
<textarea style='display:none;' name="list_row" id="list_row"></textarea>
</form>

<script>

//

</script>
