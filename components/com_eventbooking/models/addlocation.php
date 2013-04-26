<?php
/**
 * @version		1.5.0
 * @package		Joomla
 * @subpackage	Event Booking
 * @author  Tuan Pham Ngoc
 * @copyright	Copyright (C) 2010 Ossolution Team
 * @license		GNU/GPL, see LICENSE.php
 */
// no direct access
defined('_JEXEC') or die();

/**
 * Event Booking Component AddLocation Model
 *
 * @package		Joomla
 * @subpackage	Event Booking
 * @since 1.5
 */
class EventBookingModelAddlocation extends JModelLegacy
{

	/**
	 * Field id
	 *
	 * @var int
	 */
	var $_id = null;

	/**
	 * Location data
	 *
	 * @var array
	 */
	var $_data = null;

	/**
	 * Constructor
	 *
	 * @since 1.5
	 */
	function __construct()
	{
		parent::__construct();
		$id = JRequest::getInt('id', 0);
		if ($id)
			$this->setId($id);
	}

	/**
	 * Method to set the field identifier
	 *
	 * @access	public
	 * @param	int field identifier
	 */
	function setId($id)
	{
		// Set field id and wipe data
		$this->_id = $id;
		$this->_data = null;
	}

	/**
	 * Method to get a field
	 *
	 * @since 1.5
	 */
	function &getData()
	{
		if (empty($this->_data))
		{
			$id = JRequest::getInt('id', 0);
			$sql = 'SELECT * FROM #__eb_locations WHERE id=' . $id;
			$this->_db->setQuery($sql);
			$this->_data = $this->_db->loadObject();
			if (!$id)
				$this->_initData();
		}
		return $this->_data;
	}

	/**
	 * Method to store a field
	 *
	 * @access	public
	 * @return	boolean	True on success
	 * @since	1.5
	 */
	function store(&$data)
	{
		$apiKey = EventBookingHelper::getConfigValue('google_api_key');
		$row = & $this->getTable('EventBooking', 'Location');
		$user = &JFactory::getUser();
		$row->user_id = $user->id;
		if ($data['id'])
			$row->load($data['id']);
		if (!$row->bind($data))
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		//Canculate location here		
		$ch = curl_init();
		if (!$row->lat && !$row->long)
		{
			$address = array();
			if ($row->address)
				$address[] = $row->address;
			if ($row->city)
				$address[] = $row->city;
			if ($row->state)
				$address[] = $row->state;
			if ($row->zip)
				$address[] = $row->zip;
			if ($row->country)
				$address[] = $row->country;
			$address = implode('+', $address);
			$address = str_replace(' ', '+', $address);
			$url = 'http://maps.google.com/maps/geo?q=' . $address . '&output=csv&key=' . $apiKey;
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			$data = curl_exec($ch);
			curl_close($ch);
			$arrData = explode(',', $data);
			if ((count($arrData) == 4) && $arrData[0] == 200)
			{
				$row->lat = $arrData[2];
				$row->long = $arrData[3];
			}
		}
		if (!$row->store())
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		return true;
	}

	/**
	 * Init Location data
	 *
	 */
	function _initData()
	{
		$row = new stdClass();
		$row->id = null;
		$row->name = null;
		$row->address = null;
		$row->city = null;
		$row->state = null;
		$row->zip = null;
		$row->country = EventBookingHelper::getConfigValue('default_country');
		$row->lat = null;
		$row->long = null;
		$row->user_id = null;
		$row->published = 1;
		$this->_data = $row;
	}

	/**
	 * Delete the selected location
	 * @param array $cid
	 * @return boolean
	 */
	function delete($cid = array())
	{
		if (count($cid))
		{
			$cids = implode(',', $cid);
			$sql = 'DELETE FROM #__eb_locations WHERE id IN (' . $cids . ')';
			$this->_db->setQuery($sql);
			if (!$this->_db->query())
				return false;
		}
		return true;
	}
}