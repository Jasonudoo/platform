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
class HikashopProductType{
	function load(){
		$this->values = array();
		$this->values[] = JHTML::_('select.option', 'all',JText::_('HIKA_ALL') );
		$this->values[] = JHTML::_('select.option', 'main',JText::_('PRODUCTS'));
		$this->values[] = JHTML::_('select.option', 'variant',JText::_('VARIANTS'));		
	}
	function display($map,$value){
		$this->load();
		return JHTML::_('select.genericlist',   $this->values, $map, 'class="inputbox" size="1" onchange="document.adminForm.submit( );"', 'value', 'text', $value );
	}
}
