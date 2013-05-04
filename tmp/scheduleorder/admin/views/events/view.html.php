<?php
/**
 * @version		1.0.0
 * @package		Joomla
 * @subpackage	Schedule Order
 * @author      Jason<jason@netwebx.com>
 * @copyright	Copyright (C) 2013 NetWebX.COM
 * @license		GNU/GPL, see LICENSE.php
 */
// no direct access
defined('_JEXEC') or die('Restricted Access');

/**
 * HTML View class for the Schedule Order component
 *
 * @static
 * @package		Joomla
 * @subpackage	Schedule Order
 * @since 1.0.0
 */
class ScheduleOrderViewCart extends JView
{

	function display($tpl = null)
	{
		$mainframe = JFactory::getApplication();
		$option = 'com_scheduleorder';
		
		$db = JFactory::getDBO();
		
		$filter_order = $mainframe->getUserStateFromRequest($option . 'event_filter_order', 'filter_order', 'id', 'cmd');
		$filter_order_Dir = $mainframe->getUserStateFromRequest($option . 'filter_order_Dir', 'filter_order_Dir', '', 'word');
		$search = $mainframe->getUserStateFromRequest($option . 'search', 'search', '', 'string');
		$search = JString::strtolower($search);
		$categoryId = $mainframe->getUserStateFromRequest($option . 'category_id', 'category_id', 0, 'int');
		$locationId = $mainframe->getUserStateFromRequest($option . 'location_id', 'location_id', 0, 'int');
		$pastEvent = $mainframe->getUserStateFromRequest($option . 'past_event', 'past_event', 1, 'int');
		$filter_state = $mainframe->getUserStateFromRequest($option . 'events_filter_state', 'filter_state', '');
		$language = $mainframe->getUserStateFromRequest($option . 'events_language', 'filter_language', '', 'string');
		
		$lists['category_id'] = EventBookingHelper::buildCategoryDropdown($categoryId, 'category_id', true);
		$lists['search'] = $search;
		$lists['order_Dir'] = $filter_order_Dir;
		$lists['order'] = $filter_order;
		$items = & $this->get('Data');
		$pagination = & $this->get('Pagination');
		$sql = 'SELECT id, name FROM #__eb_locations WHERE published=1';
		$db->setQuery($sql);
		$options = array();
		$options[] = JHTML::_('select.option', 0, JText::_('EB_SELECT_LOCATION'), 'id', 'name');
		$options = array_merge($options, $db->loadObjectList());
		$lists['location_id'] = JHTML::_('select.genericlist', $options, 'location_id', ' class="inputbox" onchange="submit();" ', 'id', 'name',
			$locationId);
		$activateRecurringEvent = EventBookingHelper::getConfigValue('activate_recurring_event');
		$dateFormat = EventBookingHelper::getConfigValue('date_format');
		
		$options = array();
		$options[] = JHTML::_('select.option', 0, JText::_('EB_HIDE'));
		$options[] = JHTML::_('select.option', 1, JText::_('EB_SHOW'));
		$lists['past_event'] = JHTML::_('select.genericlist', $options, 'past_event', ' class="inputbox" onchange="submit();" ', 'value', 'text',
			$pastEvent);
		
		$options = array();
		$options[] = JHTML::_('select.option', '', JText::_('EB_SELECT_STATUS'));
		$options[] = JHTML::_('select.option', 1, JText::_('EB_PUBLISHED'));
		$options[] = JHTML::_('select.option', 0, JText::_('EB_UNPUBLISHED'));
		$lists['filter_state'] = JHTML::_('select.genericlist', $options, 'filter_state', ' class="inputbox" onchange="submit();" ', 'value', 'text',
			$filter_state);
		
		$this->assignRef('lists', $lists);
		$this->assignRef('items', $items);
		$this->assignRef('pagination', $pagination);
		$this->assignRef('activateRecurringEvent', $activateRecurringEvent);
		$this->assignRef('dateFormat', $dateFormat);
		$this->assignRef('language', $language);
		
		parent::display($tpl);
	}
}