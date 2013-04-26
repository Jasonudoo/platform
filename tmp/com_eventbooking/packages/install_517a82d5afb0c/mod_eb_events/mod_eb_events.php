<?php
/**
 * @version		1.5.1
 * @package		Joomla
 * @subpackage	Event Booking
 * @author  Tuan Pham Ngoc
 * @copyright	Copyright (C) 2010 Ossolution Team
 * @license		GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die ;
require_once JPATH_ROOT.'/components/com_eventbooking/helper/helper.php';
EventBookingHelper::loadLanguage() ;
$app = JFactory::getApplication() ;	
$db = & JFactory::getDBO();		
$itemId = (int) $params->get('item_id', 0);
if (!$itemId)
    $itemId = EventBookingHelper::getItemid() ;
$user = & JFactory::getUser() ;    	
$numberEvents = $params->get('number_events', 6);
$categoryIds = $params->get('category_ids', '');
$showCategory = $params->get('show_category', 1);
$showLocation = $params->get('show_location') ;		
$where = array() ;
$where[] = 'a.published =1 ' ;
$where[] = 'DATE(event_date) >= CURDATE()' ;
$where[] = '(cut_off_date = "'.$db->getNullDate().'" OR DATE(cut_off_date) >= CURDATE())' ;
if ($categoryIds != '') {
	$where[] = ' a.id IN (SELECT event_id FROM #__eb_event_categories WHERE category_id IN ('.$categoryIds.'))' ;	
}
$where[] = ' a.access IN ('.implode(',', $user->getAuthorisedViewLevels()).')' ;
if ($app->getLanguageFilter()) {
	$where[] = ' a.language IN (' . $db->Quote(JFactory::getLanguage()->getTag()) . ',' . $db->Quote('*') . ')';
} 
$sql = 'SELECT a.id, a.title, a.location_id, a.event_date, c.name AS location_name FROM #__eb_events AS a '		 
	 . ' LEFT JOIN #__eb_locations AS c '
	 . ' ON a.location_id = c.id '
	 . ' WHERE '.implode(' AND ', $where)
	 . ' ORDER BY a.event_date '
	 . ' LIMIT '.$numberEvents		
;	
$db->setQuery($sql) ;	
$rows = $db->loadObjectList();
for ($i = 0 , $n = count($rows) ; $i < $n ; $i++) {
	$row = $rows[$i] ;
	//Get all categories
	$sql = 'SELECT a.id, a.name FROM #__eb_categories AS a INNER JOIN #__eb_event_categories AS b ON a.id = b.category_id WHERE b.event_id='.$row->id;
	$db->setQuery($sql) ;
	$categories = $db->loadObjectList() ;
	if (count($categories)) {
		$itemCategories = array() ;
		foreach ($categories as  $category) {
			$itemCategories[] = '<a href="'.JRoute::_('index.php?option=com_eventbooking&task=view_category&category_id='.$category->id).'&Itemid='.$itemId.'"><strong>'.$category->name.'</strong></a>';
		}
		$row->categories = implode('&nbsp;|&nbsp;', $itemCategories) ;
	}		
}
$config = EventBookingHelper::getConfig() ;
$document = & JFactory::getDocument() ;
$css = 'modules/mod_eb_events/css/style.css' ;
$document->addStyleSheet($css);			
require(JModuleHelper::getLayoutPath('mod_eb_events', 'default'));