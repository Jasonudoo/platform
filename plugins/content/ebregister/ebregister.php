<?php
// Check to ensure this file is included in Joomla!
defined( '_JEXEC' ) or die( 'Restricted access' );
jimport('joomla.plugin.plugin');
class plgContentEBRegister extends JPlugin
{
	/**
	 * Constructor
	 *
	 * For php4 compatability we must not use the __constructor as a constructor for plugins
	 * because func_get_args ( void ) returns a copy of all passed arguments NOT references.
	 * This causes problems with cross-referencing necessary for the observer design pattern.
	 *
	 * @param object $subject The object to observe
	 * @param object $params  The object that holds the plugin parameters
	 * @since 1.5
	 */
	function plgContentEBRegister( &$subject, $params )
	{
		parent::__construct( $subject, $params );
	}
	/**	 	 
	 * Method is called by the view
	 *
	 * @param 	object		The article object.  Note $article->text is also available
	 * @param 	object		The article params
	 * @param 	int			The 'page' number
	 */
	function onContentPrepare($context, &$article, &$params, $limitstart)
	{		
		error_reporting(0);
		$app = JFactory::getApplication() ;
		if ($app->getName() != 'site') {
			return true;
		}	
		if ( strpos( $article->text, 'ebregister' ) === false ) {
			return true;
		}
		$regex = "#{ebregister (\d+)}#s";
		$article->text = preg_replace_callback( $regex, array( &$this, '_replaceEBRegister') , $article->text );
		return true;
	}	
	/**
	 * Replace the text with the event detail
	 * 
	 * @param array $matches
	 */
	function _replaceEBRegister(&$matches) {						
		$document = JFactory::getDocument();	
		require_once JPATH_ROOT.'/components/com_eventbooking/helper/fields.php';
		require_once (JPATH_ROOT.'/components/com_eventbooking/helper/helper.php');
		require_once (JPATH_ROOT.'/components/com_eventbooking/payments/os_payment.php');
		require_once (JPATH_ROOT.'/components/com_eventbooking/payments/os_payments.php');		
		$Itemid = EventBookingHelper::getItemid();
		EventBookingHelper::loadLanguage();		
		$viewConfig['name'] = 'form' ;
		$viewConfig['base_path'] = JPATH_ROOT.'/plugins/content/ebregister/ebregister' ;
		$viewConfig['template_path'] = JPATH_ROOT.'/plugins/content/ebregister/ebregister' ;		
		$viewConfig['layout'] = 'default' ;
		$view =  new JViewLegacy($viewConfig) ;	
		$document = & JFactory::getDocument() ;
		$document->addStyleSheet(JURI::base(true).'/components/com_eventbooking/assets/css/style.css') ;
		$document->addScript(JURI::base(true).'/components/com_eventbooking/assets/js/paymentmethods.js');
		EventBookingHelper::loadBootstrap(false);				
		$eventId = $matches[1] ;									
		$db = JFactory::getDBO();
		$user = JFactory::getUser();		
		$userId = $user->get('id');
		$config = EventBookingHelper::getConfig() ;									
		if (($userId > 0) && ($config->cb_integration == 1)) {
			$sql = 'SELECT * FROM #__comprofiler WHERE user_id='.$userId;
			$db->setQuery($sql);
			$rowProfile = $db->loadObject();
			$mFirstname = $config->m_firstname ? $config->m_firstname : 'firstname' ;			
			$mLastname = $config->m_lastname ? $config->m_lastname : 'lastname';
			$mOrganization = $config->m_organization ? $config->m_organization : 'organization' ;
			$mAddress = $config->m_address ? $config->m_address : 'address';			
			$mAddress2 = $config->m_address2 ? $config->m_address2 : 'address2';
			$mCity = $config->m_city  ? $config->m_city : 'city';
			$mState = $config->m_state ? $config->m_state : 'state' ;
			$mZip = $config->m_zip ? $config->m_zip : 'zip';
			$mCountry = $config->m_country ? $config->m_country : 'country';
			$mPhone = $config->m_phone ? $config->m_phone : 'phone' ;
			$mFax = $config->m_fax ? $config->m_fax : 'fax' ;																
			$firstName = JRequest::getVar('first_name', @$rowProfile->$mFirstname, 'post');
			$lastName = JRequest::getVar('last_name', @$rowProfile->$mLastname, 'post');
			$organization = JRequest::getVar('organization', @$rowProfile->$mOrganization, '');
			$address = JRequest::getVar('address', @$rowProfile->$mAddress, 'post');
			$address2 = JRequest::getVar('address2', @$rowProfile->$mAddress2, 'post');
			$city = JRequest::getVar('city', @$rowProfile->$mCity, 'post');
			$state = JRequest::getVar('state', @$rowProfile->$mState, 'post');
			$zip = JRequest::getVar('zip', @$rowProfile->$mZip, 'post');
			$country = JRequest::getVar('country', @$rowProfile->$mCountry, 'post');
			$phone = JRequest::getVar('phone', @$rowProfile->$mPhone, 'post');					
			$fax = JRequest::getVar('fax', @$rowProfile->$mFax, 'post');					
		} elseif (($userId > 0) && ($config->cb_integration == 2)) {
			//Read information from database
			$sql = 'SELECT cf.fieldcode , fv.value FROM #__community_fields AS cf '
				. ' INNER JOIN #__community_fields_values AS fv '
				. ' ON cf.id = fv.field_id '
				. ' WHERE fv.user_id = '.$userId 
			;				
			$db->setQuery($sql);			
			$rows = $db->loadObjectList();
			$fieldData = array() ;
			for ($i = 0 , $n = count($rows) ; $i < $n ; $i++) {
				$row = $rows[$i] ;
				$fieldData["$row->fieldcode"] = $row->value ;
			}						
			$mFirstname = $config->m_firstname ?  $config->m_firstname : 'firstname';
			$mLastname = $config->m_lastname ? $config->m_lastname : 'lastname';
			$mOrganization = $config->m_organization ? $config->m_organization : 'organization' ;			
			$mAddress = $config->m_address ? $config->m_address : 'address';
			$mAddress2 = $config->m_address2 ? $config->m_address2 : 'address2' ;
			$mCity = $config->m_city ? $config->m_city : 'city' ;
			$mState = $config->m_state ? $config->m_state : 'state' ;
			$mZip = $config->m_zip ? $config->m_zip : 'zip' ;
			$mCountry = $config->m_country ? $config->m_country : 'country';
			$mPhone = $config->m_phone ? $config->m_phone : 'phone';
			$mFax = $config->m_fax ? $config->m_fax : 'fax' ;																				
			$firstName = JRequest::getVar('first_name', @$fieldData["$mFirstname"], 'post');
			$lastName = JRequest::getVar('last_name', @$fieldData["$mLastname"], 'post');
			$organization = JRequest::getVar('organization', @$fieldData["$mOrganization"], '');
			$address = JRequest::getVar('address', @$fieldData["$mAddress"], 'post');
			$address2 = JRequest::getVar('address2', @$fieldData["$mAddress2"], 'post');
			$city = JRequest::getVar('city', @$fieldData["$mCity"], 'post');
			$state = JRequest::getVar('state', @$fieldData["$mState"], 'post');
			$zip = JRequest::getVar('zip', @$fieldData["$mZip"], 'post');
			$country = JRequest::getVar('country', @$fieldData["$mCountry"], 'post');
			$phone = JRequest::getVar('phone', @$fieldData["$mPhone"], 'post');			
			$fax = JRequest::getVar('fax', @$fieldData["$mFax"], 'post');			
		} else {			
			$row = null ;
			if ($userId) {
				$sql = 'SELECT * FROM #__eb_registrants WHERE user_id = '.$userId .' ORDER BY id LIMIT 1';
				$db->setQuery($sql) ;
				$row = $db->loadObject();				
			}
			if (!$row) {
				$row = new stdClass() ;
			}
			$firstName = JRequest::getVar('first_name', @$row->first_name, 'post');
			$lastName = JRequest::getVar('last_name', @$row->last_name, 'post');
			$organization = JRequest::getVar('organization', @$row->organization, '');
			$address = JRequest::getVar('address', @$row->address, 'post');
			$address2 = JRequest::getVar('address2', @$row->address2, 'post');
			$city = JRequest::getVar('city', @$row->city, 'post');
			$state = JRequest::getVar('state', @$row->state, 'post');
			$zip = JRequest::getVar('zip', @$row->zip, 'post');
			$country = JRequest::getVar('country', @$row->country ? @$row->country : $config->default_country, 'post');
			$phone = JRequest::getVar('phone', @$row->phone, 'post');
			$fax = JRequest::getVar('fax', @$row->fax, 'post');
		}				
		$email = JRequest::getVar('email', $user->get('email'), 'post');
		$comment = JRequest::getVar('comment', '' ,'post');
		if (!$country)
			$country = $config->default_country ;
		$paymentMethod = JRequest::getVar('payment_method', os_payments::getDefautPaymentMethod(), 'post');					
		$x_card_num = JRequest::getVar('x_card_num', '', 'post');
		$expMonth =  JRequest::getVar('exp_month', date('m'), 'post') ;				
		$expYear = JRequest::getVar('exp_year', date('Y'), 'post') ;		
		$x_card_code = JRequest::getVar('x_card_code', '', 'post');
		$cardHolderName = JRequest::getVar('card_holder_name', '', 'post') ;
		$lists['exp_month'] = JHTML::_('select.integerlist', 1, 12, 1, 'exp_month', '', $expMonth, '%02d') ;
		$curentYear = date('Y') ;
		$lists['exp_year'] = JHTML::_('select.integerlist', $curentYear, $curentYear + 10 , 1, 'exp_year', '', $expYear) ;																							
		//Get list of country		
		$sql  = 'SELECT name AS value, name AS text FROM #__eb_countries WHERE published = 1 ORDER BY name';
		$db->setQuery($sql);
		$rowCountries = $db->loadObjectList();
		$options = array();
		$options[] = JHTML::_('select.option', '', JText::_('EB_SELECT_COUNTRY'));
		$options = array_merge($options, $rowCountries);	
		if ($config->display_state_dropdown) {
			$onChange = ' onchange="updateStateList();" ' ;
		} else {
			$onChange = '' ;
		}	
		$lists['country_list'] =  JHTML::_('select.genericlist', $options, 'country' , $onChange, 'value', 'text', $country);								
		//Custom fields feature					
		$jcFields = new JCFields($eventId, true, 0);		
		if ($jcFields->getTotal()) {
			$customField = true ;
			$fields = $jcFields->renderCustomFields();
			$validations = $jcFields->renderJSValidation();
			$fieldsList = $jcFields->getFields();
			$fieldsOutput = $jcFields->getFieldsOutput();
			$view->assignRef('fieldsList', $fieldsList) ;
			$view->assignRef('fieldsOutput', $fieldsOutput) ;
			$view->assignRef('fields', $fields);
			$view->assignRef('validations', $validations) ;			
		} else {
			$customField = false ;
		}									
		$sql = 'SELECT * FROM #__eb_events WHERE id='.$eventId ;
		$db->setQuery($sql) ;		
		$event = $db->loadObject();
		$params = new JRegistry($event->params) ;
		$keys = array('s_lastname', 'r_lastname', 's_organization', 'r_organization', 's_address', 'r_address', 's_address2', 'r_address2', 's_city', 'r_city', 's_state', 'r_state', 's_zip', 'r_zip', 's_country', 'r_country', 's_phone', 'r_phone', 's_fax', 'r_fax', 's_comment', 'r_comment');
		foreach ($keys as $key) {
			$config->$key = $params->get($key, 0) ;
		}															
		$rate = $event->individual_price ;				
		$customFields =  new JCFields($eventId, false, false) ;
		$extraFee = $customFields->calculateFee($eventId) ;
		$totalAmount = $rate + $extraFee ;			
		$methods = os_payments::getPaymentMethods();				
		//TODO: Get enabled card type from configuration function
		$options =  array() ;
		$options[] = JHTML::_('select.option', 'Visa', 'Visa') ;		 
		$options[] = JHTML::_('select.option', 'MasterCard', 'MasterCard') ;		 
		$options[] = JHTML::_('select.option', 'Discover', 'Discover') ;		 
		$options[] = JHTML::_('select.option', 'Amex', 'American Express') ;
		$lists['card_type'] = JHTML::_('select.genericlist', $options, 'card_type', ' class="inputbox" ', 'value', 'text') ;
		$couponCode = JRequest::getVar('coupon_code', '', 'post');						
		$enableCoupon = $config->enable_coupon ;		
		$username = JRequest::getVar('username', '');
		$password = JRequest::getVar('password', '');
		$errorCoupon = JRequest::getVar('error_coupon', 0);
		$registrationErrorCode = JRequest::getVar('registration_error_code', 0);	
		$idealEnabled = EventBookingHelper::idealEnabled();
		if ($idealEnabled) {			
			$bankLists = EventBookingHelper::getBankLists() ;			
			$options = array() ;
			foreach ($bankLists as $bankId => $bankName) {
				$options[] = JHTML::_('select.option', $bankId, $bankName) ; 
			}	
			$lists['bank_id'] = JHTML::_('select.genericlist', $options, 'bank_id', ' class="inputbox" ', 'value', 'text', JRequest::getInt('bank_id'));				
		}		
		$sql = 'SELECT COUNT(*) FROM #__eb_fields WHERE published=1 AND fee_field = 1 AND (event_id = -1 OR id IN (SELECT field_id FROM #__eb_field_events WHERE event_id='.$eventId.')) AND (display_in IN (0, 1, 3, 5))';
		$db->setQuery($sql) ;
		$numberFeeFields = (int)$db->loadResult();
		
		
		//State dropdown
		if ($config->display_state_dropdown) {
			//Get list of country and corresponding states
			$sql = 'SELECT country_id, CONCAT(state_2_code, ":", state_name) AS state_name FROM #__eb_states';
			$db->setQuery($sql) ;
			$rowStates = $db->loadObjectList();
			$states = array() ;
			for ($i = 0 , $n = count($rowStates) ; $i < $n ; $i++) {
				$rowState = $rowStates[$i] ;
				$states[$rowState->country_id][] = $rowState->state_name ;
			}
			$stateString = " var stateList = new Array();\n" ;
			foreach ($states as $countryId => $stateArray) {
				$stateString .= " stateList[$countryId] = \"".implode(',', $stateArray)."\";\n" ;
			}
			$view->assignRef('stateString', $stateString) ;
			$options = array() ;
			$options[] = JHTML::_('select.option', '', JText::_('EB_SELECT_STATE'), 'state_2_code', 'state_name') ;			
			if ($country) {
				$sql = 'SELECT country_id FROM #__eb_countries WHERE LOWER(name)="'.JString::strtolower($country).'"';
				$db->setQuery($sql) ;
				$countryId = $db->loadResult();
				if ($countryId) {
					$sql = 'SELECT state_2_code, state_name FROM #__eb_states WHERE country_id='.$countryId;
					$db->setQuery($sql) ;
					$options = array_merge($options, $db->loadObjectList()) ;
				}
			}
			$lists['state'] = JHTML::_('select.genericlist', $options, 'state', ' class="inputbox" ', 'state_2_code', 'state_name', $state) ;
			$sql = 'SELECT country_id, name FROM #__eb_countries';
			$db->setQuery($sql) ;
			$rowCountries = $db->loadObjectList();
			$countryIdsString = " var countryIds = new Array(); \n" ;
			$countryNamesString = " var countryNames = new Array(); \n" ;
			$i = 0;
			foreach ($rowCountries as $rowCountry) {
				$countryIdsString .= " countryIds[".$i."] = $rowCountry->country_id;\n" ;
				$countryNamesString .= " countryNames[".$i."]= \"$rowCountry->name\"\n" ;
				$i++ ;
			}
			$view->assignRef('countryIdsString', $countryIdsString) ;
			$view->assignRef('countryNamesString', $countryNamesString) ;
		}
		
		
		//Assign these parameters							
		$view->assignRef('userId', $userId) ;		
		$view->assignRef('firstName', $firstName);
		$view->assignRef('lastName', $lastName);
		$view->assignRef('organization', $organization);
		$view->assignRef('address', $address);
		$view->assignRef('address2', $address2);
		$view->assignRef('city', $city);
		$view->assignRef('state', $state);
		$view->assignRef('zip', $zip);		
		$view->assignRef('phone', $phone);
		$view->assignRef('fax', $fax);
		$view->assignRef('email', $email);
		$view->assignRef('comment', $comment);
		$view->assignRef('paymentMethod', $paymentMethod);		
		$view->assignRef('lists', $lists);		
		$view->assignRef('Itemid', $Itemid);
		$view->assignRef('config', $config);								
		$view->assignRef('x_card_num', $x_card_num);
		$view->assignRef('x_card_code', $x_card_code);
		$view->assignRef('cardHolderName', $cardHolderName) ;						
		$view->assignRef('customField', $customField) ;				
		$view->assignRef('event', $event) ;			
		$view->assignRef('amount', $totalAmount) ;	
		$view->assignRef('methods', $methods) ;
		$view->assignRef('enableCoupon', $enableCoupon) ;
		$view->assignRef('couponCode', $couponCode) ;
		$view->assignRef('username', $username) ;
		$view->assignRef('password', $password) ;		
		$view->assignRef('errorCoupon', $errorCoupon) ;
		$view->assignRef('registrationErrorCode', $registrationErrorCode) ;
		$view->assignRef('userId', $userId) ;
		$view->assignRef('lists', $lists) ;
		$view->assignRef('idealEnabled', $idealEnabled) ;	
		$view->assignRef('numberFeeFields', $numberFeeFields) ;			
		ob_start();		
		$view->display() ;	
		$text = ob_get_contents() ;
		ob_end_clean();
		return $text ;					
	}	
}