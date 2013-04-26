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
 * EventBooking Component Up-coming events Model
 *
 * @package		Joomla
 * @subpackage	EventBooking
 * @since 1.5
 */
class EventBookingModelUPcomingevents extends JModelLegacy
{				
	/**
	 * Events data array
	 *
	 * @var array
	 */
	var $_data = null;	
	/**
	 * Constructor
	 *
	 * @since 1.5
	 */
	function __construct()
	{
		parent::__construct();		
		$mainframe = JFactory::getApplication() ;		
		$listLength = EventBookingHelper::getConfigValue('number_upcoming_events');				
		if (!$listLength)
			$listLength = $mainframe->getCfg('list_limit');
		$limit		= $mainframe->getUserStateFromRequest( 'global.list.limit', 'limit', $listLength, 'int' );					
		$limitstart = JRequest::getVar('limitstart', 0 );		
		// In case limit has been changed, adjust limitstart accordingly
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);

		$this->setState('limit', $limit);		
		$this->setState('limitstart', $limitstart);
	}

	/**
	 * Method to get Events data
	 *
	 * @access public
	 * @return array
	 */
	function getData()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_data))
		{
			$db = $this->getDbo();
		    $config = EventBookingHelper::getConfig() ;		    
		    $user = JFactory::getUser();
		    $nullDate = $db->getNullDate() ;
			$query = $this->_buildQuery();						
			$db->setQuery($query);						
			$rows = $this->_getList($query, $this->getState('limitstart'), $this->getState('limit'));					
			if ($user->get('id')) {
				$userId = $user->get('id');				
				for ($i = 0 , $n = count($rows) ; $i < $n ; $i++) {
					$row = $rows[$i] ;
					$sql = 'SELECT COUNT(id) FROM #__eb_registrants WHERE user_id='.$userId.' AND event_id='.$row->id.' AND (published=1 OR (payment_method LIKE "os_offline%" AND published != 2))';
					$db->setQuery($sql) ;
					$row->user_registered = $db->loadResult() ;					
					//Canculate discount price					
					if ($config->show_discounted_price) {
					    $discount = 0 ;
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
            			$row->discounted_price = $row->individual_price - $discount ;          			
					}										
				}				
			} else {
			    //Calculate discounted price
			    if ($config->show_discounted_price) {			        
    			    for ($i = 0 , $n = count($rows) ; $i < $n ; $i++) {
    					$row = $rows[$i] ;    									    					
    					if ($config->show_discounted_price) {
    					    $discount = 0 ;
    					    if (($row->early_bird_discount_date != $nullDate) && ($row->date_diff >=0)) {
                		        if ($row->early_bird_discount_type == 1) {            		                        		           
                					$discount += $row->individual_price*$row->early_bird_discount_amount/100 ;						
                				} else {            				    
                					$discount += $row->early_bird_discount_amount ;
                				}            				
    					    }    					                    			
                			$row->discounted_price = $row->individual_price - $discount ;            			
    					}										
    				}			        			        			        			        			       
			    }				
			}
			
			$this->_data = $rows ;						
		}
		
		return $this->_data;
	}	
	/**
	 * Get total Events 
	 *
	 * @return int
	 */
	function getTotal()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_total))
		{
			$db = $this->getDbo();
			$where = $this->_buildContentWhere();
			$sql  = 'SELECT COUNT(*) FROM #__eb_events AS a '.$where;
			$db->setQuery($sql);
			$this->_total = $db->loadResult();
		}		
		return $this->_total;
	}
	/**
	 * Method to get a pagination object for the donors
	 *
	 * @access public
	 * @return integer
	 */
	function getPagination()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_pagination))
		{
			jimport('joomla.html.pagination');
			$this->_pagination = new JPagination( $this->getTotal(), $this->getState('limitstart'), $this->getState('limit') );
		}
		return $this->_pagination;
	}
	/**
	 * Build the select clause
	 *
	 * @return string
	 */
	function _buildQuery()
	{		
		// Get the WHERE and ORDER BY clauses for the query
		$where		= $this->_buildContentWhere();
		$orderby	= $this->_buildContentOrderBy();
		$query = 'SELECT a.*, DATEDIFF(a.early_bird_discount_date, NOW()) AS date_diff, c.name AS location_name, IFNULL(SUM(b.number_registrants), 0) AS total_registrants FROM  #__eb_events AS a '
			. ' LEFT JOIN #__eb_registrants AS b '
			. ' ON (a.id = b.event_id AND b.group_id=0 AND (b.published = 1 OR (b.payment_method LIKE "os_offline%" AND b.published != 2))) '
			. ' LEFT JOIN #__eb_locations AS c '
			. ' ON a.location_id = c.id '					
			. $where		
			. ' GROUP BY a.id '
			. $orderby
		;
		return $query;
	}
	/**
	 * Build order by clause for the select command
	 *
	 * @return string order by clause
	 */
	function _buildContentOrderBy()
	{							
		return ' ORDER BY a.event_date ';									
	}
	/**
	 * Build the where clause
	 *
	 * @return string
	 */
	function _buildContentWhere()
	{
		$app = JFactory::getApplication() ;
		$db = $this->getDbo();
		$user = JFactory::getUser() ;				
		$where = array() ;
		$categoryId = JRequest::getInt('category_id', 0) ;
		$where[] = 'a.published = 1';
		$where[] = ' a.access IN ('.implode(',', $user->getAuthorisedViewLevels()).')' ;				
		if ($categoryId) {								
			$where[] = ' a.id IN (SELECT event_id FROM #__eb_event_categories WHERE category_id='.$categoryId.')' ;
		}		
		$where[] = ' DATE(a.event_date) >= CURDATE() ';				
		if ($app->getLanguageFilter()) {
			$where[] = 'a.language IN (' . $db->Quote(JFactory::getLanguage()->getTag()) . ',' . $db->Quote('*') . ')';
		}
		$where 		= ( count( $where ) ? ' WHERE '. implode( ' AND ', $where ) : '' );
					
		return $where;
	}
	/**
	 * Get list of category
	 *
	 */
	function getCategory() {
		$db = $this->getDbo();
		$categoryId = JRequest::getInt('category_id', 0) ;
		$sql = 'SELECT * FROM #__eb_categories WHERE id='.$categoryId;
		$db->setQuery($sql) ;
		return $db->loadObject();			
	}					
} 