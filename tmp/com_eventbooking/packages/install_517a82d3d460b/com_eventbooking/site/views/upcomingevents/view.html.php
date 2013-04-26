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
 * @subpackage	Event Booking
 * @since 1.5
 */
class EventBookingViewUpcomingEvents extends JViewLegacy
{

	function display($tpl = null)
	{
		$mainframe = JFactory::getApplication();
		$document = JFactory::getDocument();
		$db = JFactory::getDBO();
		$nullDate = $db->getNullDate();
		$categoryId = JRequest::getInt('category_id', 0);		
		if ($categoryId)
		{
			EventBookingHelper::checkCategoryAccess($categoryId);
		}
		$items = $this->get('Data');
		$category = $this->get('Category');
		$pageTitle = JText::_('EB_UPCOMING_EVENTS_PAGE_TITLE');
		$pageTitle = str_replace('[CATEGORY_NAME]', $category->name, $pageTitle);
		$document->setTitle($pageTitle);
		$config = EventBookingHelper::getConfig();
		if ($config->process_plugin)
		{
			for ($i = 0, $n = count($items); $i < $n; $i++)
			{
				$item = $items[$i];
				$item->short_description = JHtml::_('content.prepare', $item->short_description);
			}
		}
		if ($config->event_custom_field && $config->show_event_custom_field_in_category_layout)
		{
			$params = new JRegistry();
			$xml = JFactory::getXML(JPATH_COMPONENT . '/fields.xml');
			$fields = $xml->fields->fieldset->children();
			$customFields = array();
			foreach ($fields as $field)
			{
				$name = $field->attributes()->name;
				$label = JText::_($field->attributes()->label);
				$customFields["$name"] = $label;
			}
			for ($i = 0, $n = count($items); $i < $n; $i++)
			{
				$item = & $items[$i];
				$params->loadString($item->custom_fields, 'INI');
				$paramData = array();
				foreach ($customFields as $name => $label)
				{
					$paramData[$name]['title'] = $label;
					$paramData[$name]['value'] = $params->get($name);
				}
				
				$item->paramData = $paramData;
			}
		}
		$Itemid = JRequest::getInt('Itemid', 0);
		$user = JFactory::getUser();
		$userId = $user->get('id');
		$viewLevels = $user->getAuthorisedViewLevels();
		$this->viewLevels = $viewLevels;
		$this->userId = $userId;
		$this->items = $items;
		$this->Itemid = $Itemid;
		$this->config = $config;
		$this->nullDate = $nullDate;
		
		parent::display($tpl);
	}
}