<?php
/**
* @Copyright Freestyle Joomla (C) 2010
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*     
* This file is part of Freestyle FAQs
* This program is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation, either version 3 of the License, or
* (at your option) any later version.
* 
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
* 
* You should have received a copy of the GNU General Public License
* along with this program.  If not, see <http://www.gnu.org/licenses/>.
**/
?>
<?php
// No direct access

defined( '_JEXEC' ) or die( 'Restricted access' );

// Require the base controller

require_once( JPATH_COMPONENT.DS.'controller.php' );
require_once( JPATH_COMPONENT.DS.'settings.php' );
require_once( JPATH_SITE.DS.'components'.DS.'com_fsf'.DS.'helper'.DS.'helper.php' );
require_once( JPATH_COMPONENT.DS.'adminhelper.php' );

// Require specific controller if requested
if($controller = JRequest::getWord('controller')) {
	$path = JPATH_COMPONENT.DS.'controllers'.DS.$controller.'.php';
	if (file_exists($path)) {
		require_once $path;
	} else {
		$controller = '';
	}
}

function print_p($var)
{
	echo "<pre>";
	print_r($var);
	echo "</pre>";	
}
	
// do version check
$ver_inst = FSFAdminHelper::GetInstalledVersion();
$ver_files = FSFAdminHelper::GetVersion();
	
	
// if bad version display warning message
if ($ver_files != $ver_inst)
{
	$task = JRequest::getVar('task');	
	$view = JRequest::getVar('view');

	if ($task != "update" || $view != "backup")
		JError::raiseWarning( 100, JText::sprintf('INCORRECT_VERSION',JRoute::_('index.php?option=com_fsf&view=backup&task=update')) );
	
	if ($view != "" && $view != "backup")
		JRequest::setVar('view','');
}
// if bad version and controller is not fsfs dont display
	
	
// Create the controller
$controllername = $controller;
$classname    = 'FsfsController'.$controller;
$controller   = new $classname( );

$css = JRoute::_( "../index.php?option=com_fsf&view=css" );
$document =& JFactory::getDocument();
$document->addStyleSheet($css); 
$document->addStyleSheet(JURI::root().'administrator/components/com_fsf/assets/css/main.css'); 

//if (!JFactory::getApplication()->get('jquery')) {
//	JFactory::getApplication()->set('jquery', true);
	$document->addScript( JURI::root().'/components/com_fsf/assets/js/jquery.1.6.2.min.js' );
//}

$document->addScript( JURI::root().'/components/com_fsf/assets/js/main.js' );



// Perform the Request task
$controller->execute( JRequest::getVar( 'task' ) );

// Redirect if set by the controller
$controller->redirect();
