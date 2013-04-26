<?php
/**
 * @version		1.5.0
 * @package		Joomla
 * @subpackage	Event Booking
 * @author  Tuan Pham Ngoc
 * @copyright	Copyright (C) 2010 Ossolution Team
 * @license		GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die;
class plgSystemEBReminder extends JPlugin
{
	public static $running ;
	function onAfterInitialise()
	{		
		jimport('joomla.filesystem.file');
		if (JFile::exists(JPATH_ROOT.'/components/com_eventbooking/eventbooking.php') && !self::$running) {
			self::$running = true ;			
			$lastRun = (int) $this->params->get('last_run', 0);
			$numberEmailSendEachTime = (int) $this->params->get('number_registrants', 0);
			$currentTime = time() ;
			$numberMinutes = ($currentTime - $lastRun)/60 ;
			//This plugin win runs in each 10 minutes
			if ($numberMinutes >= 30) {
				require_once JPATH_ROOT.'/components/com_eventbooking/helper/helper.php' ;
				require_once JPATH_ROOT.'/components/com_eventbooking/models/reminder.php' ;
				EventBookingModelReminder::sendReminder($numberEmailSendEachTime);
				$db = & JFactory::getDbo() ;
				//Store last run time
				$this->params->set('last_run', $currentTime);
				$params = $this->params->toString();
				$sql = 'SELECT extension_id FROM #__extensions WHERE element="ebreminder" AND `folder`="system"';
				$db->setQuery($sql) ;
				$pluginId = $db->loadResult() ;
				$sql = 'UPDATE #__extensions SET 	params='.$db->quote($params).' WHERE extension_id='.$pluginId ;
				$db->setQuery($sql);
				$db->query();				
			}	
			self::$running = false;
		}				
		return true ;		
	}
}
