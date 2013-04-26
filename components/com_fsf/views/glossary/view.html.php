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

// no direct access

defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view');
jimport('joomla.utilities.date');

class FsfViewGlossary extends JView
{
    function display($tpl = null)
    {
        $mainframe = JFactory::getApplication();
        
		JHTML::_('behavior.modal', 'a.modal');
		JHTML::_('behavior.mootools');
        $db =& JFactory::getDBO();

        $aparams = $mainframe->getPageParameters('com_fsf');
		$this->use_letter_bar = $aparams->get('use_letter_bar',0);
		
		if ($this->use_letter_bar)
		{
			$qry = "SELECT UPPER(SUBSTR(word,1,1)) as letter FROM #__fsf_glossary GROUP BY letter ORDER BY letter";
			$db->setQuery($qry);
			$this->letters = $db->loadObjectList();
			
			if (count($this->letters) == 0)
			{
				return parent::display("empty");	
			}
		}
				
		$this->curletter = "";
		
		// if we are showing on a per letter basis only
		if ($this->use_letter_bar == 2)
		{
			$this->curletter = JRequest::getVar('letter',$this->letters[0]->letter);	
		}
		
        $query = "SELECT * FROM #__fsf_glossary WHERE published = 1 ";
		if ($this->curletter)
		{
			$query .= " AND SUBSTR(word,1,1) = '{$db->getEscaped($this->curletter)}'";
		}
		$query .= " ORDER BY word";
        $db->setQuery($query);
        $this->rows = $db->loadObjectList();
  
        $pathway =& $mainframe->getPathway();
		if (FSF_Helper::NeedBaseBreadcrumb($pathway, array( 'view' => 'glossary' )))
			$pathway->addItem("Glossary");

		if (FSF_Settings::get('glossary_use_content_plugins'))
		{
			// apply plugins to article body
			$dispatcher	=& JDispatcher::getInstance();
			JPluginHelper::importPlugin('content');
			$art = new stdClass;
			if (FSF_Helper::Is16())
			{
				//$aparams = new JParameter(null);
			} else {
				$aparams = new stdClass();	
			}
			foreach ($this->rows as &$row)
			{
				if ($row->description)
				{
					$art->text = $row->description;
					if (FSF_Helper::Is16())
					{
						$results = $dispatcher->trigger('onContentPrepare', array ('com_content.article', & $art, null, 0));
					} else {
						$results = $dispatcher->trigger('onPrepareContent', array (& $art, & $aparams, 0));
					} 
					$row->description = $art->text;
				}
				if ($row->longdesc)
				{
					$art->text = $row->longdesc;
					if (FSF_Helper::Is16())
					{
						$results = $dispatcher->trigger('onContentPrepare', array ('com_content.article', & $art, null, 0));
					} else {
						$results = $dispatcher->trigger('onPrepareContent', array (& $art, & $aparams, 0));
					} 
					$row->longdesc = $art->text;
				}
			}
		}     
		   	
  		parent::display($tpl);
    }
}

