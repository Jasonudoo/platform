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

<?php echo JHTML::_( 'form.token' ); ?>

<script language="javascript" type="text/javascript">
<!--
function submitbutton(pressbutton) {
        var form = document.adminForm;
        if (pressbutton == 'cancel') {
                submitform( pressbutton );
                return;
        }

        <?php
        $editor =& JFactory::getEditor();
        echo $editor->save( 'description' );
        echo $editor->save( 'longdesc' );
        ?>
        submitform(pressbutton);
}
//-->
</script>

<form action="index.php" method="post" name="adminForm" id="adminForm">
<div class="col100 width-100">
	<fieldset class="adminform">
		<legend><?php echo JText::_("DETAILS"); ?></legend>

		<table class="admintable">
		<tr>
			<td width="135" align="right" class="key">
				<label for="word">
					<?php echo JText::_("WORD"); ?>:
				</label>
			</td>
			<td>
				<input class="text_area" type="text" name="word" id="word" size="32" maxlength="250" value="<?php echo JView::escape($this->glossary->word);?>" />
			</td>
		</tr>
		<tr>
			<td width="135" align="right" class="key">
				<label for="description">
					<?php echo JText::_("DESCRIPTION"); ?>:
				</label>
			</td>
			<td>
				<?php echo $editor->display('description', $this->glossary->description, '550', '400', '60', '20', array('pagebreak')); ?>
            </td>

		</tr>
		<tr>
			<td width="135" align="right" class="key">
				<label for="longdesc">
					<?php echo JText::_("LONG_DESCRIPTION"); ?>:
				</label>
			</td>
			<td>
				<?php echo $editor->display('longdesc', $this->glossary->longdesc, '550', '400', '60', '20', array('pagebreak')); ?>
            </td>

		</tr>
	</table>
	</fieldset>
</div>
<div class="clr"></div>

<input type="hidden" name="option" value="com_fsf" />
<input type="hidden" name="id" value="<?php echo $this->glossary->id; ?>" />
<input type="hidden" name="published" value="<?php echo $this->glossary->published; ?>" />
<input type="hidden" name="task" value="save" />
<input type="hidden" name="controller" value="glossary" />
</form>

