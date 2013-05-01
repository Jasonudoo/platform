<?php
/**
 * @version		1.0.0
 * @package		Joomla
 * @subpackage	Schedule Order
 * @author      Jason <jason@netwebx.com>
 * @copyright	Copyright (C) 2013 NetWebX.COM
 * @license		GNU/GPL, see LICENSE.php
 */
// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');
 
/**
 * ScheduleOrder Component Controller
 *
 * @package     Joomla
 * @subpackage  Schedule Order
 * @since       v1.0.0
 */
class ScheduleOrderController extends JController
{
	/**
	 * display task
	 *
	 * @return void
	 */
	function display($cachable = false)
	{
		// set default view if not set
		$input = JFactory::getApplication()->input;
		$input->set('view', $input->getCmd('view', 'ScheduleOrder'));
	
		// call parent behavior
		parent::display($cachable);
	}	
}