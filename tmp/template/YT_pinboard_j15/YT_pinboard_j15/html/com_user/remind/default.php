<?php
/**
* @package   yoo_pinboard
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   YOOtheme Proprietary Use License (http://www.yootheme.com/license)
*/

// no direct access
defined('_JEXEC') or die('Restricted access');
?>

<div class="joomla <?php echo $this->escape($this->params->get('pageclass_sfx')); ?>">
	
	<div class="user">
	
		<?php if ( $this->params->get( 'show_page_title' ) ) : ?>
		<h1 class="pagetitle">
			<?php echo $this->escape($this->params->get('page_title')); ?>
		</h1>
		<?php endif; ?>

		<p><?php echo JText::_('REMIND_USERNAME_DESCRIPTION'); ?></p>

		<form action="<?php echo JRoute::_( 'index.php?option=com_user&task=remindusername' ); ?>" method="post" class="josForm form-validate">
		<fieldset>
			<legend><?php echo JText::_( 'REMIND_USERNAME_EMAIL_TIP_TITLE' ) ?></legend>

			<div>
				<label for="email" class="hasTip" title="<?php echo JText::_('REMIND_USERNAME_EMAIL_TIP_TITLE'); ?>::<?php echo JText::_('REMIND_USERNAME_EMAIL_TIP_TEXT'); ?>"><?php echo JText::_('Email Address'); ?>:</label>
			<input id="email" name="email" type="text" class="required validate-email" />
			</div>
			<div>
				<button type="submit"><?php echo JText::_('Submit'); ?></button>
			</div>
		</fieldset>
		<?php echo JHTML::_( 'form.token' ); ?>
		</form>

	</div>
</div>