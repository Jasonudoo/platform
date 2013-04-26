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
 * HTML View class for Event Booking component
 *
 * @static
 * @package		Joomla
 * @subpackage	Event Booking
 * @since 1.5
 */
class EventBookingViewLocationlist extends JViewLegacy
{
	function display($tpl = null)
	{				
		$user = JFactory::getUser() ;	
		if (!$user->authorise('eventbooking.addlocation', 'com_eventbooking')) {
		    JFactory::getApplication()->redirect('index.php', JText::_("EB_NO_PERMISSION")) ;
		    return ;
		}											
		$Itemid = JRequest::getInt('Itemid', 0) ;
		$items		= $this->get( 'Data');				
		$config = EventBookingHelper::getConfig();								
		$pagination = $this->get( 'Pagination' );	
		$this->assignRef('config', $config) ;		
		$this->assignRef('items',		$items);
		$this->assignRef('Itemid', $Itemid) ;
		$this->assignRef('pagination',	$pagination);
						
		parent::display($tpl);				
	}	
}