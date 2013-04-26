<?php
/**
 * @version		1.5.0
 * @package		Joomla
 * @subpackage	Event Booking
 * @author  Tuan Pham Ngoc
 * @copyright	Copyright (C) 2010 Ossolution Team
 * @license		GNU/GPL, see LICENSE.php
 */
defined( '_JEXEC' ) or die( 'Restricted access' );
class plgEventBookingAcyMailing extends JPlugin
{	
	public function __construct(& $subject, $config)
	{
		parent::__construct($subject, $config);		
	}
	
	public function onAfterStoreRegistrant($row) {		
		$db = JFactory::getDBO() ;
		$params = $this->params ;			
		$sql = "SELECT subid FROM #__acymailing_subscriber WHERE email='$row->email'";
		$db->setQuery($sql) ;
		$subId = $db->loadResult();		
		$time = time() ;
		if (!$subId) {
			$name = $row->first_name . ' ' . $row->last_name ;
			$ip = @$_SERVER['REMOTE_ADDR'] ;
			$user = JFactory::getUser() ;
			$userId = $user->get('id');
			$sql = "INSERT INTO #__acymailing_subscriber(email, userid, name, created, 	confirmed, ip)
			VALUES('$row->email', '$userId', '$name', $time, 1, '$ip')
			";
			$db->setQuery($sql) ;
			$db->query();
			$subId = $db->insertId();
		} 										
		//Insert subscriber into list
		$listIds = trim($params->get('list_ids', ''));
		if ($listIds) {
			$sql = 'SELECT listid FROM #__acymailing_list WHERE listid IN('.$listIds.') AND published=1';				
		} else {
			$sql = 'SELECT listid FROM #__acymailing_list WHERE listid IN('.$listIds.')';
		}
		$db->setQuery($sql) ;
		$rows = $db->loadObjectList();
		if (count($rows)) {
			foreach ($rows as $row) {
				$listId = $row->listid ;
				//Check to see if users has subscribed for this list
				$sql = 'SELECT COUNT(*) FROM #__acymailing_listsub WHERE listid='.$listId.' AND subid='.$subId ;
				$db->setQuery($sql) ;
				$total = $db->loadResult();
				if (!$total) {
					$sql = "INSERT INTO #__acymailing_listsub(listid, subid, subdate, unsubdate, `status`) VALUES($listId, $subId, $time, NULL, 1)";
					$db->setQuery($sql) ;
					$db->query();			
				}
			}
		}							
	} 	
}	