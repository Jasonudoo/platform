<?php
/**
 * @version		1.5.0
 * @package		Joomla
 * @subpackage	EventBooking
 * @author  Tuan Pham Ngoc
 * @copyright	Copyright (C) 2010 Ossolution Team
 * @license		GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die;

jimport('joomla.plugin.plugin');
jimport('joomla.html.parameter') ;

/**
 * Contacts Search plugin
 *
 * @package		Joomla.Plugin
 * @subpackage	Search.contacts
 * @since		1.6
 */
class plgSearchEventBooking extends JPlugin
{
	/**
	 * Constructor
	 *
	 * @access      protected
	 * @param       object  $subject The object to observe
	 * @param       array   $config  An array that holds the plugin configuration
	 * @since       1.5
	 */
	public function __construct(& $subject, $config)
	{
		parent::__construct($subject, $config);
				
		$this->loadLanguage();
	}

	/**
	* @return array An array of search areas
	*/
	function onContentSearchAreas()
	{
		static $areas = array(
			'eb_search' => 'Events'
		);
		return $areas;
	}

	function onContentSearch($text, $phrase='', $ordering='', $areas=null)
	{
		require_once JPATH_ROOT.'/components/com_eventbooking/helper/helper.php';
		$db		=& JFactory::getDBO();	
		$Itemid = EventBookingHelper::getItemid();
		if (is_array( $areas )) {
			if (!array_intersect( $areas, array_keys($this->onContentSearchAreas()) )) {
				return array();
			}
		}
		// load plugin params info	 	
		$limit = $this->params->def( 'search_limit', 50 );
		$text = trim( $text );
		if ($text == '') {
			return array();
		}
		$section 	= JText::_( 'Events' );
		$wheres 	= array();
		switch ($phrase)
		{
			case 'exact':
				$text		= $db->Quote( '%'.$db->escape( $text, true ).'%', false );
				$wheres2 	= array();
				$wheres2[] 	= 'a.title LIKE '.$text;
				$wheres2[] 	= 'a.short_description LIKE '.$text;
				$wheres2[] 	= 'a.description LIKE '.$text;						
				$where 		= '(' . implode( ') OR (', $wheres2 ) . ')';
				break;
	
			case 'all':
			case 'any':
			default:
				$words 	= explode( ' ', $text );
				$wheres = array();
				foreach ($words as $word)
				{
					$word		= $db->Quote( '%'.$db->escape( $word, true ).'%', false );
					$wheres2 	= array();
					$wheres2[] 	= 'a.title LIKE '.$word;
					$wheres2[] 	= 'a.short_description LIKE '.$word;
					$wheres2[] 	= 'a.description LIKE '.$word;				
					$wheres[] 	= implode( ' OR ', $wheres2 );
				}
				$where 	= '(' . implode( ($phrase == 'all' ? ') AND (' : ') OR ('), $wheres ) . ')';
				break;
		}
	
		switch ( $ordering )
		{
			case 'oldest':
				$order = 'a.event_date ASC';
				break;		
			case 'alpha':
				$order = 'a.title ASC';
				break;
			case 'newest':
				$order = 'a.event_date ASC';
			default:
				$order = 'a.ordering ';
		}	
		$user = & JFactory::getUser() ;		
		$query = 'SELECT a.id, a.category_id AS cat_id, a.title AS title, a.description AS text, event_date AS `created`, '	
		.$db->Quote($section) .' AS section,'
		. ' "1" AS browsernav'
		. ' FROM #__eb_events AS a'	
		. ' WHERE ('. $where .') AND (a.access = 0 OR a.access IN ('.implode(',', $user->getAuthorisedViewLevels()).'))'
		. ' AND a.published = 1'	
		. ' ORDER BY '. $order
		;
		$db->setQuery( $query, 0, $limit );			
		$rows = $db->loadObjectList();
		if (count($rows)) {
			foreach($rows as $key => $row) {		
				$rows[$key]->href = JRoute::_('index.php?option=com_eventbooking&task=view_event&event_id='.$row->id.'&Itemid='.$Itemid);		
			}	
		}	
		return $rows ;
	}	
}
