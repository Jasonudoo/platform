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
<?php

// No direct access

defined('_JEXEC') or die('Restricted access'); ?>
<?php echo FSF_Helper::PageStyle(); ?>
<?php echo FSF_Helper::PageTitle("FREQUENTLY_ASKED_QUESTIONS","TAGS"); ?>

	<div class="fsf_spacer"></div>
	<?php echo FSF_Helper::PageSubTitle("PLEASE_SELECT_A_TAG"); ?>

	<div class='faq_category'>
	    <div class='faq_category_image'>
			<img src='<?php echo JURI::root( true ); ?>/components/com_fsf/assets/images/tags-64x64.png' width='64' height='64'>
	    </div>
	    <div class='fsf_spacer contentheading' style="padding-top:6px;padding-bottom:6px;">
			<?php echo JText::_("FAQS"); ?> <?php echo JText::_('TAGS'); ?>
		</div>
	</div>
	<div class='fsf_clear'></div>
	
	<div class='fsf_faqs' id='fsf_faqs'>
	<?php if (count($this->tags)) foreach ($this->tags as $tag) : ?>
		<div class='fsf_faq'>
			<div class="fsf_faq_question">
				<a class='fsf_highlight' href='<?php echo FSFRoute::_('index.php?option=com_fsf&view=faq&tag=' . urlencode($tag->tag) . '&Itemid=' . JRequest::getVar('Itemid')); ?>'>
					<?php echo $tag->tag; ?>
				</a>
			</div>
		</div>	
	<?php endforeach; ?>
	<?php if (count($this->tags) == 0): ?>
	<div class="fsf_no_results"><?php echo JText::_("NO_TAGS_FOUND");?></div>
	<?php endif; ?>
	</div>
	
<?php include JPATH_SITE.DS.'components'.DS.'com_fsf'.DS.'_powered.php'; ?>

<?php echo FSF_Helper::PageStyleEnd(); ?>
