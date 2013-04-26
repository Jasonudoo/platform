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

class FSFCustFieldPlugin
{
	var $name = "Please select a plugin";
	
	function DisplaySettings($params) // passed object with settings in
	{
		return "There are no settings for this plugin";
	}
	
	function SaveSettings() // return object with settings in
	{
		return "";
	}
	
	function Input($current, $params, $context) // output the field for editing
	{
		return "";
	}
	
	function Save()
	{
		return "";
	}
	
	function Display($value, $params, $context) // output the field for display
	{
		return $value;
	}
	
	function CanEdit()
	{
		return false;	
	}
	
	function CanSearch()
	{
		return false;	
	}
}

class FSFCF
{	
	var $_ticketvalues = array();
	
	function &GetCustomFields($ticketid,$prod_id,$ticket_dept_id,$maxpermission = 3,$isopen = false)
	{
		$db =& JFactory::getDBO();

		if (!$ticketid) $ticketid = 0;
		if (!$prod_id) $prod_id = 0;
		if (!$ticket_dept_id) $ticket_dept_id = 0;
	
		// get a list of all available fields
		$qry = "SELECT * FROM #__fsf_field as f WHERE f.published = 1 AND f.ident = 0 AND ";
		$qry .= " (allprods = 1 OR '{$db->getEscaped($prod_id)}' IN (SELECT prod_id FROM #__fsf_field_prod WHERE field_id = f.id)) AND ";
		$qry .= " (alldepts = 1 OR '{$db->getEscaped($ticket_dept_id)}' IN (SELECT ticket_dept_id FROM #__fsf_field_dept WHERE field_id = f.id)) AND ";
		if ($isopen)
		{
			$qry .= " (f.permissions <= '{$db->getEscaped($maxpermission)}' OR f.permissions = 4) ";
		} else {
			$qry .= " f.permissions <= '{$db->getEscaped($maxpermission)}' ";
		}
	
		$qry .= " ORDER BY f.grouping, f.ordering ";
		$db->setQuery($qry);
		$rows = $db->loadAssocList("id");

		$indexes = array();

		if (count($rows) > 0)
		{
			foreach ($rows as $index => &$row)
			{
				$indexes[] = $db->getEscaped($index);
			} 
		}
	
		$indexlist = implode(",",$indexes);
		if (count($indexes) == 0)
			$indexlist = "0";
	
		$qry = "SELECT * FROM #__fsf_field_values WHERE field_id IN ($indexlist)";
		$db->setQuery($qry);
		$values = $db->loadAssocList();

		if (count($values) > 0)
		{
			foreach($values as &$value)
			{
				$field_id = $value['field_id'];
				$rows[$field_id]['values'][] = $value['value'];
			}
		}

		$this->_custfields = $rows;

		return $rows;
	}

	function &GetAllCustomFields($values = true)
	{
		//$values = true;
		$db =& JFactory::getDBO();
		
		if (empty($this->allfields))
		{
			// get a list of all available fields
			$qry = "SELECT * FROM #__fsf_field as f WHERE f.published = 1 AND f.ident = 0 ";
			$qry .= " ORDER BY f.grouping, f.ordering ";
			$db->setQuery($qry);
			$rows = $db->loadAssocList("id");
		
			$indexes = array();

			if (count($rows) > 0)
			{
				foreach ($rows as $index => &$row)
				{
					$indexes[] = $db->getEscaped($index);
				} 
			}

			if ($values)
			{
				$indexlist = implode(",",$indexes);
				if (count($indexes) == 0)
					$indexlist = "0";
	
				$qry = "SELECT * FROM #__fsf_field_values WHERE field_id IN ($indexlist)";
				$db->setQuery($qry);
				
				$values = $db->loadAssocList();

				if (count($values) > 0)
				{
					foreach($values as &$value)
					{
						$field_id = $value['field_id'];
						$rows[$field_id]['values'][] = $value['value'];
					}
				}

			}
			
			$this->allfields = $rows;
		}
		return $this->allfields;
	}
	
	function GetField($fieldid)
	{
		$db =& JFactory::getDBO();
		$qry = "SELECT * FROM #__fsf_field WHERE id = '{$db->getEscaped($fieldid)}'";
		$db->setQuery($qry);
		return $db->loadObject();
	}

	function FieldHeader(&$field, $showreq = false)
	{
		echo $field['description'];
		if ($showreq && $field['required'] == 1)
			echo " <font color='red'>*</font>";
		if ($field['peruser'])
			echo "<img src='". JURI::root( true ). "/components/com_fsf/assets/images/user.png' style='position:relative;top:4px;' title='Global Field'>";
	}

	function GetValues(&$field)
	{
		if ($field['type'] == "text" || $field['type'] == "area" || $field['type'] == "plugin")
		{
			if (!array_key_exists('values',$field))
				return array();
			
			$output = array();
			if ($field['type'] == "plugin")
			{
				$output['plugindata'] = "";
				$output['plugin'] = "";	
			}
			if (array_key_exists('values',$field) && count($field['values']) > 0)
			{
				foreach ($field['values'] as &$value)
				{
					$bits = explode("=",$value);
					if (count($bits) == 2)
					{
						$output[$bits[0]] = $bits[1];	
					}
				}
			}
		
			return $output;
		}
	
		if ($field['type'] == "radio" || $field['type'] == "combo")
		{
			if (!array_key_exists('values',$field))
				return array();
			return $field['values'];	
		}
	
		if ($field['type'] == "checkbox")
			return array();
	}

	function FieldInput(&$field,&$errors,$errortype="ticket",$context = array())
	{
		$output = "";
		
		$id = $field['id'];
		
		$userid = 0;
		if (array_key_exists('userid',$context))
			$userid = $context['userid'];
		$ticketid = 0;
		if (array_key_exists('ticketid',$context))
			$ticketid = $context['ticketid'];
		
		// if its a per user field, try to load the value
		$current = $field['default'];

		if ($field['peruser'] && $errortype == "ticket")
		{
			
			$uservalues = FSFCF::GetUserValues($userid, $ticketid);
			
			if (array_key_exists($field['id'],$uservalues))
			{
				$current = $uservalues[$field['id']]['value'];
			}
		}
		
		$current = JRequest::getVar("custom_$id",$current);
		
		if ($field['type'] == "text")
		{
			$aparams = FSFCF::GetValues($field);
			$text_max = $aparams['text_max'];
			$text_size = $aparams['text_size'];
			$output = "<input name='custom_$id' id='custom_$id' value=\"".FSF_Helper::escape($current)."\" maxlength='$text_max' size='$text_size'>\n";
		}
	
		if ($field['type'] == "radio")
		{
			$values = FSFCF::GetValues($field);
			$output = "";
			if (count($values) > 0)
			{
				foreach ($values as $value)
				{
					$output .= "<input type='radio' id='custom_$id' name='custom_$id' value=\"".FSF_Helper::escape($value)."\"";
					if ($value == $current) $output .= " checked";
					$output .= ">$value<br>\n";
				}	
			}
		} 
	
		if ($field['type'] == "combo")
		{
			$values = FSFCF::GetValues($field);
			$output = "<select name='custom_$id' id='custom_$id'>\n";
			$output .= "<option value=''>".JText::_("PLEASE_SELECT")."</option>\n";
			if (count($values) > 0)
			{
				foreach ($values as $value)
				{
					$output .= "<option value=\"".FSF_Helper::escape($value)."\"";
					if ($value == $current) $output .= " selected";
					$output .= ">$value</option>\n";
				}	
			}
			$output .= "</select>";
		}
	
		if ($field['type'] == "area")
		{
			$aparams = FSFCF::GetValues($field);
			$area_width = $aparams['area_width'];
			$area_height = $aparams['area_height'];
			$output = "<textarea name='custom_$id' id='custom_$id' cols='$area_width' rows='$area_height'>$current</textarea>\n";
		}
	
		if ($field['type'] == "checkbox")
		{	
			$output = "<input type='checkbox' name='custom_$id' id='custom_$id'";
			if ($current == "on") $output .= " checked";
			$output .= ">\n";
		}
		
		if ($field['type'] == "plugin")
		{
			$aparams = FSFCF::GetValues($field);
			$plugin = FSFCF::get_plugin($aparams['plugin']);
			
			$output = $plugin->Input($current, $aparams['plugindata'], $context);
		}
	
		$id = "custom_" .$field['id'];
		if (array_key_exists($id,$errors))
		{
			if ($errortype == "ticket")
			{
				$output .= '<div class="fsf_ticket_error" id="error_subject">' . $errors[$id] . '</div>';
			} else {
				$output .= '</td><td class="fsf_must_have_field">' . $errors[$id];
			}
		}
	
		return $output;
	}
	
	function get_plugin_from_row(&$row)
	{
		$db	= & JFactory::getDBO();
		
		$query = "SELECT value FROM #__fsf_field_values WHERE field_id = " . $db->getEscaped($row->id);
		$db->setQuery($query);
		$values = $db->loadResultArray();
	
		$plugin_name = '';
		$plugin_data = '';
		
		foreach ($values as $value)
		{
			$bits = explode("=",$value);
			if (count($bits == 2))
			{
				if ($bits[0] == "plugin")
					$plugin_name = $bits[1];
				if ($bits[0] == "plugindata")
					$plugin_data = $bits[1];
			}
		}
		
		return FSFCF::get_plugin($plugin_name);
	}	
		
	function get_plugin($name)
	{
		if ($name == "")
			return new FSFCustFieldPlugin();
		
		$name = preg_replace("/[^a-zA-Z0-9\s]/", "", $name);
		$name = strtolower($name);
		require_once(JPATH_SITE.DS.'components'.DS.'com_fsf'.DS.'plugins'.DS.'custfield'.DS.$name.".php");
		$classname = $name . "Plugin";
		$obj = new $classname();
		
		return $obj;	
	}
	
	function get_plugins()
	{
		$path = JPATH_SITE.DS.'components'.DS.'com_fsf'.DS.'plugins'.DS.'custfield'.DS;
		
		$plugins = array();
		
		$files = FSFCF::get_filenames($path);
		foreach ($files as $file)
		{
			if (strpos($file,".php") < 1) continue;
			
			$filename = $path . $file;
			$file = str_replace(".php","",$file);
			$class = $file . "Plugin";
			
			require_once($filename);

			$plugins[$file] = new $class();
				
		}
		
		return $plugins;
	}
	
	function get_filenames($source_dir, $include_path = FALSE, $_recursion = FALSE)
	{
		static $_filedata = array();

		if ($fp = @opendir($source_dir))
		{
			// reset the array and make sure $source_dir has a trailing slash on the initial call
			if ($_recursion === FALSE)
			{
				$_filedata = array();
				$source_dir = rtrim(realpath($source_dir), DS).DS;
			}

			while (FALSE !== ($file = readdir($fp)))
			{
				if (@is_dir($source_dir.$file) && strncmp($file, '.', 1) !== 0)
				{
					FSFCF::get_filenames($source_dir.$file.DS, $include_path, TRUE);
				}
				elseif (strncmp($file, '.', 1) !== 0)
				{
					$_filedata[] = ($include_path == TRUE) ? $source_dir.$file : $file;
				}
			}
			return $_filedata;
		}
		else
		{
			return FALSE;
		}
	}
		
	function ValidateFields(&$fields, &$errors)
	{
		$ok = true;
		foreach ($fields as &$field)
		{
			if ($field['required'] > 0)
			{
				$value = JRequest::getVar("custom_" . $field['id'],"");
				if ($value == "")
				{
					$errors["custom_" . $field['id']] = JText::sprintf("YOU_MUST_ENTER_A_VALUE_FOR",$field['description']);	
					$ok = false;
				}	
			}
		}
	
		return $ok;
	}

	function StoreFields(&$fields, $ticketid)
	{
		$allfields = FSFCF::GetAllCustomFields(false);
		$db =& JFactory::getDBO();
		$user = JFactory::getUser();
		$userid = $user->get('id');

		if (count($fields) > 0)
		{
			foreach ($fields as &$field)
			{
				// only place this is called is creating a new ticket, so dont overwrite any per user fields that have permissions > 0
				if (array_key_exists($field['id'],$allfields) && $allfields[$field['id']]['peruser'] && $allfields[$field['id']]['permissions'] > 0)
					continue;
					
				$value = JRequest::getVar("custom_" . $field['id'],"XX--XX--XX");
				
				if ($field['type'] == "plugin")
				{
					$aparams = FSFCF::GetValues($field);
					$plugin = FSFCF::get_plugin($aparams['plugin']);
					
					$value = $plugin->Save();
				}
				
				if ($value != "XX--XX--XX")
				{
					if (array_key_exists($field['id'],$allfields) && $allfields[$field['id']]['peruser'])
					{
						$qry = "REPLACE INTO #__fsf_ticket_user_field (user_id, field_id, value) VALUES ('{$db->getEscaped($userid)}','";
						$qry .= $db->getEscaped($field['id']) . "','";
						$qry .= $db->getEscaped($value) . "')";
						$db->setQuery($qry);
						$db->Query();
					} else {
						$qry = "REPLACE INTO #__fsf_ticket_field (ticket_id, field_id, value) VALUES ('{$db->getEscaped($ticketid)}','";
						$qry .= $db->getEscaped($field['id']) . "','";
						$qry .= $db->getEscaped($value) . "')";
						$db->setQuery($qry);
						$db->Query();
					}
				}	
			}
		}	
	}

	function StoreField($fieldid, $ticketid, $ticket)
	{
		$allfields = FSFCF::GetAllCustomFields(true);
		
		//print_p($allfields);
		$db =& JFactory::getDBO();
		$value = JRequest::getVar("custom_" . $fieldid,"");
	
		$field = $allfields[$fieldid];
		
		if ($field['type'] == "plugin")
		{
			$aparams = FSFCF::GetValues($field);
			$plugin = FSFCF::get_plugin($aparams['plugin']);
					
			$value = $plugin->Save();
		}
				
				
		if (array_key_exists($fieldid, $allfields) && $allfields[$fieldid]['peruser'])
		{
			$userid = $ticket['user_id'];

			$qry = "SELECT value FROM #__fsf_ticket_user_field WHERE user_id = '{$db->getEscaped($userid)}' AND field_id = '{$db->getEscaped($fieldid)}'";
			$db->setQuery($qry);
			$row = $db->loadObject();
			$qry = "REPLACE INTO #__fsf_ticket_user_field (user_id, field_id, value) VALUES ('{$db->getEscaped($userid)}','";
			$qry .= $db->getEscaped($fieldid). "','";
			$qry .= $db->getEscaped($value) . "')";
			$db->setQuery($qry);
			$db->Query();
			
		} else{
						
			$qry = "SELECT value FROM #__fsf_ticket_field WHERE ticket_id = '{$db->getEscaped($ticketid)}' AND field_id = '{$db->getEscaped($fieldid)}'";
			$db->setQuery($qry);
			$row = $db->loadObject();
			$qry = "REPLACE INTO #__fsf_ticket_field (ticket_id, field_id, value) VALUES ('{$db->getEscaped($ticketid)}','";
			$qry .= $db->getEscaped($fieldid). "','";
			$qry .= $db->getEscaped($value) . "')";
			$db->setQuery($qry);
			$db->Query();
		}
		if (!$row)
			return array("",$value);
			
		return array($row->value,$value);
	}

	function &GetUserValues($userid = 0,$ticketid = 0)
	{
		if (empty($this->user_values))
		{
			$db =& JFactory::getDBO();
			if ($userid < 1)
			{
				if (empty($this->ticket_user_id))
				{
					$qry = "SELECT user_id FROM #__fsf_ticket_ticket WHERE id = '{$db->getEscaped($ticketid)}'";
					$db->setQuery($qry);
					$row = $db->loadObject();
					$this->ticket_user_id = $row->user_id;	
				}
				
				$userid = $this->ticket_user_id;
			}
			
			$qry = "SELECT * FROM #__fsf_ticket_user_field WHERE user_id ='{$db->getEscaped($userid)}'";
			$db->setQuery($qry);
			$this->user_values = $db->loadAssocList('field_id');
		}
		
		return $this->user_values;
	}

	function &GetTicketValues($ticketid,$ticket)
	{
		if (empty($this->_ticketvalues))
			$this->_ticketvalues = array();
			
		if (!array_key_exists($ticketid,$this->_ticketvalues))
		{
			$allfields = FSFCF::GetAllCustomFields(true);
			
			$db =& JFactory::getDBO();
			$qry = "SELECT * FROM #__fsf_ticket_field WHERE ticket_id ='{$db->getEscaped($ticketid)}'";
			$db->setQuery($qry);
			$values = $db->loadAssocList('field_id');
		
			$values2 = FSFCF::GetUserValues($ticket['user_id'], $ticket['id']);
			
			foreach ($values2 as $id => $value)
			{
				if ($allfields[$id]['peruser'])
					$values[$id] = $value;
			}
			$this->_ticketvalues[$ticketid] = $values;
		}
		return $this->_ticketvalues[$ticketid];	
	}

	function FieldOutput(&$field,&$fieldvalues,$context /*$ticketid = 0, $userid = 0*/)
	{
		$value = "";
		if (count($fieldvalues) > 0)
		{
			foreach ($fieldvalues as $fieldvalue)
			{
				if ($fieldvalue['field_id'] == $field['id'])
				{
					$value = $fieldvalue['value'];
					break;	
				}	
			}
		}
		
		if ($field['type'] == "plugin")
		{
			$aparams = FSFCF::GetValues($field);
			$plugin = FSFCF::get_plugin($aparams['plugin']);
			$value = $plugin->Display($value, $aparams['plugindata'], $context);
		}
		
		if ($field['type'] == "area")
		{
			$value = str_replace("\n","<br />",$value);	
		}
	
		if ($field['type'] == "checkbox")
		{
			if ($value == "on")
				return "Yes";
			return "No";
		}
	
		return $value;
	}
	
	// stuff below here is specific for comments
	function &Comm_GetCustomFields($ident)
	{
		$db =& JFactory::getDBO();
	
		// get a list of all available fields
		if ($ident != -1)
		{
			$qry = "SELECT * FROM #__fsf_field as f WHERE f.published = 1 AND (f.ident = 999 OR f.ident = '{$db->getEscaped($ident)}') ";
		} else {
			$qry = "SELECT * FROM #__fsf_field as f WHERE f.published = 1 ";
		}
	
		$qry .= " ORDER BY f.ordering";
		$db->setQuery($qry);
		$rows = $db->loadAssocList("id");

		$indexes = array();

		if (count($rows) > 0)
		{
			foreach ($rows as $index => &$row)
			{
				$indexes[] = $db->getEscaped($index);
			} 
		}
	
		$indexlist = implode(",",$indexes);
		if (count($indexes) == 0)
			$indexlist = "0";
	
		$qry = "SELECT * FROM #__fsf_field_values WHERE field_id IN ($indexlist)";
		$db->setQuery($qry);
		$values = $db->loadAssocList();

		if (count($values) > 0)
		{
			foreach($values as &$value)
			{
				$field_id = $value['field_id'];
				$rows[$field_id]['values'][] = $value['value'];
			}
		}

		return $rows;
	}
	
	function Comm_StoreFields(&$fields)
	{
		$result = array();
		
		if (count($fields) > 0)
		{
			foreach ($fields as &$field)
			{
				$value = JRequest::getVar("custom_" . $field['id'],"XX--XX--XX");
				if ($value != "XX--XX--XX")
				{
					$result[$field['id']] = $value;
				}	
			}
		}
		
		return $result;
	}

}