<?php
/**
 * @version		1.5.3
 * @package		Joomla
 * @subpackage	Event Booking
 * @author  Tuan Pham Ngoc
 * @copyright	Copyright (C) 2010 Ossolution Team
 * @license		GNU/GPL, see LICENSE.php
 */
// no direct access
defined('_JEXEC') or die();

/**
 * Event Booking Component Plugin Model
 *
 * @package		Joomla
 * @subpackage	Event Booking
 * @since 1.5
 */
class EventBookingModelPlugin extends JModelLegacy
{

	/**
	 * Plugin ID
	 *
	 * @var int
	 */
	var $_id = null;

	/**
	 * Plugin data
	 *
	 * @var array
	 */
	var $_data = null;

	/**
	 * Constructor function, init some data
	 *
	 */
	function __construct()
	{
		parent::__construct();
		$array = JRequest::getVar('cid', array(0), '', 'array');
		$edit = JRequest::getVar('edit', true);
		if ($edit)
			$this->setId((int) $array[0]);
	}

	/**
	 * Method to set the plugin identifier
	 *
	 * @access	public
	 * @param	int plugin identifier
	 */
	function setId($id)
	{
		// Set plugin id and wipe data
		$this->_id = $id;
		$this->_data = null;
	}

	/**
	 * Method to get a plugin data
	 *
	 * @since 1.5
	 */
	function &getData()
	{
		if (empty($this->_data))
		{
			if ($this->_id)
				$this->_loadData();
		}
		return $this->_data;
	}

	/**
	 * Load plugin data
	 *
	 */
	function _loadData()
	{
		$sql = 'SELECT * FROM #__eb_payment_plugins WHERE id=' . $this->_id;
		$this->_db->setQuery($sql);
		$this->_data = $this->_db->loadObject();
	}

	/**
	 * Method to store a plugin
	 *
	 * @access	public
	 * @return	boolean	True on success
	 * @since	1.5
	 */
	function store(&$data)
	{
		$row = & $this->getTable('EventBooking', 'Plugin');
		$row->load($this->_id);
		if (!$row->bind($data))
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		//Save parameters
		$params = JRequest::getVar('params', null, 'post', 'array');
		if (is_array($params))
		{
			$txt = array();
			foreach ($params as $k => $v)
			{
				if (is_array($v))
				{
					$v = implode(',', $v);
				}
				$v = str_replace("\r\n", '@@', $v);
				$txt[] = "$k=\"$v\"";
			}
			$row->params = implode("\n", $txt);
		}
		if (!$row->store())
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		return true;
	}

	/**
	 * Publish/unpublish plugins
	 *
	 * @param array $cid
	 * @param int $state 
	 */
	function publish($cid, $state = 1)
	{
		$db = JFactory::getDBO();
		$sql = 'UPDATE #__eb_payment_plugins SET published=' . $state . ' WHERE id IN(' . implode(',', $cid) . ')';
		$db->setQuery($sql);
		if ($db->query())
			return true;
		else
			return false;
	}

	/**
	 * Install the plugin
	 *
	 */
	function install()
	{
		$db = JFactory::getDBO();
		jimport('joomla.filesystem.folder');
		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.archive');
		$plugin = JRequest::getVar('plugin_package', null, 'files');
		if ($plugin['error'] || $plugin['size'] < 1)
		{
			JRequest::setVar('msg', JText::_('Upload plugin package error'));
			return false;
		}
		$config = new JConfig();
		$dest = $config->tmp_path . '/' . $plugin['name'];
		$uploaded = JFile::upload($plugin['tmp_name'], $dest);
		if (!$uploaded)
		{
			JRequest::setVar('msg', JText::_('Upload plugin package'));
			return false;
		}
		// Temporary folder to extract the archive into
		$tmpdir = uniqid('install_');
		$extractdir = JPath::clean(dirname($dest) . '/' . $tmpdir);
		$result = JArchive::extract($dest, $extractdir);
		if (!$result)
		{
			JRequest::setVar('msg', JText::_('Upload plugin package'));
			return false;
		}
		$dirList = array_merge(JFolder::files($extractdir, ''), JFolder::folders($extractdir, ''));
		if (count($dirList) == 1)
		{
			if (JFolder::exists($extractdir . '/' . $dirList[0]))
			{
				$extractdir = JPath::clean($extractdir . '/' . $dirList[0]);
			}
		}
		//Now, search for xml file
		$xmlfiles = JFolder::files($extractdir, '.xml$', 1, true);
		if (empty($xmlfiles))
		{
			JRequest::setVar('msg', JText::_('Could not find xml file in the package'));
			return false;
		}
		$file = $xmlfiles[0];
		$root = JFactory::getXML($file, true);
		if ($root->getName() !== 'install')
		{
			JRequest::setVar('msg', JText::_('Invalid xml file for payment plugin installation function'));
			return false;
		}
		$row = JTable::getInstance('EventBooking', 'Plugin');
		$name = (string) $root->name;
		$title = (string) $root->title;
		$author = (string) $root->author;
		$creationDate = (string) $root->creationDate;
		$copyright = (string) $root->copyright;
		$license = (string) $root->license;
		$authorEmail = (string) $root->authorEmail;
		$authorUrl = (string) $root->authorUrl;
		$version = (string) $root->version;
		$description = (string) $root->description;
		$sql = 'SELECT id FROM #__eb_payment_plugins WHERE name="' . $name . '"';
		$db->setQuery($sql);
		$pluginId = (int) $db->loadResult();
		if ($pluginId)
		{
			$row->load($pluginId);
			$row->name = $name;
			$row->title = $title;
			$row->author = $author;
			$row->creation_date = $creationDate;
			$row->copyright = $copyright;
			$row->license = $license;
			$row->author_email = $authorEmail;
			$row->author_url = $authorUrl;
			$row->version = $version;
			$row->description = $description;
		}
		else
		{
			$row->name = $name;
			$row->title = $title;
			$row->author = $author;
			$row->creation_date = $creationDate;
			$row->copyright = $copyright;
			$row->license = $license;
			$row->author_email = $authorEmail;
			$row->author_url = $authorUrl;
			$row->version = $version;
			$row->description = $description;
			$row->published = 0;
			$row->ordering = $row->getNextOrder('published=1');
		}
		$row->store();
		$pluginDir = JPATH_ROOT . '/components/com_eventbooking/payments';
		JFile::move($file, $pluginDir . '/' . basename($file));
		$files = $root->files->children();
		for ($i = 0, $n = count($files); $i < $n; $i++)
		{
			$file = $files[$i];
			if ($file->getName() == 'filename')
			{
				$fileName = $file;
				if (!JFile::exists($pluginDir . '/' . $fileName))
				{
					JFile::copy($extractdir . '/' . $fileName, $pluginDir . '/' . $fileName);
				}
			}
			elseif ($file->getName() == 'folder')
			{
				$folderName = $file;
				if (JFolder::exists($extractdir . '/' . $folderName))
				{
					JFolder::move($extractdir . '/' . $folderName, $pluginDir . '/' . $folderName);
				}
			}
		}
		
		$languageFolder = JPATH_ROOT . '/' . 'language';
		$files = $root->languages->children();
		for ($i = 0, $n = count($files); $i < $n; $i++)
		{
			$fileName = $files[$i];
			$pos = strpos($fileName, '.');
			$languageSubFolder = substr($fileName, 0, $pos);
			if (!JFile::exists($languageFolder . '/' . $languageSubFolder . '/' . $fileName))
			{
				JFile::copy($extractdir . '/' . $fileName, $languageFolder . '/' . $languageSubFolder . '/' . $fileName);
			}
		}
		JFolder::delete($extractdir);
		return true;
	}

	/**
	 * Uninstall a payment plugin
	 *
	 * @param int $id
	 * @return boolean
	 */
	function uninstall($id)
	{
		jimport('joomla.filesystem.folder');
		jimport('joomla.filesystem.file');
		$row = JTable::getInstance('EventBooking', 'Plugin');
		$row->load($id);
		$name = $row->name;
		$pluginFolder = JPATH_ROOT . '/components/com_eventbooking/payments';
		$file = $pluginFolder . '/' . $name . '.xml';
		if (!JFile::exists($file))
		{
			$row->delete();
			return true;
		}
		$root = JFactory::getXML($file);
		$files = $root->files->children();
		$pluginDir = JPATH_ROOT . '/components/com_eventbooking/payments';
		for ($i = 0, $n = count($files); $i < $n; $i++)
		{
			$file = $files[$i];
			if ($file->getName() == 'filename')
			{
				$fileName = $file;
				if (JFile::exists($pluginDir . '/' . $fileName))
				{
					JFile::delete($pluginDir . '/' . $fileName);
				}
			}
			elseif ($file->getName() == 'folder')
			{
				$folderName = $file;
				if ($folderName)
				{
					if (JFolder::exists($pluginDir . '/' . $folderName))
					{
						JFolder::delete($pluginDir . '/' . $folderName);
					}
				}
			}
		}
		$files = $root->languages->children();
		$languageFolder = JPATH_ROOT . '/language';
		for ($i = 0, $n = count($files); $i < $n; $i++)
		{
			$fileName = $files[$i];
			$pos = strpos($fileName, '.');
			$languageSubFolder = substr($fileName, 0, $pos);
			if (JFile::exists($languageFolder . '/' . $languageSubFolder . '/' . $fileName))
			{
				JFile::delete($languageFolder . '/' . $languageSubFolder . '/' . $fileName);
			}
		}
		JFile::delete($pluginFolder . '/' . $name . '.xml');
		$row->delete();
		return true;
	}

	/**
	 * Save the order of plugins
	 *
	 * @param array $cid
	 * @param array $order
	 */
	function saveOrder($cid, $order)
	{
		$row = JTable::getInstance('EventBooking', 'Plugin');
		// update ordering values
		for ($i = 0; $i < count($cid); $i++)
		{
			$row->load((int) $cid[$i]);
			// track parents			
			if ($row->ordering != $order[$i])
			{
				$row->ordering = $order[$i];
				if (!$row->store())
				{
					$this->setError($this->_db->getErrorMsg());
					return false;
				}
			}
		}
		return true;
	}

	/**
	 * Change ordering of a category
	 *
	 */
	function move($direction)
	{
		$row = JTable::getInstance('EventBooking', 'Plugin');
		$row->load($this->_id);
		if (!$row->move($direction, ' published = 1 '))
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		return true;
	}
}
?> 