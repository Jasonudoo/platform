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
<form action="<?php echo JRoute::_( 'index.php?option=com_fsf&task=pick&controller=faq&tmpl=component' );?>" method="post" name="adminForm">
<?php $ordering = ($this->lists['order'] == "ordering"); ?>
<div id="editcell">
	<table>
		<tr>
			<td width="100%">
				<?php echo JText::_("FILTER"); ?>:
				<input type="text" name="search" id="search" value="<?php echo JView::escape($this->lists['search']);?>" class="text_area" onchange="document.adminForm.submit();" title="<?php echo JText::_("FILTER_BY_TITLE_OR_ENTER_ARTICLE_ID");?>"/>
				<button onclick="this.form.submit();"><?php echo JText::_("GO"); ?></button>
				<button onclick="document.getElementById('search').value='';this.form.submit();this.form.getElementById('faq_cat_id').value='0';"><?php echo JText::_("RESET"); ?></button>
			</td>
			<td nowrap="nowrap">
				<?php
				echo $this->lists['cats'];
				?>
			</td>
		</tr>
	</table>

    <table class="adminlist">
    <thead>

        <tr>
			<th width="5">#</th>
            <th>
                <?php echo JHTML::_('grid.sort',   'Question', 'question', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
            </th>
			<th width="8%" class="title" nowrap="nowrap">
				<?php echo JHTML::_('grid.sort',   'Category', 'title', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
			</th>
		</tr>
    </thead>
    <?php

    $k = 0;
    for ($i=0, $n=count( $this->data ); $i < $n; $i++)
    {
        $row =& $this->data[$i];
        $checked    = JHTML::_( 'grid.id', $i, $row->id );
        $link = JRoute::_( 'index.php?option=com_fsf&controller=faq&task=edit&cid[]='. $row->id );

    	$img = 'publish_g.png';
		$alt = JText::_("PUBLISHED");


		if ($row->published == 0)
		{
			$img = 'publish_x.png';
			$alt = JText::_("UNPUBLISHED");
		}

        ?>
        <tr class="<?php echo "row$k"; ?>">
            <td>
                <?php echo $row->id; ?>
            </td>
			<td>
			    <a style="cursor: pointer;" onclick="window.parent.jSelectArticle('<?php echo $row->id; ?>', '<?php echo str_replace(array("'", "\""), array("\\'", ""),$row->question); ?>', 'faqid');">
                            <?php echo htmlspecialchars($row->question, ENT_QUOTES, 'UTF-8'); ?></a>
			</td>
			<td>
			    <?php echo $row->title; ?>
			</td>
		</tr>
        <?php
        $k = 1 - $k;
    }
    ?>
	<tfoot>
		<tr>
			<td colspan="9"><?php echo $this->pagination->getListFooter(); ?></td>
		</tr>
	</tfoot>

    </table>
</div>

<input type="hidden" name="option" value="com_fsf" />
<input type="hidden" name="task" value="pick" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="tmpl" value="component" />
<input type="hidden" name="controller" value="faq" />
<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
</form>

