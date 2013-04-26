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
class EventBookingViewCoupon extends JViewLegacy
{

	function display($tpl = null)
	{
		$db = JFactory::getDBO();
		$nullDate = $db->getNullDate();
		$item = $this->get('Data');
		$config = EventBookingHelper::getConfig();
		$options = array();
		$options[] = JHTML::_('select.option', 0, JText::_('%'));
		$options[] = JHTML::_('select.option', 1, EventBookingHelper::getConfigValue('currency_symbol'));
		$lists['coupon_type'] = JHTML::_('select.genericlist', $options, 'coupon_type', 'class="inputbox"', 'value', 'text', $item->coupon_type);
		$options = array();
		$options[] = JHTML::_('select.option', 0, 'All Events', 'id', 'title');
		
		$sql = 'SELECT id, title, event_date FROM #__eb_events WHERE published=1 ORDER BY title, ordering';
		$db->setQuery($sql);
		if ($config->show_event_date)
		{
			$rows = $db->loadObjectList();
			for ($i = 0, $n = count($rows); $i < $n; $i++)
			{
				$row = $rows[$i];
				$options[] = JHTML::_('select.option', $row->id, 
					$row->title . ' (' . JHTML::_('date', $row->event_date, $config->date_format, null) . ')' . '', 'id', 'title');
			}
		}
		else
		{
			$options = array_merge($options, $db->loadObjectList());
		}
		$lists['event_id'] = JHTML::_('select.genericlist', $options, 'event_id', 'class="inputbox"', 'id', 'title', $item->event_id);
		$lists['published'] = JHTML::_('select.booleanlist', 'published', 'class="inputbox"', $item->published);
		$this->assignRef('item', $item);
		$this->assignRef('lists', $lists);
		$this->assignRef('nullDate', $nullDate);
		parent::display($tpl);
	}
}