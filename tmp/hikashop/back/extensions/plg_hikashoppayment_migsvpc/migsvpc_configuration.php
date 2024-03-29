<?php
/**
 * @package	HikaShop for Joomla!
 * @version	2.1.2
 * @author	hikashop.com
 * @copyright	(C) 2010-2013 HIKARI SOFTWARE. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><tr>
	<td class="key">
		<label for="data[payment][payment_params][url]">
			<?php echo JText::_( 'URL' ); ?>
		</label>
	</td>
	<td>
		<input type="text" name="data[payment][payment_params][url]" value="<?php echo @$this->element->payment_params->url; ?>" />
	</td>
</tr>
<tr>
	<td class="key">
		<label for="data[payment][payment_params][vpc_mode]">
			VPC Mode
		</label>
	</td>
	<td><?php
		$values = array(
			JHtml::_('select.option', 'dps', JText::_('HIKA_HOSTED')),
			JHtml::_('select.option', 'pay', JText::_('HIKA_REDIRECT'))
		);
		if(empty($this->element->payment_params->vpc_mode)) $this->element->payment_params->vpc_mode = 'dps';
		echo JHTML::_('hikaselect.radiolist', $values, "data[payment][payment_params][vpc_mode]" , '', 'value', 'text', @$this->element->payment_params->vpc_mode);
	?></td>
<tr>
	<td class="key">
		<label for="data[payment][payment_params][merchant_id]">
			<?php echo JText::_( 'ATOS_MERCHANT_ID' ); ?>
		</label>
	</td>
	<td>
		<input type="text" name="data[payment][payment_params][merchant_id]" value="<?php echo @$this->element->payment_params->merchant_id; ?>" />
	</td>
</tr>
<tr>
	<td class="key">
		<label for="data[payment][payment_params][locale]">
			Locale
		</label>
	</td>
	<td>
		<input type="text" name="data[payment][payment_params][locale]" value="<?php echo @$this->element->payment_params->locale; ?>" />
	</td>
</tr>
<tr>
	<td class="key">
		<label for="data[payment][payment_params][access_code]">
			<?php echo JText::_( 'ACCESS_CODE' ); ?>
		</label>
	</td>
	<td>
		<input type="text" name="data[payment][payment_params][access_code]" value="<?php echo @$this->element->payment_params->access_code; ?>" />
	</td>
</tr>
<tr>
	<td class="key">
		<label for="data[payment][payment_params][secure_secret]">
			Secure secret code
		</label>
	</td>
	<td>
		<input type="text" name="data[payment][payment_params][secure_secret]" value="<?php echo @$this->element->payment_params->secure_secret; ?>" />
	</td>
</tr>
<tr>
	<td class="key">
		<label for="data[payment][payment_params][ticket_info]">
			display payment information in Redirect mode
		</label>
	</td>
	<td>
		<?php echo JHTML::_('hikaselect.booleanlist', "data[payment][payment_params][ticket_info]" , '',@$this->element->payment_params->ticket_info ); ?>
	</td>
</tr>
<tr>
	<td class="key">
		<label for="data[payment][payment_params][currency]">
			<?php echo JText::_( 'CURRENCY' ); ?>
		</label>
	</td>
	<td>
		<input type="text" name="data[payment][payment_params][currency]" value="<?php echo @$this->element->payment_params->currency; ?>" />
	</td>
</tr>
<tr>
	<td class="key">
		<label for="data[payment][payment_params][ask_ccv]">
			Ask CCV
		</label>
	</td>
	<td>
		<?php echo JHTML::_('hikaselect.booleanlist', "data[payment][payment_params][ask_ccv]" , '',@$this->element->payment_params->ask_ccv ); ?>
	</td>
</tr>
<tr>
	<td class="key">
		<label for="data[payment][payment_params][debug]">
			<?php echo JText::_( 'DEBUG' ); ?>
		</label>
	</td>
	<td>
		<?php echo JHTML::_('hikaselect.booleanlist', "data[payment][payment_params][debug]" , '',@$this->element->payment_params->debug	); ?>
	</td>
</tr>
<tr>
	<td class="key">
		<label for="data[payment][payment_params][cancel_url]">
			<?php echo JText::_( 'CANCEL_URL' ); ?>
		</label>
	</td>
	<td>
		<input type="text" name="data[payment][payment_params][cancel_url]" value="<?php echo @$this->element->payment_params->cancel_url; ?>" />
	</td>
</tr>
<tr>
	<td class="key">
		<label for="data[payment][payment_params][return_url]">
			<?php echo JText::_( 'RETURN_URL' ); ?>
		</label>
	</td>
	<td>
		<input type="text" name="data[payment][payment_params][return_url]" value="<?php echo @$this->element->payment_params->return_url; ?>" />
	</td>
</tr>
<tr>
	<td class="key">
		<label for="data[payment][payment_params][invalid_status]">
			<?php echo JText::_( 'INVALID_STATUS' ); ?>
		</label>
	</td>
	<td>
		<?php echo $this->data['category']->display("data[payment][payment_params][invalid_status]",@$this->element->payment_params->invalid_status); ?>
	</td>
</tr>
<tr>
	<td class="key">
		<label for="data[payment][payment_params][verified_status]">
			<?php echo JText::_( 'VERIFIED_STATUS' ); ?>
		</label>
	</td>
	<td>
		<?php echo $this->data['category']->display("data[payment][payment_params][verified_status]",@$this->element->payment_params->verified_status); ?>
	</td>
</tr>
