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

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

// Include the syndicate functions only once
require_once( JPATH_SITE.DS.'components'.DS.'com_fsf'.DS.'helper'.DS.'helper.php' );

$css = JRoute::_( "index.php?option=com_fsf&view=css&layout=default" );
$document =& JFactory::getDocument();
$document->addStyleSheet($css); 

$listtype = $params->get('listtype');

$db =& JFactory::getDBO();

if ($listtype == 'faqcat')
{
	$query = "SELECT * FROM #__fsf_faq_cat WHERE published = 1 ORDER BY ordering";

	$db->setQuery($query);
	$rows = $db->loadAssocList();
	
	require( JModuleHelper::getLayoutPath( 'mod_fsf_catprods', 'faqcat' ) );
} else if ($listtype == 'kbprod')
{
	$query = "SELECT * FROM #__fsf_prod WHERE published = 1 AND inkb = 1 ORDER BY ordering";

	$db->setQuery($query);
	$rows = $db->loadAssocList();
		
	require( JModuleHelper::getLayoutPath( 'mod_fsf_catprods', 'kbprod' ) );	
} else if ($listtype == 'kbcats')
{
	
	$prodid = $params->get('prodid');

	if ($prodid == -1)
		$prodid = JRequest::getVar('prodid');


	if ($prodid > 0)
	{
		$qry1 = "SELECT a.kb_cat_id FROM #__fsf_kb_art as a LEFT JOIN #__fsf_kb_art_prod as p ON a.id = p.kb_art_id WHERE p.prod_id = '" . $db->getEscaped($prodid) . "' AND published = 1 GROUP BY a.kb_cat_id";
		$qry2 = "SELECT a.kb_cat_id FROM #__fsf_kb_art as a WHERE a.allprods = '1' AND published = 1 GROUP BY a.kb_cat_id";
			
		$query = "($qry1) UNION ($qry2)";
		$db->setQuery($query);
			
		$rows = $db->loadAssocList('kb_cat_id');
		$catids = array();
		foreach($rows as &$rows)
		{
			$catids[$rows['kb_cat_id']] = $rows['kb_cat_id'];
		}

		$query = "SELECT parcatid FROM #__fsf_kb_cat WHERE id IN (".implode(", ",$catids).") AND parcatid > 0";
		$db->setQuery($query);
		$rows = $db->loadAssocList();
		foreach($rows as &$rows)
		{
			$catids[$rows['parcatid']] = $rows['parcatid'];
		}

		$query = "SELECT * FROM #__fsf_kb_cat WHERE published = 1 AND id IN (".implode(", ",$catids) . ") AND parcatid = 0 ORDER BY ordering";
	} else {
		$query = "SELECT * FROM #__fsf_kb_cat WHERE published = 1 AND parcatid = 0 ORDER BY ordering";
	}

	$db->setQuery($query);
	$rows = $db->loadAssocList('id');

	require( JModuleHelper::getLayoutPath( 'mod_fsf_catprods', 'kbcats' ) );	
} else {
	$module->showtitle = 0;
	$attribs['style'] = "hide_me";
	
}
