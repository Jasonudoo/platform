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
defined('_JEXEC') or die();

//Require the controller
error_reporting(0);
define('EB_AFFILIATE', 0);
//Basic ACL support
if (!JFactory::getUser()->authorise('core.manage', 'com_eventbooking'))
{
	return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}

require_once JPATH_COMPONENT . '/controller.php';
require_once JPATH_ROOT . '/components/com_eventbooking/helper/helper.php';
require_once JPATH_ROOT . '/components/com_eventbooking/helper/fields.php';
require_once JPATH_ROOT . '/components/com_eventbooking/payments/os_payments.php';
require_once JPATH_ROOT . '/components/com_eventbooking/payments/os_payment.php';
//Init the controller
$controller = new EventBookingController();
// Perform the Request task
$controller->execute(JRequest::getCmd('task'));
$controller->redirect();
?>
