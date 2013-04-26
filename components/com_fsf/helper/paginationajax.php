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
/**
 * @version		$Id: pagination.php 10707 2008-08-21 09:52:47Z eddieajau $
 * @package		Joomla.Framework
 * @subpackage	HTML
 * @copyright	Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();

jimport( 'joomla.html.pagination');
require_once (JPATH_SITE.DS.'components'.DS.'com_fsf'.DS.'helper'.DS.'paginationex.php');

/**
 * Pagination Class.  Provides a common interface for content pagination for the
 * Joomla! Framework
 *
 * @package 	Joomla.Framework
 * @subpackage	HTML
 * @since		1.5
 */
class JPaginationAjax extends JPaginationEx
{
	function _buildDataObject()
	{
		// Initialize variables
		$data = new stdClass();

		$data->all	= new JPaginationObject(JText::_("VIEW_ALL"));
		if (!$this->_viewall) {
			$data->all->base	= '0';
			$data->all->link	= JRoute::_("&limitstart=");
		}

		// Set the start and previous data objects
		$data->start	= new JPaginationObject(JText::_("START"));
		$data->previous	= new JPaginationObject(JText::_("PREV"));

		if ($this->get('pages.current') > 1)
		{
			$page = ($this->get('pages.current') -2) * $this->limit;

			$page = $page == 0 ? '' : $page; //set the empty for removal from route

			$data->start->base	= '0';
			$data->start->link	= "javascript:ChangePage(0);";
			$data->previous->base	= $page;
			$data->previous->link	= "javascript:ChangePage($page);";
		}

		// Set the next and end data objects
		$data->next	= new JPaginationObject(JText::_("NEXT"));
		$data->end	= new JPaginationObject(JText::_("END"));

		if ($this->get('pages.current') < $this->get('pages.total'))
		{
			$next = $this->get('pages.current') * $this->limit;
			$end  = ($this->get('pages.total') -1) * $this->limit;

			$data->next->base	= $next;
			$data->next->link	= "javascript:ChangePage($next);";
			$data->end->base	= $end;
			$data->end->link	= "javascript:ChangePage($end);";
		}

		$data->pages = array();
		$stop = $this->get('pages.stop');
		for ($i = $this->get('pages.start'); $i <= $stop; $i ++)
		{
			$offset = ($i -1) * $this->limit;

			$offset = $offset == 0 ? '' : $offset;  //set the empty for removal from route

			$data->pages[$i] = new JPaginationObject($i);
			if ($i != $this->get('pages.current') || $this->_viewall)
			{
				$data->pages[$i]->base	= $offset;
				$data->pages[$i]->link	= "javascript:ChangePage($offset);";
			}
		}
		return $data;
	}
	
	function getLimitBox()
	{
		$mainframe = JFactory::getApplication();

		// Initialize variables
		$limits = array ();

		// Make the option list
		for ($i = 5; $i <= 30; $i += 5) {
			$limits[] = JHTML::_('select.option', "$i");
		}
		$limits[] = JHTML::_('select.option', '50');
		$limits[] = JHTML::_('select.option', '100');
		$limits[] = JHTML::_('select.option', '0', JText::_("ALL"));

		$selected = $this->_viewall ? 0 : $this->limit;

		// Build the select list
		$html = JHTML::_('select.genericlist',  $limits, 'limit_base', 'class="inputbox" size="1" onchange="ChangePageCount(this.value)"', 'value', 'text', $selected);
		
		return $html;
	}
}

