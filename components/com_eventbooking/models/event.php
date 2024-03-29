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
defined( '_JEXEC' ) or die ;

/**
 * Event Booking Component Event Model
 *
 * @package		Joomla
 * @subpackage	Event Booking
 * @since 1.5
 */
class EventBookingModelEvent extends JModelLegacy
{		
	/**
	 * Event id
	 *
	 * @var int
	 */
	var $_id = null;		
	/**
	 * Event data
	 *
	 * @var array
	 */
	var $_data = null;	
	/**
	 * Constructor function
	 *
	 */
	function __construct() {
		parent::__construct();
		$id = JRequest::getInt('id', 0);		
		if($id)
			$this->setId($id);			
	}
	/**
	 * Get event detail
	 *
	 * @return object
	 */
	function getData() {
		if (empty($this->_data)) {
		    $config = EventBookingHelper::getConfig();
		    $nullDate = $this->_db->getNullDate() ;		    
			$user = JFactory::getUser();
			$userId = $user->get('id');
			$id = JRequest::getInt('event_id', 0) ;
			$sql = 'SELECT * FROM #__eb_events WHERE id='.$id;
			$this->_db->setQuery($sql) ;
			$this->_data = $this->_db->loadObject();
			//Get total registered user
			$sql = 'SELECT SUM(number_registrants) FROM #__eb_registrants WHERE event_id='.$this->_data->id.' AND group_id=0 AND (published = 1 OR (payment_method LIKE "os_offline%" AND published != 2))';
			$this->_db->setQuery($sql) ;
			$this->_data->total_registrants = (int) $this->_db->loadResult();
			$sql = 'SELECT category_id FROM #__eb_event_categories WHERE event_id='.$id;
			$this->_db->setQuery($sql) ;
			$this->_data->category_id = $this->_db->loadResult() ;					
			if ($userId > 0) {
				$sql = 'SELECT COUNT(id) FROM #__eb_registrants WHERE user_id='.$userId.' AND event_id='.$id.' AND (published=1 OR (payment_method LIKE "os_offline%" AND published != 2))';
				$this->_db->setQuery($sql) ;
				$this->_data->user_registered = (int)$this->_db->loadResult();
				if ($config->show_discounted_price) {
			        $discount = 0 ;
			        $row = $this->_data ;
				    if (($row->early_bird_discount_date != $nullDate) && ($row->date_diff >=0)) {
        		        if ($row->early_bird_discount_type == 1) {            		                        		           
        					$discount += $row->individual_price*$row->early_bird_discount_amount/100 ;						
        				} else {            				    
        					$discount += $row->early_bird_discount_amount ;
        				}            				
				    }
				    if ($row->discount > 0) {
        				if ($row->discount_type == 1) {
        					$discount += $row->individual_price*$row->discount/100 ;						
        				} else {
        					$discount += $row->discount ;
        				}
        			}        			
        			$this->_data->discounted_price = $this->_data->individual_price - $discount ;
				}
			} else {
				$this->_data->user_registered = 0 ;
				if ($config->show_discounted_price) {
				    $discount = 0 ;
			        $row = $this->_data ;
				    if (($row->early_bird_discount_date != $nullDate) && ($row->date_diff >=0)) {
        		        if ($row->early_bird_discount_type == 1) {            		                        		           
        					$discount += $row->individual_price*$row->early_bird_discount_amount/100 ;						
        				} else {            				    
        					$discount += $row->early_bird_discount_amount ;
        				}            				
				    }	
				    $this->_data->discounted_price = $this->_data->individual_price - $discount ;			            			        			    				  
				}
			}
		}						
		return $this->_data ;
	}	
	/**
	 * Get categories path to add to gateway
	 *
	 * @return array
	 */
	function getCategories(){
		$item = $this->getData();
		$sql = 'SELECT category_id FROM #__eb_event_categories WHERE event_id='.$item->id ;
		$this->_db->setQuery($sql) ;
		$categoryId = $this->_db->loadResult() ;
		if (!$categoryId)
			return array();
		$sql = "SELECT id, name, parent FROM #__eb_categories WHERE id = ".$categoryId." AND published=1";
		$this->_db->setQuery( $sql );
		$row = $this->_db->loadObject();
		$row->total_children = 0 ;//Fake total children
		$parents = array();
		$parents[] = $row ;		
		$categoryId = $row->parent ;
		while ( true ){
			$sql = "SELECT id, name, parent FROM #__eb_categories WHERE id = ".$categoryId." AND published=1";
			$this->_db->setQuery( $sql );
			$row = $this->_db->loadObject();
			if ($row){
				$sql = 'SELECT COUNT(*) FROM #__eb_categories WHERE parent='.$row->id.' AND published = 1';
				$this->_db->setQuery($sql) ;
				$total = $this->_db->loadResult();
				$row->total_children = $total ;				
				$parents[] = $row ;
				$categoryId = $row->parent ;				
			} else {							
			 	break;
			}			
		}
		return $parents ;	
	}
	
	###############################################Added for supporting add/edit events from front-end##########################################################
	
	/**
	 * Method to set the event identifier
	 *
	 * @access	public
	 * @param	int event identifier
	 */
	function setId($id)
	{
		// Set event id and wipe data
		$this->_id		= $id;
		$this->_data	= null;
	}
	/**
	 * Method to get a package
	 *
	 * @since 1.5
	 */
	function &getEvent()
	{		
		if (empty($this->_data)) {
			if ($this->_id)
				$this->_loadData();
			else 
				$this->_initData();	
		}							
		return $this->_data;
	}
	/**
	 * Method to store an event
	 *
	 * @access	public
	 * @return	boolean	True on success
	 * @since	1.5
	 */
	function store(&$data)
	{	
		//Init default data
		if (!isset($data['weekdays']))
			$data['weekdays'] = array() ;
		if (!isset($data['monthdays']))
			$data['monthdays'] = '' ;			
		if (!$data['number_days'])
			$data['number_days'] = 1 ;
		if (!$data['number_weeks'])
			$data['number_week'] = 1 ;		
		if (!$data['recurring_occurrencies'])
			$data['recurring_occurrencies'] = 0 ;
		if (!$data['recurring_end_date']) {
			$data['recurring_end_date'] = $this->_db->getNullDate() ;
		}
		if (isset($data['recurring_type']) && $data['recurring_type']) {
			return $this->_storeRecurringEvent($data) ;			
		} else {			
			//Normal events
			jimport('joomla.filesystem.file') ;	
			$row = & $this->getTable('EventBooking', 'Event');		
			if ($data['id']) {
				$row->load($data['id']);	
			} else {
				$user = JFactory::getUser() ;
				$row->created_by = $user->get('id') ; 
			}							
			if (!$row->bind($data, array('category_id'))) {
				$this->setError($this->_db->getErrorMsg());
				return false;
			}
			$eventDateHour = $data['event_date_hour'] ;			
			$row->event_date .=' '.$eventDateHour.':'.$data['event_date_minute'].':00';
			
			$eventDateHour = $data['event_end_date_hour'] ;			
			$row->event_end_date .=' '.$eventDateHour.':'.$data['event_end_date_minute'].':00';				
			//Setup the parameters, too
			$keys = array('s_lastname', 'r_lastname', 's_organization', 'r_organization', 's_address', 'r_address', 's_address2', 'r_address2', 's_city', 'r_city', 's_state', 'r_state', 's_zip', 'r_zip', 's_country', 'r_country', 's_phone', 'r_phone', 's_fax', 'r_fax', 's_comment', 'r_comment');
			$keys = array_merge($keys, array('gs_lastname', 'gr_lastname', 'gs_organization', 'gr_organization', 'gs_address', 'gr_address', 'gs_address2', 'gr_address2', 'gs_city', 'gr_city', 'gs_state', 'gr_state', 'gs_zip', 'gr_zip', 'gs_country', 'gr_country', 'gs_phone', 'gr_phone', 'gs_fax', 'gr_fax', 'gs_email', 'gr_email', 'gs_comment', 'gr_comment')) ;
			$params = array() ;
			foreach ($keys as $key) {
				$params[$key] = JRequest::getInt($key, 0) ;
			}
			$txt = array() ;
			foreach ( $params as $k=>$v) {
				$txt[] = "$k=\"$v\"";
			}
			$row->params = implode("\n", $txt);
			$eventCustomField = EventBookingHelper::getConfigValue('event_custom_field');
			if ($eventCustomField) {							
				$params		= JRequest::getVar( 'params', null, 'post', 'array' );				
				if (is_array($params))
				{
					$txt = array ();
					foreach ($params as $k => $v) {						
						$txt[] = "$k=\"$v\"";
					}
					$row->custom_fields = implode("\n", $txt);
				}							
			}			
			//Check ordering of the fieds		
			if (!$row->id) {
				$where = ' category_id = ' . (int) $row->category_id ;
				$row->ordering = $row->getNextOrder( $where );
			}								
			if (!$row->store()) {
				$this->setError($this->_db->getErrorMsg());			
				return false;
			}									
			$sql = 'DELETE FROM #__eb_event_group_prices WHERE event_id = '.$row->id;
			$this->_db->setQuery($sql);
			$this->_db->query();		
			$prices = $data['price'];
			$registrantNumbers = $data['registrant_number'];
			for ($i = 0, $n = count($prices) ; $i < $n; $i++) {
				$price = $prices[$i] ;
				$registrantNumber = $registrantNumbers[$i];
				if (($registrantNumber > 0) && ($price >0)) {
					$sql = "INSERT INTO #__eb_event_group_prices(event_id, registrant_number, price) VALUES($row->id, $registrantNumber, $price)";
					$this->_db->setQuery($sql);
					$this->_db->query();				
				}
			}	
			$sql = 'DELETE FROM #__eb_event_categories WHERE event_id = '.$row->id;
			$this->_db->setQuery($sql);
			$this->_db->query();		
			$categories = $data['category_id'];			
			for ($i = 0, $n = count($categories) ; $i < $n; $i++) {
				$categoryId = (int)$categories[$i] ;				
				if ($categoryId) {
					$sql = "INSERT INTO #__eb_event_categories(event_id, category_id) VALUES($row->id, $categoryId)";
					$this->_db->setQuery($sql);
					$this->_db->query();				
				}
			}							
			return true;
		}		
	}
	/**
	 * Store the event in case recurring feature activated
	 * @param array $data
	 */
	function _storeRecurringEvent($data) {
		jimport('joomla.filesystem.file') ;	
		$row = & $this->getTable('EventBooking', 'Event');			
		if ($data['id']) {
			$row->load($data['id']);	
		} else {
			$user = JFactory::getUser() ;
			$row->created_by = $user->get('id') ;
		}				
		if (!$row->bind($data, array('category_id', 'params'))) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		$row->event_type = 1 ;				
		$eventDateHour = $data['event_date_hour'] ;			
		$row->event_date .=' '.$eventDateHour.':'.$data['event_date_minute'].':00';		
		$eventDateHour = $data['event_end_date_hour'] ;
		
		$row->weekdays =  implode(',', $data['weekdays']) ;	
		$row->event_end_date .=' '.$eventDateHour.':'.$data['event_end_date_minute'].':00';				
		//Adjust event start date and event end date				
		if ($data['recurring_type'] == 1) {
			$eventDates = EventBookingHelper::getDailyRecurringEventDates($row->event_date, $data['recurring_end_date'], (int)$data['number_days'], (int)$data['recurring_occurrencies']);
			$row->recurring_frequency = $data['number_days'] ;
		} elseif ($data['recurring_type'] == 2) {
			$eventDates = EventBookingHelper::getWeeklyRecurringEventDates($row->event_date, $data['recurring_end_date'], (int) $data['number_weeks'], (int)$data['recurring_occurrencies'], $data['weekdays']);
			$row->recurring_frequency = $data['number_weeks'] ;			
		}else {
			//Monthly recurring
			$eventDates = EventBookingHelper::getMonthlyRecurringEventDates($row->event_date, $data['recurring_end_date'], (int) $data['number_months'], (int)$data['recurring_occurrencies'], $data['monthdays']);
			$row->recurring_frequency = $data['number_months'] ;
		}
		$eventDuration = abs(strtotime($row->event_end_date) - strtotime($row->event_date));
		if (count($eventDates) == 0) {
			$mainframe = JFactory::getApplication() ;
			$mainframe->redirect('index.php?option=com_eventbooking&task=show_events', JText::_('Invalid recurring setting'));
		} else {
			$row->event_date = $eventDates[0];
			//$row->event_end_date = JHTML::_('date', $eventDates[count($eventDates) - 1], '%Y-%m-%d', 0).' '.$eventDateHour.':'.$data['event_end_date_minute'].':00'; ;
			$row->event_end_date =  strftime('%Y-%m-%d %H:%M:%S', strtotime($row->event_date) + $eventDuration) ;
		} 									
		//Setup the parameters, too
		$keys = array('s_lastname', 'r_lastname', 's_organization', 'r_organization', 's_address', 'r_address', 's_address2', 'r_address2', 's_city', 'r_city', 's_state', 'r_state', 's_zip', 'r_zip', 's_country', 'r_country', 's_phone', 'r_phone', 's_fax', 'r_fax', 's_comment', 'r_comment');
		$keys = array_merge($keys, array('gs_lastname', 'gr_lastname', 'gs_organization', 'gr_organization', 'gs_address', 'gr_address', 'gs_address2', 'gr_address2', 'gs_city', 'gr_city', 'gs_state', 'gr_state', 'gs_zip', 'gr_zip', 'gs_country', 'gr_country', 'gs_phone', 'gr_phone', 'gs_fax', 'gr_fax', 'gs_email', 'gr_email', 'gs_comment', 'gr_comment')) ;
		$params = array() ;
		foreach ($keys as $key) {
			$params[$key] = JRequest::getInt($key, 0) ;
		}
		$txt = array() ;
		foreach ( $params as $k=>$v) {
			$txt[] = "$k=\"$v\"";
		}
		$row->params = implode("\n", $txt);				
		$eventCustomField = EventBookingHelper::getConfigValue('event_custom_field');
		if ($eventCustomField) {							
			$params		= JRequest::getVar( 'params', null, 'post', 'array' );				
			if (is_array($params))
			{
				$txt = array ();
				foreach ($params as $k => $v) {
					$txt[] = "$k=\"$v\"";
				}
				$row->custom_fields = implode("\n", $txt);
			}							
		}	
		//Check ordering of the fieds		
		if (!$row->id) {
			$where = ' category_id = ' . (int) $row->category_id ;
			$row->ordering = $row->getNextOrder( $where );
			$user = JFactory::getUser() ;
			$row->created_by = $user->get('id') ;
		}									
		if (!$row->store()) {
			$this->setError($this->_db->getErrorMsg());
			die($this->_db->getErrorMsg()) ;			
			return false;
		}									
		$sql = 'DELETE FROM #__eb_event_group_prices WHERE event_id = '.$row->id;
		$this->_db->setQuery($sql);
		$this->_db->query();		
		$prices = $data['price'];
		$registrantNumbers = $data['registrant_number'];
		for ($i = 0, $n = count($prices) ; $i < $n; $i++) {
			$price = $prices[$i] ;
			$registrantNumber = $registrantNumbers[$i];
			if (($registrantNumber > 0) && ($price >0)) {
				$sql = "INSERT INTO #__eb_event_group_prices(event_id, registrant_number, price) VALUES($row->id, $registrantNumber, $price)";
				$this->_db->setQuery($sql);
				$this->_db->query();				
			}
		}
		$sql = 'DELETE FROM #__eb_event_categories WHERE event_id = '.$row->id;
		$this->_db->setQuery($sql);
		$this->_db->query();		
		$categories = $data['category_id'];			
		for ($i = 0, $n = count($categories) ; $i < $n; $i++) {
			$categoryId = (int)$categories[$i] ;				
			if ($categoryId) {
				$sql = "INSERT INTO #__eb_event_categories(event_id, category_id) VALUES($row->id, $categoryId)";
				$this->_db->setQuery($sql);
				$this->_db->query();				
			}
		}				
		/**
		 * In case creating new event, we will create children events
		 */
		if (!$this->_id) {
			for ($i = 1 , $n = count($eventDates) ; $i < $n ; $i++) {
				$rowChildEvent = clone ($row) ;
				$rowChildEvent->id = 0 ;
				$rowChildEvent->event_date  = $eventDates[$i] ;
				$rowChildEvent->event_end_date = strftime('%Y-%m-%d %H:%M:%S', strtotime($eventDates[$i]) + $eventDuration) ;								
				$rowChildEvent->event_type = 2 ;
				$rowChildEvent->parent_id = $row->id ;
				$rowChildEvent->created_by = $row->created_by ;
				$rowChildEvent->recurring_type = 0 ;
				$rowChildEvent->recurring_frequency = 0 ;
				$rowChildEvent->weekdays = '' ;
				$rowChildEvent->monthdays = '' ;				
				$rowChildEvent->recurring_end_date = $this->_db->getNullDate();
				$rowChildEvent->recurring_occurrencies = 0 ; 				
				$rowChildEvent->store();				
				//Event Price
				for ($j = 0, $m = count($prices) ; $j < $m; $j++) {
						$price = $prices[$j] ;
						$registrantNumber = $registrantNumbers[$j];
						if (($registrantNumber > 0) && ($price >0)) {
							$sql = "INSERT INTO #__eb_event_group_prices(event_id, registrant_number, price) VALUES($rowChildEvent->id, $registrantNumber, $price)";
							$this->_db->setQuery($sql);
							$this->_db->query();				
						}
				}
				for ($j = 0, $m = count($categories) ; $j < $m; $j++) {
					$categoryId = (int)$categories[$j] ;				
					if ($categoryId) {
						$sql = "INSERT INTO #__eb_event_categories(event_id, category_id) VALUES($rowChildEvent->id, $categoryId)";
						$this->_db->setQuery($sql);
						$this->_db->query();
						echo $this->_db->getQuery() ;				
					}
				}			
			}															
		}elseif (isset($data['update_children_event'])) {
			$sql = 'SELECT id FROM #__eb_events WHERE parent_id='.$row->id;
			$this->_db->setQuery($sql) ;
			if (version_compare(JVERSION, '3.0', 'ge'))
				$children = $this->_db->loadColumn() ;
			else
				$children = $this->_db->loadResultArray() ;
			if (count($children)) {
				$fieldsToUpdate = array('category_id', 'location_id', 'title', 
										'short_description', 'description', 'access', 
										'registration_access', 'individual_price', 'event_capacity',
										'cut_off_date', 'registration_type', 'max_group_number',
										'discount_type', 'discount', 'paypal_email', 
										'paypal_email', 'notification_emails', 'user_email_body',
										'user_email_body_offline', 'thanks_message',
										'thanks_message_offline', 'params', 'published'
				) ;
				$rowChildEvent = JTable::getInstance('EventBooking', 'Event');
				foreach ($children as $childId) {
					$rowChildEvent->load($childId);
					foreach ($fieldsToUpdate as $field) 
						$rowChildEvent->$field = $row->$field ;
					$rowChildEvent->store() ;
					$sql = 'DELETE FROM #__eb_event_group_prices WHERE event_id='.$rowChildEvent->id ;
					$this->_db->setQuery($sql) ;
					$this->_db->query() ;
					for ($i = 0, $n = count($prices) ; $i < $n; $i++) {
						$price = $prices[$i] ;
						$registrantNumber = $registrantNumbers[$i];
						if (($registrantNumber > 0) && ($price >0)) {
							$sql = "INSERT INTO #__eb_event_group_prices(event_id, registrant_number, price) VALUES($rowChildEvent->id, $registrantNumber, $price)";
							$this->_db->setQuery($sql);
							$this->_db->query();				
						}
					}			
					$sql = 'DELETE FROM #__eb_event_categories WHERE event_id = '.$rowChildEvent->id;
					$this->_db->setQuery($sql);
					$this->_db->query();					
					for ($i = 0, $n = count($categories) ; $i < $n; $i++) {
						$categoryId = (int)$categories[$i] ;				
						if ($categoryId) {
							$sql = "INSERT INTO #__eb_event_categories(event_id, category_id) VALUES($rowChildEvent->id, $categoryId)";
							$this->_db->setQuery($sql);
							$this->_db->query();				
						}
					}															
				}				
			}
		}		
		return true ;															
	}	
	/**
	 * Init event data
	 *
	 */
	function _initData() {
		$db = JFactory::getDBO() ;
		$config = EventBookingHelper::getConfig() ;		
		$row = new stdClass() ;
		$row->id = 0 ;
		$row->category_id = 0 ;
		$row->location_id = 0 ;
		$row->title = null ;
		$row->event_date = $db->getNullDate() ;
		$row->event_end_date = $db->getNullDate();
		$row->short_description = null ;
		$row->description = null ;
		$row->individual_price = null ;
		$row->event_capacity = null ;
		$row->cut_off_date = $db->getNullDate() ;		
		$row->registration_type = 0 ;								
		$row->access = 1 ;				
		$row->registration_access = 1 ;
		$row->max_group_number = 0 ;						
		$row->discount_type = 0 ;
		$row->discount = 0 ;
		$row->enable_cancel_registration = 0 ;
		$row->cancel_before_date = $db->getNullDate() ;
		$row->enable_auto_reminder = null ;
		$row->remind_before_x_days = 3 ;
		$row->early_bird_discount_type = null ;
		$row->early_bird_discount_amount = null ;
		$row->early_bird_discount_date = $db->getNullDate() ;
		$row->article_id = $config->article_id ;
		$row->recurring_type = 0 ;
		$row->number_days = '' ;
		$row->number_weeks = '' ; 
		$row->number_months = '' ;
		$row->recurring_frequency = 0 ;
		$row->weekdays = null ;
		$row->monthdays = null ;				
		$row->recurring_end_date = $db->getNullDate() ;
		$row->recurring_occurrencies = null ;
		$row->paypal_email = null ;
		$row->notification_emails = null ;
		$row->user_email_body = null ;
		$row->user_email_body_offline = null ;
		$row->thanks_message = null ;
		$row->thanks_message_offline = null ;		
		$row->params = null ;
		$row->custom_fields = null ;
		$row->ordering = 0 ;
		$row->published = 0 ;
		$this->_data = $row;
	}
	/**
	 * Load event information from database
	 * 
	 */
	function _loadData() {
		$sql = 'SELECT * FROM #__eb_events WHERE id='.$this->_id;
		$this->_db->setQuery($sql);		
		$row = $this->_db->loadObject();
		$activateRecurringEvent = EventBookingHelper::getConfigValue('activate_recurring_event');
		if ($activateRecurringEvent) {
			if ($row->recurring_type == 1) {
				$row->number_days = $row->recurring_frequency ;
				$row->number_weeks = 0 ;
				$row->number_months = 0 ;				
			} elseif ($row->recurring_type == 2) {
				$row->number_weeks = $row->recurring_frequency ;
				$row->number_days = 0 ;
				$row->number_months = 0 ;				
			}elseif ($row->recurring_type == 3) {
				$row->number_months = $row->recurring_frequency ;
				$row->number_days = 0 ;
				$row->number_weeks = 0 ;
			}
		}
		$this->_data = $row ;
	}	
	/**
	 * Publish / unpublish an event 
	 *
	 * @param int $id
	 * @param int $state
	 */
	function publish($id, $state) {		
		$sql = " UPDATE #__eb_events SET published=$state WHERE id=$id";
		$this->_db->setQuery($sql) ;		
		if (!$this->_db->query())
			return false;
		return true ;
	}	
	/**
	 * Save the order of events
	 *
	 * @param array $cid
	 * @param array $order
	 */
	function saveOrder($cid, $order) {
		$row = JTable::getInstance('EventBooking', 'Event');
		$groupings = array();
		// update ordering values
		for( $i=0; $i < count($cid); $i++ )
		{
			$row->load( (int) $cid[$i] );
			// track parents
			//$groupings[] = $row->category_id ;
			if ($row->ordering != $order[$i])
			{
				$row->ordering = $order[$i];
				if (!$row->store()) {
					$this->setError($this->_db->getErrorMsg());
					return false;
				}
			}
		}
		// execute updateOrder for each parent group
		/*
		$groupings = array_unique( $groupings );
		foreach ($groupings as $group){
			$row->reorder('category_id = '.(int) $group);
		}
		*/
		return true;	
	}	
	/**
	 * Change ordering of a category
	 *
	 */
	function move($direction) {
		$row = JTable::getInstance('EventBooking', 'Event');
		$row->load($this->_id);		
		if (!$row->move( $direction, ' published >= 0 ' )) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		return true;
	}
	/**
	 * Get price setting for the event
	 *
	 */
	function getPrices() {
		if ($this->_id) {
			$sql = 'SELECT * FROM #__eb_event_group_prices WHERE event_id='.$this->_id.' ORDER BY id ';
			$this->_db->setQuery($sql);			
			$prices = $this->_db->loadObjectList();			
		} else {
			$prices = array();			
		}
		return $prices ;
	}
	/**
	 * Copy an event to create new event
	 *
	 * @param int $id
	 */
	function copy($id) {						
		$rowOld = JTable::getInstance('EventBooking', 'Event');
		$rowOld->load($id) ;
		$row = JTable::getInstance('EventBooking', 'Event');
		$data = JArrayHelper::fromObject($rowOld) ; 
		$row->bind($data) ;
		$row->id = 0 ;
		$row->title = 'Copy of '.$row->title ;
		$row->store() ;		
		//We will insert group rate for this event
		$sql = 'INSERT INTO #__eb_event_group_prices(event_id, registrant_number, price) '
			.' SELECT '.$row->id.' , registrant_number, price FROM #__eb_event_group_prices '
			.' WHERE event_id='.$id		
		;				
		$this->_db->setQuery($sql) ;
		$this->_db->query();
		
		//Need to enter categories for this event
				
		$sql = 'INSERT INTO #__eb_event_categories(event_id, category_id) '
			.' SELECT '.$row->id.' , category_id FROM #__eb_event_categories '
			.' WHERE event_id='.$id		
		;				
		$this->_db->setQuery($sql) ;
		$this->_db->query();
		
		return $row->id ;
	}		
} 