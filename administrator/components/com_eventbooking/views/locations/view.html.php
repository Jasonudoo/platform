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
 * HTML View class for Event Booking component
 *
 * @static
 * @package		Joomla
 * @subpackage	Event Booking
 * @since 1.5
 */
class EventBookingViewLocations extends JViewLegacy
{

	function display($tpl = null)
	{
		$mainframe = JFactory::getApplication();
		if (!function_exists('curl_init')) {			
			$mainframe->enqueueMessage(JText::_('EB_CURL_NOT_INSTALLED'), 'warning');
		}
		$option = 'com_eventbooking';
		$filter_order = $mainframe->getUserStateFromRequest($option . 'location_filter_order', 'filter_order', 'a.id', 'cmd');
		$filter_order_Dir = $mainframe->getUserStateFromRequest($option . 'filter_order_Dir', 'filter_order_Dir', '', 'word');
		$language = $mainframe->getUserStateFromRequest($option . 'locations_language', 'filter_language', '', 'string');
		$search = $mainframe->getUserStateFromRequest($option . 'search', 'search', '', 'string');
		$search = JString::strtolower($search);
		$lists['search'] = $search;
		//Get list of campaigns		 
		$items = & $this->get('Data');
		$pagination = & $this->get('Pagination');
		$lists['order_Dir'] = $filter_order_Dir;
		$lists['order'] = $filter_order;
		$this->assignRef('lists', $lists);
		$this->assignRef('items', $items);
		$this->assignRef('pagination', $pagination);
		$this->assignRef('language', $language);
		parent::display($tpl);
	}
}