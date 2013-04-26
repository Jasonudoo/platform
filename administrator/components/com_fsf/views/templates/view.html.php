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

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

jimport( 'joomla.application.component.view' );
require_once (JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_fsf'.DS.'settings.php');
require_once (JPATH_SITE.DS.'components'.DS.'com_fsf'.DS.'helper'.DS.'parser.php');
require_once (JPATH_SITE.DS.'components'.DS.'com_fsf'.DS.'helper'.DS.'fields.php');


class FsfsViewTemplates extends JView
{
	
	function display($tpl = null)
	{
		JHTML::_('behavior.modal');
		
		if (JRequest::getVar('task') == "cancellist")
		{
			$mainframe = JFactory::getApplication();
			$link = JRoute::_('index.php?option=com_fsf&view=fsfs',false);
			$mainframe->redirect($link);
			return;			
		}

		$what = JRequest::getString('what','');
		
		$settings = FSF_Settings::GetAllSettings();
		$db	= & JFactory::getDBO();
		
		if ($what == "testref")
		{
			return $this->TestRef();
		} else if ($what == "save")
		{

			$large = FSF_Settings::GetLargeList();
			$templates = FSF_Settings::GetTemplateList();
			$intpltable = FSF_Settings::StoreInTemplateTable();
			
			// save support custom setting
			$head = JRequest::getVar('support_list_head', '', 'post', 'string', JREQUEST_ALLOWRAW);
			$row = JRequest::getVar('support_list_row', '', 'post', 'string', JREQUEST_ALLOWRAW);

			$qry = "REPLACE INTO #__fsf_templates (template, tpltype, value) VALUES ('custom', 1, '" . $db->getEscaped($head) . "')";
			$db->setQuery($qry);$db->Query();
			$qry = "REPLACE INTO #__fsf_templates (template, tpltype, value) VALUES ('custom', 0, '" . $db->getEscaped($row) . "')";
			$db->setQuery($qry);$db->Query();

			unset($_POST['support_list_head']);
			unset($_POST['support_list_row']);
					
			// save templates
			$intpltable = FSF_Settings::StoreInTemplateTable();
			foreach($intpltable as $template)
			{
				$value = JRequest::getVar($template, '', 'post', 'string', JREQUEST_ALLOWRAW);
				$qry = "REPLACE INTO #__fsf_templates (template, tpltype, value) VALUES ('{$db->getEscaped($template)}', 2, '{$db->getEscaped($value)}')";
				$db->setQuery($qry);
				$db->Query();
			}
		
			// large settings
			foreach($large as $setting)
			{
				if (!array_key_exists($setting,$templates))
					continue;
	
				$value = JRequest::getVar($setting, '', 'post', 'string', JREQUEST_ALLOWRAW);
				$qry = "REPLACE INTO #__fsf_settings_big (setting, value) VALUES ('";
				$qry .= $db->getEscaped($setting) . "','";
				$qry .= $db->getEscaped($value) . "')";
				//echo $qry."<br>";
				$db->setQuery($qry);$db->Query();

				$qry = "DELETE FROM #__fsf_settings WHERE setting = '{$db->getEscaped($setting)}'";
				//echo $qry."<br>";
				$db->setQuery($qry);$db->Query();

				unset($_POST[$setting]);
			}		
			
			
			
			$data = JRequest::get('POST',JREQUEST_ALLOWRAW);

			foreach ($data as $setting => $value)
				if (array_key_exists($setting,$settings))
					$settings[$setting] = $value;
			
			foreach ($settings as $setting => $value)
			{
				if (!array_key_exists($setting,$data))
				{
					$settings[$setting] = 0;
					$value = 0;	
				}
				
				if (!array_key_exists($setting,$templates))
					continue;

				if (array_key_exists($setting,$large))
					continue;

				$qry = "REPLACE INTO #__fsf_settings (setting, value) VALUES ('";
				$qry .= $db->getEscaped($setting) . "','";
				$qry .= $db->getEscaped($value) . "')";
				$db->setQuery($qry);$db->Query();
				//echo $qry."<br>";
			}

			//exit;
			$link = 'index.php?option=com_fsf&view=templates';
			
			if (JRequest::getVar('task') == "save")
				$link = 'index.php?option=com_fsf';

			$mainframe = JFactory::getApplication();
			$mainframe->redirect($link, JText::_("Settings_Saved"));		
			exit;
		} else if ($what == "customtemplate") {
			$this->CustomTemplate();
			exit;	
		} else {
			
			// load other templates
			$intpltable = FSF_Settings::StoreInTemplateTable();
			foreach($intpltable as $template)
			{
				$settings[$template] = '';
				$settings[$template. '_default'] = '';
			}
			$tpllist = "'" . implode("', '", $intpltable) . "'";
			$qry = "SELECT * FROM #__fsf_templates WHERE template IN ($tpllist)";
			$db->setQuery($qry);
			$rows = $db->loadAssocList();
			if (count($rows) > 0)
			{	
				foreach ($rows as $row)
				{
					if ($row['tpltype'] == 2)
					{
						$settings[$row['template']] = $row['value'];
					} else if ($row['tpltype'] == 3) {
						$settings[$row['template'] . '_default'] = $row['value'];
					}
				}
			}

			
			// load ticket template stuff
			$qry = "SELECT * FROM #__fsf_templates WHERE template = 'custom'";
			$db->setQuery($qry);
			$rows = $db->loadAssocList();
			if (count($rows) > 0)
			{	
				foreach ($rows as $row)
				{
					if ($row['tpltype'] == 1)
					{
						$settings['support_list_head'] = $row['value'];
					} else if ($row['tpltype'] == 0) {
						$settings['support_list_row'] = $row['value'];
					}
				}
			} else {
				$settings['support_list_head'] = '';
				$settings['support_list_row'] = '';
			}
		
			$qry = "SELECT * FROM #__fsf_templates WHERE tpltype = 2";
			$db->setQuery($qry);
			$rows = $db->loadAssocList();
			if (count($rows) > 0)
			{	
				foreach ($rows as $row)
				{
					$settings[$row['template']] = $row['value'];
				}
			}

			$document =& JFactory::getDocument();
			$document->addStyleSheet(JURI::root().'administrator/components/com_fsf/assets/css/js_color_picker_v2.css'); 
			$document->addScript(JURI::root().'administrator/components/com_fsf/assets/js/color_functions.js'); 
			$document->addScript(JURI::root().'administrator/components/com_fsf/assets/js/js_color_picker_v2.js'); 

			$this->assignRef('settings',$settings);

			JToolBarHelper::title( JText::_("FREESTYLE_FAQS") .' - '. JText::_("SETTINGS") , 'fsf_templates' );
			JToolBarHelper::apply();
			JToolBarHelper::save();
			JToolBarHelper::cancel('cancellist');
			FSFAdminHelper::DoSubToolbar();
			parent::display($tpl);
		}
	}

	function ParseParams(&$aparams)
	{
		$out = array();
		$bits = explode(";",$aparams);
		foreach ($bits as $bit)
		{
			if (trim($bit) == "") continue;
			$res = explode(":",$bit,2);
			if (count($res) == 2)
			{
				$out[$res[0]] = $res[1];	
			}
		}
		return $out;	
	}

	function CustomTemplate()
	{
		$template = JRequest::getVar('name');
		$db	= & JFactory::getDBO();
		$qry = "SELECT * FROM #__fsf_templates WHERE template = '" . $db->getEscaped($template) . "'";
		$db->setQuery($qry);
		$rows = $db->loadAssocList();
		$output = array();
		foreach ($rows as $row)
		{
			if ($row['tpltype'])
			{
				$output['head'] = $row['value'];
			} else {
				$output['row'] = $row['value'];
			}
		}
		echo json_encode($output);
		exit;	
	}

	function TestRef()
	{
		$format = JRequest::getVar('ref');
		
		$ref = FSF_Helper::createRef(1234,$format);
		echo $ref;
		exit;	
	}
}


