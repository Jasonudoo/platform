<?php
/**
* @package   Warp Theme Framework
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   YOOtheme Proprietary Use License (http://www.yootheme.com/license)
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

$script = '<!--
	function validateForm( frm ) {
		var valid = document.formvalidator.isValid(frm);
		if (valid == false) {
			// do field validation
			if (frm.email.invalid) {
				alert( "' . JText::_( 'Please enter a valid e-mail address.', true ) . '" );
			} else if (frm.text.invalid) {
				alert( "' . JText::_( 'CONTACT_FORM_NC', true ) . '" );
			}
			return false;
		} else {
			frm.submit();
		}
	}
	// -->';
$document =& JFactory::getDocument();
$document->addScriptDeclaration($script);
	
?>

<?php if(isset($this->error)) : ?>
	<?php echo $this->error; ?>
<?php endif; ?>

<form class="submission" action="<?php echo JRoute::_( 'index.php' );?>" method="post" name="emailForm" id="emailForm">
	<fieldset>
		<legend><?php echo JText::_('E-Mail'); ?></legend>

		<div>
			<label class="label-top" for="contact_name"><?php echo JText::_( 'Enter your name' );?></label>
			<input type="text" name="name" id="contact_name" size="30" class="inputbox" value="" />
		</div>
		
		<div>
			<label class="label-top" for="contact_email"><?php echo JText::_( 'Email address' );?></label>
			<input type="text" id="contact_email" name="email" size="30" value="" class="inputbox required validate-email" maxlength="100" />
		</div>
		
		<div>
			<label class="label-top" for="contact_subject"><?php echo JText::_( 'Message subject' );?></label>
			<input type="text" name="subject" id="contact_subject" size="30" class="inputbox" value="" />
		</div>
		
		<div>
			<label class="label-top" for="contact_text"><?php echo JText::_( 'Enter your message' );?></label>
			<textarea cols="50" rows="10" name="text" id="contact_text" class="inputbox required"></textarea>
		</div>
		
		<?php if ($this->contact->params->get( 'show_email_copy' )) : ?>
		<div>
			<input type="checkbox" name="email_copy" id="contact_email_copy" value="1" />
			<label for="contact_email_copy"><?php echo JText::_( 'EMAIL_A_COPY' ); ?></label>
		</div>
		<?php endif; ?>

	</fieldset>
	
	<div class="submit">
		<button class="button validate" type="submit"><?php echo JText::_('Send'); ?></button>
	</div>

	<input type="hidden" name="option" value="com_contact" />
	<input type="hidden" name="view" value="contact" />
	<input type="hidden" name="id" value="<?php echo $this->contact->id; ?>" />
	<input type="hidden" name="task" value="submit" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>