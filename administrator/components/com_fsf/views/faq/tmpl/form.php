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
        echo $editor->save( 'answer' );
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
				<label for="question">
					<?php echo JText::_("CATEGORY"); ?>:
				</label>
			</td>
			<td>
				<?php echo $this->lists['catid']; ?>
			</td>
		</tr>		<tr>
			<td width="135" align="right" class="key">
				<label for="question">
					<?php echo JText::_("QUESTION"); ?>:
				</label>
			</td>
			<td>
				<textarea name="question" id="question" cols="80" rows="4" style="width:544px;"><?php echo JView::escape($this->faq->question);?></textarea>
			</td>
		</tr>
		<tr>
			<td width="135" align="right" class="key">
				<label for="answer">
					<?php echo JText::_("ANSWER"); ?>:
				</label>
			</td>
			<td>
				<?php
				$editor =& JFactory::getEditor();
                $text = $this->faq->answer;
                if ($this->faq->fullanswer)
                {
                    $text .= '<hr id="system-readmore" />';
                    $text .= $this->faq->fullanswer;                     
                }
				echo $editor->display('answer', $text, '550', '400', '60', '20', array('pagebreak'));
				?>
            </td>

		</tr>		<tr>
			<td width="135" align="right" class="key">
				<label for="answer">
					<?php echo JText::_("TAGS"); ?>:
				</label>
			</td>
			<td>
				<textarea name='tags' id='tags' cols="100" rows="10"><?php foreach ($this->tags as &$tag) echo $tag->tag . "\n"; ?></textarea>
            </td>
			<td>
				<?php echo JText::_('TAG_HELP'); ?>
			</td>
		</tr>
	</table>
	</fieldset>
</div>
<div class="clr"></div>

<input type="hidden" name="option" value="com_fsf" />
<input type="hidden" name="id" value="<?php echo $this->faq->id; ?>" />
<input type="hidden" name="ordering" value="<?php echo $this->faq->ordering; ?>" />
<input type="hidden" name="published" value="<?php echo $this->faq->published; ?>" />
<input type="hidden" name="task" value="save" />
<input type="hidden" name="controller" value="faq" />
</form>

