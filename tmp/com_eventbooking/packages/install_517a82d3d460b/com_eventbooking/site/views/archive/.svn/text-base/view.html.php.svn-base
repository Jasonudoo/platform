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
class EventBookingViewArchive extends JViewLegacy
{	
	function display($tpl = null)
	{		
	    $this->setLayout('default') ;						
		$mainframe = JFactory::getApplication() ;				
		$db = JFactory::getDBO() ;
		$nullDate = $db->getNullDate();
		$document = JFactory::getDocument();						
		$pathway = $mainframe->getPathway();				
		$categoryId = JRequest::getInt('category_id', 0) ;
		if (!$categoryId) {
			$menus = JSite::getMenu();
			$menu = $menus->getActive();
			if (is_object($menu)) {
				$params = new JRegistry() ;
				$params->loadString($menu->params) ;				
				$categoryId = $params->get('category_id', 0);
				if ($categoryId) {
					JRequest::setVar('category_id', $categoryId);
				}
			}
		}															
		$items = $this->get('Data');				
		$pagination = & $this->get('Pagination');		
		$document->setTitle(JText::_('EB_EVENTS_ARCHIVE'));					
		$config = EventBookingHelper::getConfig();				
		if ($config->process_plugin) {		    
			for ($i = 0, $n = count($items) ; $i < $n ; $i++) {			    
				$item = & $items[$i] ;	
				$item->short_description = JHtml::_('content.prepare', $item->short_description);			    				 	
			}				
		}
		$Itemid = JRequest::getInt('Itemid', 0) ;													
		$this->assignRef('items', $items) ;											
		$this->assignRef('pagination',	$pagination);
		$this->assignRef('Itemid', $Itemid) ;
		$this->assignRef('config', $config) ;		
		$this->assignRef('nullDate', $nullDate) ;		
		parent::display($tpl) ;									
	}	
}