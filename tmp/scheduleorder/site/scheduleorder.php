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
defined('_JEXEC') or die('Restricted Access');
//Require the controller
error_reporting(0);
if (!defined('COMPONENT_NAME')) {
    define('COMPONENT_NAME', 'com_scheduleorder');
}
//Import Joomla Controller Library
jimport('joomla.application.component.controller');
//Get an instance of the controller prefixed by ScheduleOrder
$controller = JController::getInstance('ScheduleOrder');

// Perform the Request task
$input = JFactory::getApplication()->input;
$controller->execute(JRequest::getCmd('task'));

//Redirect if set by the controller
$controller->redirect();
