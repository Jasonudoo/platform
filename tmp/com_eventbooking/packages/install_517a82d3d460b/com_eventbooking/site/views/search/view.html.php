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
 * HTML View class for EventBooking component
 *
 * @static
 * @package		Joomla
 * @subpackage	Event Booking
 * @since 1.5
 */
class EventBookingViewSearch extends JViewLegacy
{	
	function display($tpl = null)
	{								
	    $this->setLayout('default') ;
		$mainframe = JFactory::getApplication() ;				
		$db = JFactory::getDBO() ;
		$nullDate = $db->getNullDate();
		$document = JFactory::getDocument();						
		$pathway = $mainframe->getPathway();																	
		$items = $this->get('Data');				
		$pagination = $this->get('Pagination');		
		$document->setTitle(JText::_('EB_SEARCH_RESULT'));					
		$config = EventBookingHelper::getConfig();		
		if ($config->process_plugin) {			
			for ($i = 0, $n = count($items) ; $i < $n ; $i++) {
				$item = & $items[$i] ;												
				$item->short_description = JHtml::_('content.prepare', $item->short_description); ; 	
			}				
		}
		$Itemid = JRequest::getInt('Itemid', 0) ;		
		$user = JFactory::getUser();
		$userId = $user->get('id');
		$aid = $user->get('aid');							
		$this->assignRef('userId', $userId) ;
		$this->assignRef('aid', $aid) ;
		$this->assignRef('items', $items) ;											
		$this->assignRef('pagination',	$pagination);
		$this->assignRef('Itemid', $Itemid) ;
		$this->assignRef('config', $config) ;		
		$this->assignRef('nullDate', $nullDate) ;		
		parent::display($tpl) ;									
	}	
}