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
 * EventBooking controller
 *
 * @package		Joomla
 * @subpackage	Event Booking
 * @since 1.5
 */
class EventBookingController extends JControllerLegacy
{
	/**
	 * Constructor function
	 *
	 * @param array $config
	 */
	function __construct($config = array())
	{
		parent::__construct($config);		
		$this->registerTask('apply_category', 'save_category') ;	
		$this->registerTask('apply_event', 'save_event') ;
		$this->registerTask('apply_registrant', 'save_registrant') ;		
	}
	/**
	 * Display information
	 *
	 */
	function display( )
	{	
	    $document = JFactory::getDocument() ;
		$document->addStyleSheet(JURI::base(true).'/components/com_eventbooking/assets/css/style.css') ;		
		$task = $this->getTask();		
		switch ($task) {																			
			case 'edit_category' :
				JRequest::setVar('view', 'category');
				JRequest::setVar('edit', true) ;
				break ;				
			case 'new_category' :
				JRequest::setVar('view', 'category');
				JRequest::setVar('edit', false) ;
				break ;					
			case 'edit_location' :
				JRequest::setVar('view', 'location');
				JRequest::setVar('edit', true) ;
				break ;				
			case 'add_location' :
				JRequest::setVar('view', 'location');
				JRequest::setVar('edit', false) ;
				break ;				
			case 'add_field':					
				JRequest::setVar( 'view'  , 'field');
				JRequest::setVar( 'edit', false );
				break;	
			case 'edit_field':							
				JRequest::setVar( 'view'  , 'field');
				JRequest::setVar( 'edit', true );
				break;																	
			case 'add_event'     :										
				JRequest::setVar( 'view'  , 'event');
				JRequest::setVar( 'edit', false );				
			 	break;			
			case 'edit_event'    :							
				JRequest::setVar('view', 'event');								
				JRequest::setVar( 'edit', true );				
				break;																										
			case 'add_registrant':					
				JRequest::setVar( 'view'  , 'registrant');
				JRequest::setVar( 'edit', false );
				break;	
			case 'edit_registrant':					
				JRequest::setVar( 'view'  , 'registrant');
				JRequest::setVar( 'edit', true );
				break;			
			case 'email_registrants' :
				JRequest::setVar('view', 'email') ;
				break ;	
			case 'edit_members' :
				JRequest::setVar('view', 'members') ;
				break ;
			case 'show_translation' :
				JRequest::setVar('view', 'language') ;
				break ;						
			case 'edit_plugin' :
				JRequest::setVar('view', 'plugin') ;				
				JRequest::setVar('edit', true) ;
				break ;				
			case 'add_coupon':					
				JRequest::setVar( 'view'  , 'coupon');
				JRequest::setVar( 'edit', false );
				break;	
			case 'edit_coupon':				
				JRequest::setVar( 'view'  , 'coupon');
				JRequest::setVar( 'edit', true );
				break;
			case 'show_massmail_form' :
			    JRequest::setVar( 'view'  , 'massmail');
				JRequest::setVar( 'layout', 'default');				
			    break ;			     			
			case 'add_waiting':					
				JRequest::setVar( 'view'  , 'waiting');
				JRequest::setVar( 'edit', false );
				break;	
			case 'edit_waiting':							
				JRequest::setVar( 'view'  , 'waiting');
				JRequest::setVar( 'edit', true );
				break;				    
			default:
				$view = JRequest::getVar('view', '');
				if (!$view) {
					JRequest::setVar('view', 'events') ;	
				}				
				break ;																															
		}
		parent::display();				
		if (version_compare(JVERSION, '3.0', 'le')) {
			EventBookingHelper::loadBootstrap();
		}
		EventBookingHelper::addSubmenus(JRequest::getVar('view', 'events')); 
		EventBookingHelper::displayCopyRight();		
	}
	/**
	 * Save configuration data
	 *
	 */	
	function save_configuration() {
		$data = JRequest::get('post', JREQUEST_ALLOWRAW) ;
		unset($data['option']) ;
		unset($data['task']) ;							
		if (isset($data['payment_methods']) && is_array($data['payment_methods']))
			$data['payment_methods'] = implode(',', $data['payment_methods']);																									
		$model = & $this->getModel('configuration');
		$ret = $model->store($data);
		if ($ret) {
			$msg = JText::_('EB_CONFIGURATION_DATA_SAVED');
		} else {
			$msg =  JText::_('EB_CONFIGURATION_DATA_ERROR');
		}
				
		$this->setRedirect('index.php?option=com_eventbooking&view=configuration&language='.$data['language'], $msg);
	}	
	/**
	 * Save the category
	 *
	 */
	function save_category() {
		$post = JRequest::get('post' , JREQUEST_ALLOWRAW);
		$model =  $this->getModel('category') ;
		$cid = $post['cid'];
		$post['id'] = (int) $cid[0];
		$ret =  $model->store($post);
		if ($ret) {
			$msg = JText::_('EB_CATEGORY_SAVED');	
		} else {
			$msg = JText::_('EB_CATEGORY_SAVING_ERROR') ;
		}
		$task = $this->getTask() ;		
		if ($task == 'save_category') {
			$url = 'index.php?option=com_eventbooking&view=categories' ;
		} else {
			$url = 'index.php?option=com_eventbooking&task=edit_category&cid[]='.$post['id'] ;
		}
		$this->setRedirect($url, $msg);
	}
	/**
	 * Save ordering of the selected category
	 *
	 */
	function save_category_order() {
		$order = JRequest::getVar('order', array(), 'post') ;
		$cid = JRequest::getVar('cid', array(), 'post') ;
		JArrayHelper::toInteger($order);
		JArrayHelper::toInteger($cid);
		$model = & $this->getModel('category');
		$ret = $model->saveOrder($cid, $order);
		if ($ret) {
			$msg = JText::_('EB_ORDERING_SAVED') ; 
		} else {
			$msg = JText::_('EB_ORDERING_SAVING_ERROR') ;
		}
		$this->setRedirect('index.php?option=com_eventbooking&view=categories', $msg);
	}
	/**
	 * Order up a category
	 *
	 */
	function orderup_category() {
		$model =  $this->getModel('category');
		$model->move(-1);		 			
		$msg = JText::_('EB_ORDERING_UPDATED');			
		$url = 'index.php?option=com_eventbooking&view=categories';
		$this->setRedirect($url, $msg);
	}
	/**
	 * Order down a category
	 *
	 */
	function orderdown_category() {
		$model =  $this->getModel('category');
		$model->move(1);		 			
		$msg = JText::_('EB_ORDERING_UPDATED');			
		$url = 'index.php?option=com_eventbooking&view=categories';
		$this->setRedirect($url, $msg);
	}	
	/**
	 * Removing categories
	 *
	 */
	function remove_categories() {
		$model =  $this->getModel('category');
		$cid = JRequest::getVar('cid', array()) ;
		JArrayHelper::toInteger($cid);
		$model->delete($cid);
		$msg = JText::_('EB_CATEGORIES_REMOVED');			
		$url = 'index.php?option=com_eventbooking&view=categories';
		$this->setRedirect($url, $msg);
	}	
	/**
	 * Unpublish categories
	 *
	 */		
	function categories_publish() {
		$cid = JRequest::getVar('cid', array(), 'post');
		JArrayHelper::toInteger($cid);
		$model = & $this->getModel('category');
		$ret = $model->publish($cid, 1);
		if ($ret) 
			$msg = JText::_('EB_CATEGORIES_PUBLISHED');
		else
			$msg = JText::_('EB_CATEGORY_PUBLISH_ERROR');		
		$this->setRedirect('index.php?option=com_eventbooking&view=categories', $msg);		
	}
	/**
	 * Publish categories
	 *
	 */
	function categories_unpublish() {
		$cid = JRequest::getVar('cid', array(), 'post');
		JArrayHelper::toInteger($cid);
		$model = & $this->getModel('category');
		$ret = $model->publish($cid, 0);
		if ($ret) 
			$msg = JText::_('EB_CATEGORY_UNPUBLISHED');
		else
			$msg = JText::_('EB_CATEGORY_UNPUBLISH_ERROR');		
		$this->setRedirect('index.php?option=com_eventbooking&view=categories', $msg);
	}	
	/**
	 * Copy a category
	 * 
	 */
	function copy_category() {
		$cid = JRequest::getVar('cid', array(), 'post') ;
		JArrayHelper::toInteger($cid);
		$id = $cid[0] ;
		$model = $this->getModel('category') ;
		$model->copy($id);
		$msg = JText::_('EB_CATEGORY_COPIED') ;
		$this->setRedirect('index.php?option=com_eventbooking&view=categories', $msg);
	}
	/**
	 * Cancel the category . Redirect user to categories list page
	 *
	 */
	function cancel_category() {
		$this->setRedirect('index.php?option=com_eventbooking&view=categories');
	}	
	/**
	 * Save event
	 *
	 */	
	function save_event() {
		$post = JRequest::get('post' , JREQUEST_ALLOWRAW);
		$model =  $this->getModel('event') ;
		$cid = $post['cid'];
		$post['id'] = (int) $cid[0];
		$ret =  $model->store($post);
		if ($ret) {
			$msg = JText::_('EB_EVENT_SAVED');	
		} else {
			$msg = JText::_('EB_EVENT_SAVE_ERROR') ;
		}
	    $task = $this->getTask() ;		
		if ($task == 'save_event') {
			$url = 'index.php?option=com_eventbooking&view=events' ;
		} else {
			$url = 'index.php?option=com_eventbooking&task=edit_event&cid[]='.$post['id'] ;
		}
		$this->setRedirect($url, $msg);
	}
	/**
	 * Copy a field
	 *
	 */
	function copy_event() {
		$cid = JRequest::getVar('cid', null) ;
		JArrayHelper::toInteger($cid);
		$id = $cid[0] ;
		$model = $this->getModel('event') ;
		$newEventId =  $model->copy($id);		
		$this->setRedirect('index.php?option=com_eventbooking&task=edit_event&cid[]='.$newEventId, JText::_('EB_EVENT_COPIED'));	
	}
	/**
	 * Remove selected events
	 *
	 */	
	function remove_events() {
		$cid = JRequest::getVar('cid', null);
		JArrayHelper::toInteger($cid);
		$model = $this->getModel('event');
		$ret = $model->delete($cid);
		if ($ret) {
			$msg = JText::_('EB_EVENT_REMOVED'); 
		} else {
			$msg = JText::_('EB_EVENT_REMOVE_ERROR');
		}
		$this->setRedirect('index.php?option=com_eventbooking&view=events', $msg);
	}
	/**
	 * Publish the selected events
	 *
	 */
	function events_publish() {
		$cid = JRequest::getVar('cid', null) ;
		JArrayHelper::toInteger($cid);
		$model = $this->getModel('event') ;
		$ret =  $model->publish($cid, 1);
		if ($ret) {
			$msg = JText::_('EB_EVENT_PUBLISHED'); 
		} else {
			$msg = JText::_('EB_EVENT_PUBLISH_ERROR');
		}
		$this->setRedirect('index.php?option=com_eventbooking&view=events', $msg);
	}
	/**
	 * Unpublish the selected events
	 *
	 */
	function events_unpublish() {
		$cid = JRequest::getVar('cid', null) ;
		JArrayHelper::toInteger($cid);
		$model = $this->getModel('event') ;
		$ret =  $model->publish($cid, 0);
		if ($ret) {
			$msg = JText::_('EB_EVENT_UNPUBLISHED'); 
		} else {
			$msg = JText::_('EB_EVENT_UNPUBLISH_ERROR');
		}
		$this->setRedirect('index.php?option=com_eventbooking&view=events', $msg);
	}
	/**
	 * Redirect user to events mangement page
	 *
	 */
	function cancel_event() {
		$this->setRedirect('index.php?option=com_eventbooking&view=events');
	}
	/**
	 * Save ordering of the selected category
	 *
	 */
	function save_event_order() {
		$order = JRequest::getVar('order', array(), 'post') ;
		$cid = JRequest::getVar('cid', array(), 'post') ;
		JArrayHelper::toInteger($order);
		JArrayHelper::toInteger($cid);
		$model = & $this->getModel('event');
		$ret = $model->saveOrder($cid, $order);
		if ($ret) {
			$msg = JText::_('EB_ORDERING_SAVED') ; 
		} else {
			$msg = JText::_('EB_ORDERING_SAVING_ERROR') ;
		}
		$this->setRedirect('index.php?option=com_eventbooking&view=events', $msg);
	}
	/**
	 * Order up a category
	 *
	 */
	function orderup_event() {
		$model =  $this->getModel('event');
		$model->move(-1);		 			
		$msg = JText::_('EB_ORDERING_UPDATED');			
		$url = 'index.php?option=com_eventbooking&view=events';
		$this->setRedirect($url, $msg);
	}
	/**
	 * Order down a category
	 *
	 */
	function orderdown_event() {
		$model =  $this->getModel('event');
		$model->move(1);		 			
		$msg = JText::_('EB_ORDERING_UPDATED');			
		$url = 'index.php?option=com_eventbooking&view=events';
		$this->setRedirect($url, $msg);
	}	
	/**
	 * Save field
	 *
	 */
	function save_field() {
		$post = JRequest::get('post' , JREQUEST_ALLOWRAW);
		$model =  $this->getModel('field') ;
		$cid = $post['cid'];
		$post['id'] = (int) $cid[0];
		$ret =  $model->store($post);
		if ($ret) {
			$msg = JText::_('EB_FIELD_SAVED');	
		} else {
			$msg = JText::_('EB_FIELD_SAVE_ERROR') ;
		}
		$this->setRedirect('index.php?option=com_eventbooking&view=fields', $msg);
	}
	/**
	 * Remove selected fields
	 *
	 */	
	function remove_fields() {
		$cid = JRequest::getVar('cid', null);
		JArrayHelper::toInteger($cid);
		$model = $this->getModel('field');
		$ret = $model->delete($cid);
		if ($ret) {
			$msg = JText::_('EB_FIELD_REMOVED'); 
		} else {
			$msg = JText::_('EB_FIELD_REMOVE_ERROR');
		}
		$this->setRedirect('index.php?option=com_eventbooking&view=fields', $msg);
	}
	/**
	 * Save ordering of custom fields
	 * 
	 */
	function save_field_order() {
		$order = JRequest::getVar('order', array(), 'post') ;
		$cid = JRequest::getVar('cid', array(), 'post') ;
		JArrayHelper::toInteger($order);
		JArrayHelper::toInteger($cid);
		$model = & $this->getModel('field');
		$ret = $model->saveOrder($cid, $order);
		if ($ret) {
			$msg = JText::_('EB_ORDERING_SAVED') ; 
		} else {
			$msg = JText::_('EB_ORDERING_SAVING_ERROR') ;
		}
		$this->setRedirect('index.php?option=com_eventbooking&view=fields', $msg);	
	}
	/**
	 * Publish the selected fields
	 *
	 */
	function fields_publish() {
		$cid = JRequest::getVar('cid', null) ;
		JArrayHelper::toInteger($cid);
		$model = $this->getModel('field');
		$ret = $model->publish($cid , 1);
		if ($ret) {
			$msg = JText::_('EB_FIELD_PUBLISHED'); 
		} else {
			$msg = JText::_('EB_FIELD_PUBLISH_ERROR');
		}
		$this->setRedirect('index.php?option=com_eventbooking&view=fields', $msg);
	}
	/**
	 * Unpublish the selected fields
	 *
	 */
	function fields_unpublish() {
		$cid = JRequest::getVar('cid', null) ;
		JArrayHelper::toInteger($cid);
		$model = $this->getModel('field') ;
		$ret =  $model->publish($cid, 0);
		if ($ret) {
			$msg = JText::_('EB_FIELD_UNPUBLISHED'); 
		} else {
			$msg = JText::_('EB_FIELD_UNPUBLISH_ERROR');
		}
		$this->setRedirect('index.php?option=com_eventbooking&view=fields', $msg);
	}
	/**
	 * Copy a field
	 *
	 */
	function copy_field() {
		$cid = JRequest::getVar('cid', null) ;
		JArrayHelper::toInteger($cid);
		$id = $cid[0] ;
		$model = $this->getModel('field') ;
		$newFieldId =  $model->copy($id);		
		$this->setRedirect('index.php?option=com_eventbooking&task=edit_field&cid[]='.$newFieldId, JText::_('EB_FIELD_COPIED'));	
	}
	/**
	 * Require the selected fields
	 *
	 */
	function required() {
		$cid = JRequest::getVar('cid', null) ;
		JArrayHelper::toInteger($cid);
		$model = $this->getModel('field');
		$ret = $model->required($cid , 1);
		if ($ret) {
			$msg = JText::_('EB_FIELD_REQUIRED'); 
		} else {
			$msg = JText::_('EB_FIELD_REQUIRE_ERROR');
		}
		$this->setRedirect('index.php?option=com_eventbooking&view=fields', $msg);
	}
	
	/**
	 * Change status to un required
	 *
	 */
	function un_required() {
		$cid = JRequest::getVar('cid', null) ;
		JArrayHelper::toInteger($cid);
		$model = $this->getModel('field');
		$ret = $model->required($cid , 0);
		if ($ret) {
			$msg = JText::_('EB_FIELD_REQUIRED'); 
		} else {
			$msg = JText::_('EB_FIELD_REQUIRE_ERROR');
		}
		$this->setRedirect('index.php?option=com_eventbooking&view=fields', $msg);
	}
	/**
	 * Order up custom field
	 *
	 */
	function orderup_field() {
		$model =  $this->getModel('field');
		$model->move(-1);		 			
		$msg = JText::_('EB_ORDERING_UPDATED');			
		$url = 'index.php?option=com_eventbooking&view=fields';
		$this->setRedirect($url, $msg);
	}
	/**
	 * Order down a custom field
	 *
	 */
	function orderdown_field() {
		$model =  $this->getModel('field');
		$model->move(1);		 			
		$msg = JText::_('EB_ORDERING_UPDATED');			
		$url = 'index.php?option=com_eventbooking&view=fields';
		$this->setRedirect($url, $msg);
	}
	/**
	 * Redirect user to fields mangement page
	 *
	 */
	function cancel_field() {
		$this->setRedirect('index.php?option=com_eventbooking&view=fields');
	}				
	/**
	 * Export data to csv file
	 *
	 */
	function csv_export() {
		$db = JFactory::getDBO();		
		$config = EventBookingHelper::getConfig();		
		$delimiter = $config->csv_delimiter? $config->csv_delimiter : ',' ;		
		$taxEnabled = $config->enable_tax && ($config->tax_rate > 0) ;    
		$eventId = JRequest::getInt('event_id') ;
		$where = array() ;		
		$where[] = '(a.published = 1 OR (a.payment_method LIKE "os_offline%" AND a.published != 2))' ;
		if ($eventId)
			$where[] = ' a.event_id='.$eventId ;
		if (isset($config->include_group_billing_in_csv_export) && !$config->include_group_billing_in_csv_export)
			$where[] = ' a.is_group_billing = 0 ' ;
		if (!$config->include_group_members_in_csv_export)
			$where[] = ' a.group_id = 0 ';
		if ($config->show_coupon_code_in_registrant_list) {					
		    	$sql = 'SELECT a.*, b.event_date, b.title AS event_title, c.code AS coupon_code FROM #__eb_registrants AS a INNER JOIN #__eb_events AS b ON a.event_id = b.id LEFT JOIN #__eb_coupons AS c ON a.coupon_id=c.id WHERE '.implode(' AND ', $where).' ORDER BY a.id ';			
		} else {
		    $sql = 'SELECT a.*, b.event_date, b.title AS event_title FROM #__eb_registrants AS a INNER JOIN #__eb_events AS b ON a.event_id = b.id WHERE '.implode(' AND ', $where).' ORDER BY a.id ';    
		}			
		$db->setQuery($sql) ;		
		$rows  = $db->loadObjectList();
		if ($eventId)
			$sql = 'SELECT id, title FROM #__eb_fields WHERE published=1 AND (event_id = -1 OR id IN (SELECT field_id FROM #__eb_field_events WHERE event_id='.$eventId.')) ORDER BY ordering';
		else 
			$sql = 'SELECT id, title FROM #__eb_fields WHERE published=1  ORDER BY ordering';
		$db->setQuery($sql) ;
		$rowFields = $db->loadObjectList() ;					
		//Get the custom fields value and store them into an array
				
		$sql = 'SELECT id FROM #__eb_registrants AS a WHERE '.implode(' AND ', $where) ; 
		$db->setQuery($sql) ;		
		$registrantIds = array(0) ;
		if (version_compare(JVERSION, '3.0', 'ge')) {
			$registrantIds = array_merge($registrantIds, $db->loadColumn()) ;
		} else {
			$registrantIds = array_merge($registrantIds, $db->loadResultArray()) ;
		}		
		$sql = 'SELECT registrant_id, field_id, field_value FROM #__eb_field_values WHERE registrant_id IN ('.implode(',', $registrantIds).')';
		$db->setQuery($sql) ;
		$rowFieldValues = $db->loadObjectList();
		$fieldValues = array() ;
		for ($i = 0 , $n = count($rowFieldValues) ; $i < $n ; $i++) {
			$rowFieldValue = $rowFieldValues[$i] ;
			$fieldValues[$rowFieldValue->registrant_id][$rowFieldValue->field_id] = $rowFieldValue->field_value ;
		}			
		//Get name of groups
		$groupNames = array() ;
		$sql = 'SELECT id, first_name, last_name FROM #__eb_registrants AS a WHERE is_group_billing = 1'. (COUNT($where) ? ' AND '.implode(' AND ', $where) : '');
		$db->setQuery($sql);
		$rowGroups = $db->loadObjectList() ;
		if (count($rowGroups)) {
			foreach ($rowGroups as $rowGroup) {
				$groupNames[$rowGroup->id] = $rowGroup->first_name . ' '.$rowGroup->last_name ;
			}
		}				
		if(count($rows)){			
			$results_arr=array();					
			$csv_output = JText::_('EB_EVENT');
			if ($config->show_event_date) {
				$csv_output .= $delimiter. JText::_('EB_EVENT_DATE') ;
			}
			$csv_output .= $delimiter. JText::_('EB_FIRST_NAME') ;
			if ($config->s_lastname)
				$csv_output .= $delimiter. JText::_('EB_LAST_NAME');		
			if ($config->s_organization) 
				$csv_output .= $delimiter. JText::_('EB_ORGANIZATION');
			if ($config->s_address)
				$csv_output .= $delimiter. JText::_('EB_ADDRESS');
			if ($config->s_address2)
				$csv_output .= $delimiter. JText::_('EB_ADDRESS2');	
			if ($config->s_city)
				$csv_output .= $delimiter. JText::_('EB_CITY');
			if ($config->s_state)
				$csv_output .= $delimiter. JText::_('EB_STATE');			
			if ($config->s_zip)
				$csv_output .= $delimiter. JText::_('EB_ZIP');
			if ($config->s_country)
				$csv_output .= $delimiter. JText::_('EB_COUNTRY');
			if ($config->s_phone)
				$csv_output .= $delimiter. JText::_('EB_PHONE');			
			if ($config->s_fax)
				$csv_output .= $delimiter. JText::_('EB_FAX');	
			$csv_output .= $delimiter. JText::_('EB_EMAIL');
			$csv_output .= $delimiter. JText::_('EB_NUMBER_REGISTRANTS');									
			$csv_output .= $delimiter. JText::_('EB_AMOUNT');			
			if ($taxEnabled) {
			    $csv_output .= $delimiter. JText::_('EB_TAX');
			}			
			if ($config->activate_deposit_feature) {
			    $csv_output .= $delimiter. JText::_('EB_DEPOSIT_AMOUNT');
			    $csv_output .= $delimiter. JText::_('EB_DUE_AMOUNT');
			}
			if ($config->show_coupon_code_in_registrant_list) {
			    $csv_output .= $delimiter. JText::_('EB_COUPON');
			}			
			$csv_output .= $delimiter. JText::_('EB_REGISTRATION_DATE');
			$csv_output .= $delimiter. JText::_('EB_TRANSACTION_ID');
			$csv_output .= $delimiter. JText::_('EB_PAYMENT_STATUS');		
			if (count($rowFields)) {
				foreach ($rowFields as  $rowField) 
					$csv_output .= $delimiter.$rowField->title ;	
			}			
			if ($config->s_comment)
				$csv_output .= $delimiter. JText::_('EB_COMMENT');			
			$csv_output = ltrim($csv_output) ;			
			foreach($rows as $r) {
				$results_arr=array();
				$results_arr[]=$r->event_title ;
				if ($config->show_event_date) {
					$results_arr[] = JHTML::_('date', $r->event_date, $config->date_format, null) ;															
				}
				if ($r->is_group_billing)
					$results_arr[]=$r->first_name.' '.JText::_('EB_GROUP_BILLING');
				elseif ($r->group_id > 0)
					$results_arr[]=$r->first_name.' '.JText::_('EB_GROUP').$groupNames[$r->group_id] ;
				else 
					$results_arr[]=$r->first_name ;
				if ($config->s_lastname)
					$results_arr[]=$r->last_name ;
				if ($config->s_organization)
					$results_arr[]=$r->organization;
				if ($config->s_address)
					$results_arr[]=$r->address;
				if ($config->s_address2)
					$results_arr[]=$r->address2;	
				if ($config->s_city)
					$results_arr[]=$r->city;
				if ($config->s_state)
					$results_arr[]=$r->state;	
				if ($config->s_zip)
					$results_arr[]=$r->zip;
				if ($config->s_country)
					$results_arr[]=$r->country;
				if ($config->s_phone)
					$results_arr[]=$r->phone;								
				if ($config->s_fax)
					$results_arr[]=$r->fax;	
				$results_arr[]=$r->email;		
				$results_arr[] = $r->number_registrants ;						
				$results_arr[]= number_format($r->amount, 2);	
                if ($taxEnabled)
                    $results_arr[]= number_format($r->tax_amount, 2);
				if ($config->activate_deposit_feature) {
				    if ($r->deposit_amount > 0) {
				        $results_arr[]= number_format($r->deposit_amount, 2);
				        $results_arr[]= number_format($r->amount - $r->deposit_amount, 2);
				    } else {
				        $results_arr[]= '';
				        $results_arr[]= '';
				    }
				}

				if ($config->show_coupon_code_in_registrant_list) {
				    $results_arr[]= $r->coupon_code ;
				}
				
				$results_arr[]= JHTML::_('date', $r->register_date, $config->date_format, null);							
				$results_arr[]= $r->transaction_id ;	
				if ($r->published) {
					$results_arr[]= 'Paid' ;
				} else {
					$results_arr[]= 'Not Paid' ;
				}
				if (count($rowFields))
					foreach ($rowFields as $rowField) {
						$results_arr[] = @$fieldValues[$r->id][$rowField->id] ;						
					}
				if ($config->s_comment)
					$results_arr[]= $r->comment;																				
				$csv_output .= "\n\"".implode ('"'.$delimiter.'"', $results_arr)."\"";				
			}
			$csv_output .= "\n";	
			if (ereg('Opera(/| )([0-9].[0-9]{1,2})', $_SERVER['HTTP_USER_AGENT'])) {
				$UserBrowser = "Opera";
			}
			elseif (ereg('MSIE ([0-9].[0-9]{1,2})', $_SERVER['HTTP_USER_AGENT'])) {	
				$UserBrowser = "IE";
			} else {
				$UserBrowser = '';	}	
				$mime_type = ($UserBrowser == 'IE' || $UserBrowser == 'Opera') ? 'application/octetstream' : 'application/octet-stream';
				$filename = "registrants_list";	
				@ob_end_clean();	
				ob_start();	
				header('Content-Type: ' . $mime_type);	
				header('Expires: ' . gmdate('D, d M Y H:i:s') . ' GMT');	
				if ($UserBrowser == 'IE') {
					header('Content-Disposition: attachment; filename="' . $filename . '.csv"');
					header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
					header('Pragma: public');
				}
				else {
					header('Content-Disposition: attachment; filename="' . $filename . '.csv"');
					header('Pragma: no-cache');
				}
				print $csv_output;
				exit();
		}	
	}	
	/**
	 * Save the location
	 *
	 */
	function save_location() {
		$post = JRequest::get('post' , JREQUEST_ALLOWRAW);
		$model =  $this->getModel('location') ;
		$cid = $post['cid'];
		$post['id'] = (int) $cid[0];
		$ret =  $model->store($post);
		if ($ret) {
			$msg = JText::_('EB_LOCATION_SAVED');	
		} else {
			$msg = JText::_('EB_LOCATION_SAVE_ERROR') ;
		}
		$this->setRedirect('index.php?option=com_eventbooking&view=locations', $msg);
	}	
	/**
	 * Removing locations
	 *
	 */
	function remove_locations() {
		$model =  $this->getModel('location');
		$cid = JRequest::getVar('cid', array()) ;
		JArrayHelper::toInteger($cid);
		$model->delete($cid);
		$msg = JText::_('EB_LOCATION_REMOVED');			
		$url = 'index.php?option=com_eventbooking&view=locations';
		$this->setRedirect($url, $msg);
	}	
	/**
	 * Unpublish Locations
	 *
	 */		
	function locations_publish() {
		$cid = JRequest::getVar('cid', array(), 'post');
		JArrayHelper::toInteger($cid);
		$model = & $this->getModel('location');
		$ret = $model->publish($cid, 1);
		if ($ret) 
			$msg = JText::_('EB_LOCATION_PUBLISHED');
		else
			$msg = JText::_('EB_LOCATION_PUBLISH_ERROR');		
		$this->setRedirect('index.php?option=com_eventbooking&view=locations', $msg);		
	}
	/**
	 * Publish Locations
	 *
	 */
	function locations_unpublish() {
		$cid = JRequest::getVar('cid', array(), 'post');
		JArrayHelper::toInteger($cid);
		$model = & $this->getModel('location');
		$ret = $model->publish($cid, 0);
		if ($ret) 
			$msg = JText::_('EB_LOCATION_UNPUBLISHED');
		else
			$msg = JText::_('EB_LOCATION_UNPUBLISH_ERROR');		
		$this->setRedirect('index.php?option=com_eventbooking&view=locations', $msg);
	}	
	/**
	 * Cancel the location . Redirect user to locations list page
	 *
	 */
	function cancel_location() {
		$this->setRedirect('index.php?option=com_eventbooking&view=locations');
	}
	/**
	 * Cancel editing/adding registrant. Redirect users to registrants management screen
	 *
	 */
	function cancel_registrant() {
		$this->setRedirect('index.php?option=com_eventbooking&view=registrants');
	}	
	/**
	 * Save the registrants
	 *
	 */
	function save_registrant() {
		$post = JRequest::get('post' , JREQUEST_ALLOWRAW);
		$model =  $this->getModel('registrant') ;
		$cid = $post['cid'];
		$post['id'] = (int) $cid[0];		
		$ret =  $model->store($post);
		if ($ret) {
			$msg = JText::_('EB_REGISTRANT_SAVED');	
		} else {
			$msg = JText::_('EB_REGISTRANT_SAVE_ERROR') ;
		}
	    $task = $this->getTask() ;		
		if ($task == 'save_registrant') {
			$url = 'index.php?option=com_eventbooking&view=registrants' ;
		} else {
			$url = 'index.php?option=com_eventbooking&task=edit_registrant&cid[]='.$post['id'] ;
		}
		$this->setRedirect($url, $msg);
	}	
	/**
	 * Removing locations
	 *
	 */
	function remove_registrants() {
		$model =  $this->getModel('registrant');
		$cid = JRequest::getVar('cid', array()) ;
		JArrayHelper::toInteger($cid);
		$model->delete($cid);
		$msg = JText::_('EB_REGISTRANT_REMOVED');			
		$url = 'index.php?option=com_eventbooking&view=registrants';
		$this->setRedirect($url, $msg);
	}	
	/**
	 * Publish Registrants
	 *
	 */		
	function registrants_publish() {
		$cid = JRequest::getVar('cid', array(), 'post');
		JArrayHelper::toInteger($cid);
		$model = & $this->getModel('registrant');
		$ret = $model->publish($cid, 1);
		if ($ret) 
			$msg = JText::_('EB_REGISTRANT_PUBLISHED');
		else
			$msg = JText::_('EB_REGISTRANT_PUBLISH_ERROR');		
		$this->setRedirect('index.php?option=com_eventbooking&view=registrants', $msg);		
	}
	/**
	 * Unpublish registrants
	 *
	 */
	function registrants_unpublish() {
		$cid = JRequest::getVar('cid', array(), 'post');
		JArrayHelper::toInteger($cid);
		$model = & $this->getModel('registrant');
		$ret = $model->publish($cid, 0);
		if ($ret) 
			$msg = JText::_('EB_REGISTRANT_UNPUBLISHED');
		else
			$msg = JText::_('EB_REGISTRANT_UNPUBLISH_ERROR');		
		$this->setRedirect('index.php?option=com_eventbooking&view=registrants', $msg);
	}
	/**
	 * Save members information for group registration
	 *
	 */	
	function save_members() {
		$post = JRequest::get('post', JREQUEST_ALLOWRAW); 
		$model = & $this->getModel('members') ;
		$model->store($post);
		$this->setRedirect('index.php?option=com_eventbooking&view=registrants', 'Member information saved');
	}
	/**
	 * Redirect users to registrants management page
	 *
	 */
	function cancel_member() {
		$this->setRedirect('index.php?option=com_eventbooking&view=registrants');
	}
	/**
	 * Save translation
	 *
	 */
	function save_translation() {
		$post = JRequest::get('post', JREQUEST_ALLOWRAW) ;
		$model = & $this->getModel('Language') ;
		$model->save($post);	
		$this->setRedirect('index.php?option=com_eventbooking&task=show_translation');	
	}
	/**
	 * Redirect user to translation management screen
	 *
	 */
	function cancel_translation() {
		$this->setRedirect('index.php?option=com_eventbooking&task=show_translation');
	}
	/**
	 * Save plugin
	 *
	 */
	function save_plugin() {
		$model = & $this->getModel('plugin') ;
		$data = JRequest::get('post') ;
		$ret = $model->store($data);
		if ($ret) {
			$msg = JText::_('EB_PLUGIN_SAVED') ;		
		} else {
			$msg = JText::_('EB_PLUGIN_SAVE_ERROR') ;
		}
		$this->setRedirect( 'index.php?option=com_eventbooking&view=plugins', $msg);
	}	
	/**
	 * Install the plugin
	 *
	 */
	function install_plugin() {
		$model = & $this->getModel('plugin') ;
		$ret = $model->install();
		if ($ret) {
			$msg = JText::_('EB_PLUGIN_INSTALLED');
		} else {
			$msg = JRequest::getVar('msg', 'EB_PLUGIN_INSTALL_ERROR') ;
		}
		$this->setRedirect('index.php?option=com_eventbooking&view=plugins', $msg);		
	}
	/**
	 * Uninstall the selected plugin
	 *
	 */	
	function uninstall_plugin() {
		$model = & $this->getModel('plugin');
		$cid = JRequest::getVar('cid', array(), 'post', 'array') ;
		JArrayHelper::toInteger($cid);
		$ret = $model->uninstall($cid[0]);
		if ($ret) {
			$msg = JText::_('EB_PLUGIN_UNINSTALLED');	
		} else {
			$msg = JRequest::getVar('msg', 'EB_PLUGIN_UNINSTALL_ERROR') ;
		}
		$this->setRedirect('index.php?option=com_eventbooking&view=plugins', $msg);		
	}
	/**
	 * Publish selected plugins
	 *
	 */
	function plugins_publish() {
		$model = & $this->getModel('plugin') ;
		$cid = JRequest::getVar('cid', array(), 'post', 'array') ;
		JArrayHelper::toInteger($cid);
		$ret = $model->publish($cid, 1) ;
		if ($ret) {
			$msg =  JText::_('EB_PLUGIN_PUBLISHED');
		} else {
			$msg = JText::_('EB_PLUGIN_PUBLISH_ERROR');
		}
		$this->setRedirect('index.php?option=com_eventbooking&view=plugins', $msg);
	}
	/**
	 * Unpublish selected plugins
	 *
	 */
	function plugins_unpublish() {
		$model = & $this->getModel('plugin') ;
		$cid = JRequest::getVar('cid', array(), 'post', 'array') ;
		JArrayHelper::toInteger($cid);
		$ret = $model->publish($cid, 0) ;
		if ($ret) {
			$msg =  JText::_('EB_PLUGIN_UNPUBLISHED');
		} else {
			$msg = JText::_('EB_PLUGIN_UNPUBLISH_ERROR');
		}
		$this->setRedirect('index.php?option=com_eventbooking&view=plugins', $msg);
	}	
	/**
	 * Save ordering of the selected category
	 *
	 */
	function save_plugin_order() {
		$order = JRequest::getVar('order', array(), 'post') ;
		$cid = JRequest::getVar('cid', array(), 'post') ;
		JArrayHelper::toInteger($order);
		JArrayHelper::toInteger($cid);
		$model = & $this->getModel('plugin');
		$ret = $model->saveOrder($cid, $order);
		if ($ret) {
			$msg = JText::_('EB_ORDERING_SAVED') ; 
		} else {
			$msg = JText::_('EB_ORDERING_SAVING_ERROR') ;
		}
		$this->setRedirect('index.php?option=com_eventbooking&view=plugins', $msg);
	}
	/**
	 * Order up a category
	 *
	 */
	function orderup_plugin() {
		$model =  $this->getModel('plugin');
		$model->move(-1);		 			
		$msg = JText::_('Ordering updated');			
		$url = 'index.php?option=com_eventbooking&view=plugins';
		$this->setRedirect($url, $msg);
	}
	/**
	 * Order down a category
	 *
	 */
	function orderdown_plugin() {
		$model =  $this->getModel('plugin');
		$model->move(1);		 			
		$msg = JText::_('EB_ORDERING_UPDATED');			
		$url = 'index.php?option=com_eventbooking&view=plugins';
		$this->setRedirect($url, $msg);
	}			
	/**
	 * Redirect to plugin management homepage
	 *
	 */
	function cancel_plugin() {
		$this->setRedirect('index.php?option=com_eventbooking&view=plugins');
	}
	/***
	 * Upgrade
	 */
	function upgrade() {
		require_once JPATH_COMPONENT.'/install.eventbooking.php' ;
		com_eventbookingInstallerScript::_updateDatabaseSchema();
	}	
	/**
	 * Save coupon
	 *
	 */
	function save_coupon() {
		$post = JRequest::get('post' , JREQUEST_ALLOWRAW);
		$model =  $this->getModel('coupon') ;
		$cid = $post['cid'];
		$post['id'] = (int) $cid[0];
		$ret =  $model->store($post);
		if ($ret) {
			$msg = JText::_('Successfully saving coupon');	
		} else {
			$msg = JText::_('Error while saving coupon') ;
		}
		$this->setRedirect('index.php?option=com_eventbooking&view=coupons', $msg);
	}
	/**
	 * Remove selected fields
	 *
	 */	
	function remove_coupons() {
		$cid = JRequest::getVar('cid', null);
		JArrayHelper::toInteger($cid);
		$model = $this->getModel('coupon');
		$ret = $model->delete($cid);
		if ($ret) {
			$msg = JText::_('EB_COUPON_SAVED'); 
		} else {
			$msg = JText::_('EB_COUPON_SAVE_ERROR');
		}
		$this->setRedirect('index.php?option=com_eventbooking&view=coupons', $msg);
	}
	/**
	 * Publish the selected coupons
	 *
	 */
	function coupons_publish() {
		$cid = JRequest::getVar('cid', null) ;
		JArrayHelper::toInteger($cid);
		$model = $this->getModel('coupon');
		$ret = $model->publish($cid , 1);
		if ($ret) {
			$msg = JText::_('EB_COUPON_PUBLISHED'); 
		} else {
			$msg = JText::_('EB_COUPON_PUBLISH_ERROR');
		}
		$this->setRedirect('index.php?option=com_eventbooking&view=coupons', $msg);
	}
	/**
	 * Unpublish the selected fields
	 *
	 */
	function coupons_unpublish() {
		$cid = JRequest::getVar('cid', null) ;
		JArrayHelper::toInteger($cid);
		$model = $this->getModel('coupon') ;
		$ret =  $model->publish($cid, 0);
		if ($ret) {
			$msg = JText::_('EB_COUPON_UNPUBLISHED'); 
		} else {
			$msg = JText::_('EB_COUPON_UNPUBLISH_ERROR');
		}
		$this->setRedirect('index.php?option=com_eventbooking&view=coupons', $msg);
	}
	/**
	 * Redirect users to coupons page
	 * 
	 */
	function cancel_coupon() {		
		$this->setRedirect('index.php?option=com_eventbooking&view=coupons') ;			
	}		
	/**
	 * Send massmail to all registrants of an event
	 * 
	 */
    function send_massmail() {        
        $data = JRequest::get('post', JREQUEST_ALLOWRAW) ;
        $model = & $this->getModel('massmail') ;
        $model->send($data);
        $this->setRedirect('index.php?option=com_eventbooking&task=show_massmail_form', JText::_('EB_EMAIL_SENT')) ;        
    }        
    #Waiting list implementation    
    /**
	 * Cancel editing/adding waiting. Redirect users to waitings management screen
	 *
	 */
	function cancel_waiting() {
		$this->setRedirect('index.php?option=com_eventbooking&view=waitings');
	}	
	/**
	 * Save the waiting
	 *
	 */
	function save_waiting() {
		$post = JRequest::get('post' , JREQUEST_ALLOWRAW);
		$model =  $this->getModel('waiting') ;
		$cid = $post['cid'];
		$post['id'] = (int) $cid[0];
		$ret =  $model->store($post);
		if ($ret) {
			$msg = JText::_('EB_WAITING_SAVED');	
		} else {
			$msg = JText::_('EB_WAITING_SAVE_ERROR') ;
		}
		$this->setRedirect('index.php?option=com_eventbooking&view=waitings', $msg);
	}	
	/**
	 * Removing waiting
	 *
	 */
	function remove_waitings() {
		$model =  $this->getModel('waiting');
		$cid = JRequest::getVar('cid', array()) ;
		JArrayHelper::toInteger($cid);
		$model->delete($cid);
		$msg = JText::_('EB_WAITING_REMOVED');			
		$url = 'index.php?option=com_eventbooking&view=waitings';
		$this->setRedirect($url, $msg);
	}
	/**
	 * Fix day light saving time issue
	 */
	function fix_daylight_saving_time() {
		$post = JRequest::get('post' , JREQUEST_ALLOWRAW);
		$model =  $this->getModel('daylightsaving');
		$model->process($post);		
		$this->setRedirect('index.php?option=com_eventbooking&view=waitings', JText::_('Day Light saving time issue fixed'));
	}	
	/**
	 * Fix error build admin menu issue
	 */
	function fix_error_build_admin_menu()
	{
		$db = JFactory::getDbo();
		$sql = 'DELETE FROM #__menu WHERE link LIKE "%index.php?option=com_eventbooking%"';
		$db->setQuery($sql);
		$db->query();
	}
	function sync() {
		jimport('joomla.filesystem.folder') ;
		JFolder::copy(JPATH_ROOT.'/components/com_eventbooking', 'D:/www/joomla30/components/com_eventbooking', '', true) ;
		JFolder::copy(JPATH_ROOT.'/administrator/components/com_eventbooking', 'D:/www/joomla30/administrator/components/com_eventbooking', '', true) ;
	}
}