<?php
/**
 * @package	HikaShop for Joomla!
 * @version	2.1.2
 * @author	hikashop.com
 * @copyright	(C) 2010-2013 HIKARI SOFTWARE. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><br/>
<span id="hikashop_checkout_status">
	<?php
	$array=array();
	if(!empty($this->shipping_data)){
		$array[]= JText::sprintf('HIKASHOP_SHIPPING_METHOD_CHOSEN', '<span class="label label-info">'.$this->shipping_data->shipping_name.'</span>');
	}
	if(!empty($this->payment_data)){
		$array[]= JText::sprintf('HIKASHOP_PAYMENT_METHOD_CHOSEN', '<span class="label label-info">'.$this->payment_data->payment_name.'</span>');
	}
	echo implode('<br/>',$array);
	?>
</span>
<div class="clear_both"></div>
