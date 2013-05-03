<?php
/**
 * @version		1.0.0
 * @package		Joomla
 * @subpackage	Event Booking
 * @author      Jason<jason@netwebx.com>
 * @copyright	Copyright (C) 2010 Ossolution Team
 * @license		GNU/GPL, see LICENSE.php
 */
// no direct access
defined('_JEXEC') or die('Restrict Access');

class com_scheduleorderInstallerScript
{
	/**
	 * Method to run before installing the component
	 */
	function preflight($type, $parent)
	{
		//Backup the old language file
		/*
		foreach (self::$languageFiles as $languageFile) {
			if (JFile::exists(JPATH_ROOT.'/language/en-GB/'.$languageFile)) {
				JFile::copy(JPATH_ROOT.'/language/en-GB/'.$languageFile, JPATH_ROOT.'/language/en-GB/bak.'.$languageFile);
			}
		}*/
	}
	/**
	 * method to install the component
	 *
	 * @return void
	 */
	function install($parent)
	{
		//$this->_updateDatabaseSchema() ;
	}
	
	function update($parent)
	{
		//$this->_updateDatabaseSchema() ;
	}	
}
