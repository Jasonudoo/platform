<?php

require_once (JPATH_SITE.DS.'components'.DS.'com_fsf'.DS.'helper'.DS.'settings.php');

class FSFAdminHelper
{
	function PageSubTitle2($title,$usejtext = true)
	{
		if ($usejtext)
			$title = JText::_($title);
		
		return str_replace("$1",$title,FSF_Settings::get('display_h3'));
	}
	
	function IsFAQs()
	{
		if (JRequest::getVar('option') == "com_fsf")
			return true;
		return false;	
	}
	
	function IsTests()
	{
		if (JRequest::getVar('option') == "com_fst")
			return true;
		return false;	
	}
	
	function GetVersion($path = "")
	{
		
		global $fsj_version;
		if (empty($fsj_version))
		{
			if ($path == "") $path = JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_fsf';
			$file = $path.DS.'fsf.xml';
			
			if (!file_exists($file))
				return FSF_Settings::get('version');
			
			$xml = simplexml_load_file($file);
			
			$fsj_version = $xml->version;
		}

		if ($fsj_version == "[VERSION]")
			return FSF_Settings::get('version');
			
		return $fsj_version;
	}	

	function GetInstalledVersion()
	{
		return FSF_Settings::get('version');
	}
	
	function Is16()
	{
		global $fsjjversion;
		if (empty($fsjjversion))
		{
			$version = new JVersion;
			$fsjjversion = 1;
			if ($version->RELEASE == "1.5")
				$fsjjversion = 0;
		}
		return $fsjjversion;
	}

	function DoSubToolbar()
	{
		if (!FSF_Helper::Is16())
		{
			JToolBarHelper::divider();
			JToolBarHelper::help("help.php?help=admin-view-" . JRequest::getVar('view'),true);
			return;
		}

		JToolBarHelper::divider();
		JToolBarHelper::help("",false,"http://www.freestyle-joomla.com/comhelp/fsf/admin-view-" . JRequest::getVar('view'));

		$vName = JRequest::getCmd('view', 'fsfs');
			
		JSubMenuHelper::addEntry(
			JText::_('COM_FSF_OVERVIEW'),
			'index.php?option=com_fsf&view=fsfs',
			$vName == 'fsfs' || $vName == ""
			);
			
		JSubMenuHelper::addEntry(
			JText::_('COM_FSF_SETTINGS'),
			'index.php?option=com_fsf&view=settings',
			$vName == 'settings'
			);

		JSubMenuHelper::addEntry(
			JText::_('COM_FSF_TEMPLATES'),
			'index.php?option=com_fsf&view=templates',
			$vName == 'templates'
			);

//##NOT_TEST_START##
//

		JSubMenuHelper::addEntry(
			JText::_('COM_FSF_FAQS'),
			'index.php?option=com_fsf&view=faqs',
			$vName == 'faqs'
			);

		JSubMenuHelper::addEntry(
			JText::_('COM_FSF_FAQ_CATEGORIES'),
			'index.php?option=com_fsf&view=faqcats',
			$vName == 'faqcats'
			);

//
		
		JSubMenuHelper::addEntry(
			JText::_('COM_FSF_GLOSSARY'),
			'index.php?option=com_fsf&view=glossarys',
			$vName == 'glossarys'
			);

//

		JSubMenuHelper::addEntry(
			JText::_('COM_FSF_ADMIN'),
			'index.php?option=com_fsf&view=backup',
			$vName == 'backup'
			);

	}	
	
	
	function IncludeHelp($file)
	{
		$lang =& JFactory::getLanguage();
		$tag = $lang->getTag();
		
		$path = JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_fsf'.DS.'help'.DS.$tag.DS.$file;
		if (file_exists($path))
			return file_get_contents($path);
		
		$path = JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_fsf'.DS.'help'.DS.'en-GB'.DS.$file;
		
		return file_get_contents($path);
	}
}