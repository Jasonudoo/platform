<?php
/**
 * @version		$Id: menu.php 21450 2011-06-04 18:56:32Z dextercowley $
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

/**
 * JMenu class
 *
 * @package		Joomla.Site
 * @subpackage	Application
 * @since		1.5
 */
class JMenuSite extends JMenu
{
	/**
	 * Loads the entire menu table into memory.
	 *
	 * @return array
	 */
	public function load()
	{	$user = JFactory::getUser();
		$cache = JFactory::getCache('mod_menu', '');  // has to be mod_menu or this cache won't get cleaned
		if (!$data = $cache->get('menu_items'.JFactory::getLanguage()->getTag().$user->id)) {
			// Initialise variables.
			$db		= JFactory::getDbo();
			$app	= JFactory::getApplication();
			$query	= $db->getQuery(true);

			$query->select('m.id, m.menutype, m.title, m.alias, m.path AS route, m.link, m.type, m.level');
			$query->select('m.browserNav, m.access, m.params, m.home, m.img, m.template_style_id, m.component_id, m.parent_id');
			$query->select('m.language');
			$query->select('e.element as component');
			$query->from('#__menu AS m');
			$query->leftJoin('#__extensions AS e ON m.component_id = e.extension_id');
			$query->where('m.published = 1');
			$query->where('m.parent_id > 0');
			$query->where('m.client_id = 0');
			$query->order('m.lft');

			$groups = implode(',', $user->getAuthorisedViewLevels());
			$query->where('m.access IN (' . $groups . ')');

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// OSE Added - OSE and Open Source Excellence is the registered trade mark of the Open Source Excellence PTE LTD.
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			$where = null;
			if (file_exists(JPATH_SITE.DS.'components'.DS.'com_osemsc'.DS.'init.php') && file_exists(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_ose_cpu'.DS.'define.php') && !file_exists(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_osemsc'.DS.'installer.dummy.ini'))
			{
			require_once(JPATH_SITE.DS.'components'.DS.'com_osemsc'.DS.'init.php');

			$content_ids = oseRegistry::call('content')->getRestrictedContent('joomla','menu');

			$where = (COUNT($content_ids) > 0)?$query->where(' m.id NOT IN ('.implode(',',$content_ids).')'):null;
			}
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// OSE Added - OSE and Open Source Excellence is the registered trade mark of the Open Source Excellence PTE LTD.
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////

			// Set the query
			$db->setQuery($query);
			if (!($menus = $db->loadObjectList('id'))) {
				JError::raiseWarning(500, JText::sprintf('JERROR_LOADING_MENUS', $db->getErrorMsg()));
				return false;
			}

			foreach ($menus as &$menu) {
				// Get parent information.
				$parent_tree = array();
				if (isset($menus[$menu->parent_id])) {
					$parent_tree  = $menus[$menu->parent_id]->tree;
				}

				// Create tree.
				$parent_tree[] = $menu->id;
				$menu->tree = $parent_tree;

				// Create the query array.
				$url = str_replace('index.php?', '', $menu->link);
				$url = str_replace('&amp;','&',$url);

				parse_str($url, $menu->query);
			}

			$cache->store($menus, 'menu_items'.JFactory::getLanguage()->getTag());

			$this->_items = $menus;
		} else {
			$this->_items = $data;
		}
	}
	/**
	 * Gets menu items by attribute
	 *
	 * @param	string	$attributes	The field name
	 * @param	string	$values		The value of the field
	 * @param	boolean	$firstonly	If true, only returns the first item found
	 *
	 * @return	array
	 */
	public function getItems($attributes, $values, $firstonly = false)
	{
		$attributes = (array) $attributes;
		$values = (array) $values;
		$app	= JFactory::getApplication();
		// Filter by language if not set
		if ($app->isSite() && $app->getLanguageFilter() && !array_key_exists('language',$attributes)) {
			$attributes[]='language';
			$values[]=array(JFactory::getLanguage()->getTag(), '*');
		}
		return parent::getItems($attributes, $values, $firstonly);
	}

	/**
	 * Get menu item by id
	 *
	 * @param	string	$language	The language code.
	 *
	 * @return	object	The item object
	 * @since	1.5
	 */
	function getDefault($language='*')
	{
		if (array_key_exists($language, $this->_default) && JFactory::getApplication()->getLanguageFilter()) {
			return $this->_items[$this->_default[$language]];
		}
		else if (array_key_exists('*', $this->_default)) {
			return $this->_items[$this->_default['*']];
		}
		else {
			return 0;
		}
	}

}
