<?php
/**
 * @package	HikaShop for Joomla!
 * @version	2.1.2
 * @author	hikashop.com
 * @copyright	(C) 2010-2013 HIKARI SOFTWARE. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><?php
class hikashopCart_redirectType{
	function load(){
		$this->values = array();
		$this->values[] = JHTML::_('select.option', 'checkout',JText::_('ALWAYS_CHECKOUT'));
		$this->values[] = JHTML::_('select.option', 'stay',JText::_('ALWAYS_STAY'));
		$this->values[] = JHTML::_('select.option', 'stay_if_cart',JText::_('STAY_IF_CART_MODULE_DISPLAYED'));
		$this->values[] = JHTML::_('select.option', 'ask_user',JText::_('STAY_AND_DISPLAY_POPUP_NOTICE'));

	}
	function display($map,$value){
		$this->load();
		return JHTML::_('select.genericlist',   $this->values, $map, 'class="inputbox" size="1"', 'value', 'text', $value );
	}
}
