<?php
/**
 * @version		1.5.3
 * @package		Joomla
 * @subpackage	Event Booking
 * @author  Tuan Pham Ngoc
 * @copyright	Copyright (C) 2010 Ossolution Team
 * @license		GNU/GPL, see LICENSE.php
 */
// no direct access
defined('_JEXEC') or die();

/**
 * HTML View class for Events Booking Extension
 *
 * @static
 * @package		Joomla
 * @subpackage	Events Booking
 * @since 1.0
 */
class EventBookingViewPlugin extends JViewLegacy
{

	function display($tpl = null)
	{
		$item = $this->get('Data');
		$lists['published'] = JHTML::_('select.booleanlist', 'published', 'class="inputbox"', $item->published);
		
		$registry = new JRegistry();
		$registry->loadString($item->params);
		$data = new stdClass();
		$data->params = $registry->toArray();
		$form = JForm::getInstance('pmform', JPATH_ROOT . '/components/com_eventbooking/payments/' . $item->name . '.xml', array(), false, '//config');
		$form->bind($data);
		
		$this->assignRef('item', $item);
		$this->assignRef('lists', $lists);
		$this->assignRef('form', $form);
		parent::display($tpl);
	}
}