<?php
/**
 * @version		1.0.0
 * @package		Joomla
 * @subpackage	Schedule Order
 * @author      Jason<jason@netwebx.com>
 * @copyright	Copyright (C) 2013 NetWebX.COM
 * @license		GNU/GPL, see LICENSE.php
 */
// no direct access
defined( '_JEXEC' ) or die('Restricted Access') ;

// import Joomla view library
jimport('joomla.application.component.view');

/**
 * HTML View class for Schedule Order component
 *
 * @static
 * @package		Joomla
 * @subpackage	Schedule Order
 * @since 1.0
 */
class ScheduleOrderViewCart extends JView
{
	/**
	 * Display interface to user
	 *
	 * @param string $tpl
	 */
	function display($tpl = null)
	{							
	    $this->setLayout('default') ;
		$Itemid = JRequest::getInt('Itemid');
		//$config = ScheduleOrderHelper::getConfig();				
		//Get category ID of the current event
		require_once JPATH_COMPONENT.'/helper/cart.php';
		$cart = new ScheduleCart() ;
		$productIds = $cart->getProducts();
		$this->assignRef('items', $productIds) ;
		//$this->assignRef('config', $config) ;
		$this->assignRef('Itemid', $Itemid) ;		
		parent::display($tpl) ;					
	}	
}