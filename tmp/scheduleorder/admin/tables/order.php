<?php
/**
 * @version		1.0.0
 * @package		Joomla
 * @subpackage	Schedule Order
 * @author      Jason<jason@netwebx.com>
 * @copyright	Copyright (C) 2013 NetWebX.COM
 * @license		GNU/GPL, see LICENSE.php
 */

// no direct access
defined('_JEXEC') or die('Restricted Access');

jimport('joomla.database.table');

/**
 * #__schorder_order table.
 *
 * @package     Joomla
 * @subpackage  Schedule Order
 * @since       V1.0.0
 */
class ScheduleOrderTableOrder extends JTable
{
    /**
     * Constructor.
     *
     * @param   JDatabase  $db  A database connector object.
     *
     * @return  ScheduleOrderTableOrder
     * @since   V1.0.0
     */
    public function __construct($db)
    {
        parent::__construct('#__schorder_order', 'order_id', $db);
    }

    /**
     * Overloaded bind function to pre-process the params.
     *
     * @param   array   $array   The input array to bind.
     * @param   string  $ignore  A list of fields to ignore in the binding.
     *
     * @return  null|string	null is operation was satisfactory, otherwise returns an error
     * @see     JTable:bind
     * @since   V1.0.0
     */
    public function bind($array, $ignore = '')
    {
        if (isset($array['params']) && is_array($array['params'])) {
            $registry = new JRegistry();
            $registry->loadArray($array['params']);
            $array['params'] = (string) $registry;
        }

        return parent::bind($array, $ignore);
    }

    /**
     * Overloaded check method to ensure data integrity.
     *
     * @return  boolean  True on success.
     * @since   V1.0.0
     */
    public function check()
    {
        // Check for valid name.
        /*if (trim($this->title) === '') 
        {
            $this->setError(JText::_('COM_scheduleorder_ERROR_#__so_order_member_TITLE'));
            return false;
        }

        return true;*/
    	return true;
    }

    /**
     * Overload the store method for the Weblinks table.
     *
     * @param   boolean  $updateNulls  Toggle whether null values should be updated.
     *
     * @return  boolean  True on success, false on failure.
     * @since   V1.0.0
     */
    public function store($updateNulls = false)
    {
        // Initialiase variables.
        $date = JFactory::getDate()->toMySQL();
        $userId = JFactory::getUser()->get('id');

        if (empty($this->order_id)) 
        {
            // New record.
            $this->created_by = $userId;
        } else {
            // Existing record.
            $this->modified_by = $userId;
        }

        // Attempt to store the data.
        return parent::store($updateNulls);
    }
}
