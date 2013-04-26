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
 * HTML View class for EventBooking component
 *
 * @static
 * @package		Joomla
 * @subpackage	EventBooking
 * @since 1.5
 */
class EventBookingViewCategory extends JViewLegacy
{

	function display($tpl = null)
	{
		$document = JFactory::getDocument();
		$document->addScript(JURI::base() . 'components/com_eventbooking/assets/js/colorpicker/jscolor.js');
		$db = JFactory::getDBO();
		$item = $this->get('Data');
		$options = array();
		$options[] = JHTML::_('select.option', '', JText::_('Default Layout'));
		$options[] = JHTML::_('select.option', 'table', JText::_('Table Layout'));
		$options[] = JHTML::_('select.option', 'calendar', JText::_('Calendar Layout'));
		$lists['layout'] = JHTML::_('select.genericlist', $options, 'layout', ' class="inputbox" ', 'value', 'text', $item->layout);
		$lists['parent'] = EventBookingHelper::parentCategories($item);
		$lists['published'] = JHTML::_('select.booleanlist', 'published', 'class="inputbox"', $item->published);
		$lists['access'] = JHtml::_('access.level', 'access', $item->access, ' class="inputbox" ', false);
		$lists['language'] = JHTML::_('select.genericlist', JHtml::_('contentlanguage.existing', true, true), 'language', ' class="inputbox" ', 
			'value', 'text', $item->language);
		$this->assignRef('item', $item);
		$this->assignRef('lists', $lists);
		parent::display($tpl);
	}
}