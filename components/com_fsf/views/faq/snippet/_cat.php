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
<?php ('_JEXEC') or die('Restricted access'); ?>
	    	<div class='faq_category <?php if ($this->view_mode_cat == "accordian") echo "accordion_toggler_$acl"; ?>' <?php if ($this->view_mode_cat == "accordian") echo " style='cursor: pointer;' "; ?> >

	    		<?php if ($cat['image']) : ?>
	    		<div class='faq_category_image'>
					<?php if (substr($cat['image'],0,1) == "/") : ?>
	    			<img src='<?php echo JURI::root( true ); ?><?php echo $cat['image']; ?>' width='64' height='64'>
	    			<?php else: ?>
	    			<img src='<?php echo JURI::root( true ); ?>/images/fsf/faqcats/<?php echo $cat['image']; ?>' width='64' height='64'>
	    			<?php endif; ?>
	    		</div>
	    		<?php endif; ?>

				<div class='faq_category_head'>
					<?php if ($cat['id'] == $this->curcatid) : ?><b><?php endif; ?>
					
					<?php if ($this->view_mode_cat == "popup") : ?>
	
						<a class="modal fsf_highlight" href='<?php echo JRoute::_( '&tmpl=component&limitstart=&catid=' . $cat['id'] . '&view_mode=' . $this->view_mode_incat ); ?>' rel="{handler: 'iframe', size: {x: 650, y: 375}}">
						   <?php echo $cat['title'] ?>
						</a>

					<?php elseif ($this->view_mode_cat == "accordian"): ?>

						<A class="fsf_highlight" href="javascript:function Z(){Z=''}Z()"><?php echo $cat['title'] ?></a>
						
					<?php else: ?>

						<A class="fsf_highlight" href='<?php echo JRoute::_( '&limitstart=&catid=' . $cat['id'] );?>'><?php echo $cat['title'] ?></a>

					<?php endif; ?>
					
					<?php if ($cat['id'] == $this->curcatid) : ?></b><?php endif; ?>
				</div>

				<div class='faq_category_desc'><?php echo $cat['description']; ?></div>

			</div>

			<!-- INLINE FAQS -->
			<?php if ($this->view_mode_cat == "inline" || $this->view_mode_cat == "accordian") : ?>
			<div class='faq_category_faqlist <?php if ($this->view_mode_cat == "accordian") echo "accordion_content_$acl"; ?>' id="faq_category_faqlist">
				<?php if ($this->view_mode_cat == "accordian") $acl = 2; ?>
				<?php $this->view_mode = $this->view_mode_incat; ?>
				<?php if (array_key_exists('faqs',$cat) && count($cat['faqs']) > 0): ?>
					<?php foreach ($cat['faqs'] as &$faq) : ?>
						<?php include JPATH_SITE.DS.'components'.DS.'com_fsf'.DS.'views'.DS.'faq'.DS.'snippet'.DS.'_faq.php' ?>
					<?php endforeach; ?>	
				<?php else: ?>
					<div class="fsf_faq_question" style="padding-bottom:5px;"><?php echo JText::_("NO_FAQS_FOUND_IN_THIS_CATEGORY");?></div>
					<div class="fsf_faq_answer"></div>
				<?php endif; ?>
				<?php if ($this->view_mode_cat == "accordian") $acl = 1; ?>
			</div>
			<?php endif; ?>				
			<!-- END INLINE FAQS -->

			<!--<div class='fsf_clear'></div>-->

