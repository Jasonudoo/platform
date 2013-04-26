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

function print_p($var)
{
	echo "<pre>";
	print_r($var);
	echo "</pre>";	
}

// Require the base controller
 
require_once( JPATH_COMPONENT.DS.'controller.php' );
require_once( JPATH_COMPONENT.DS.'helper'.DS.'helper.php' );
require_once( JPATH_COMPONENT.DS.'helper'.DS.'settings.php' );

// Require specific controller if requested
if($controller = JRequest::getWord('controller')) {
    $path = JPATH_COMPONENT.DS.'controllers'.DS.$controller.'.php';
    if (file_exists($path)) {
        require_once $path;
    } else {
        $controller = '';
    }
}

// Create the controller
$classname    = 'FsfController'.$controller;
$controller   = new $classname( );

$css = JRoute::_( "index.php?option=com_fsf&view=css&layout=default" );
$document =& JFactory::getDocument();
$document->addStyleSheet($css); 

if (!JFactory::getApplication()->get('jquery')) {
	JFactory::getApplication()->set('jquery', true);
	$document->addScript( JURI::base().'/components/com_fsf/assets/js/jquery.1.6.2.min.js' );
}
$document->addScript( JURI::base().'/components/com_fsf/assets/js/main.js' );
// Perform the Request task
$task = JRequest::getVar( 'task' );
if ($task == "captcha_image")
{
	ob_clean();
	require_once (JPATH_SITE.DS.'components'.DS.'com_fsf'.DS.'helper'.DS.'captcha.php');
	$cap = new FSF_Captcha();
	$cap->GetImage();
	exit;
} else {
	$controller->execute( $task );

	// Redirect if set by the controller
	$controller->redirect();
}
?>
