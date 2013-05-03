<?php
/**
 * @version		1.0.0
 * @package		Joomla
 * @subpackage	Schedule Order
 * @author       Jason<jason@netwebx.com>
 * @copyright	Copyright (C) 2013 NetWebX.COM
 * @license		GNU/GPL, see LICENSE.php
 */

// no direct access
defined('_JEXEC') or die('Restricted Access');

jimport('joomla.application.component.controller');

/**
 * ScheduleOrder Component Controller
 *
 * @package     Joomla
 * @subpackage  Schedule Order
 * @since       V1.0.0
 */
class ScheduleOrderController extends JController
{
    /**
     * Constructor function
     *
     * @param array $config
     */
    public function __construct($config = array())
    {
        parent::__construct($config);
    }
    /**
     * Display information
     *
     */
    public function display($cachable = false)
    {
        $task = $this->getTask();
        $document = JFactory::getDocument();
        $styleUrl = JURI::base(true) . '/components/com_scheduleorder/assets/css/style.css';
        $document->addStylesheet($styleUrl, 'text/css', null, null);

        switch ($task) {
        case 'view_categories':
            JRequest::setVar('view', 'categories');
            JRequest::setVar('layout', 'default');
            break;
        case 'view_category':
            JRequest::setVar('view', 'category');
            break;
        case 'individual_registration':
            JRequest::setVar('view', 'register');
            JRequest::setVar('layout', 'default');
            break;
        case 'individual_confirmation':
            JRequest::setVar('view', 'confirmation');
            JRequest::setVar('layout', 'default');
            break;
        case 'group_registration':
            JRequest::setVar('view', 'register');
            JRequest::setVar('layout', 'group');
            break;
        case 'group_member':
            JRequest::setVar('view', 'register');
            JRequest::setVar('layout', 'group_member');
            break;
        case 'group_billing':
            $db = JFactory::getDBO();
            $groupId = JRequest::getInt('group_id', 0);
            $sql = 'SELECT event_id FROM #__eb_registrants WHERE id='
                    . $groupId;
            $db->setQuery($sql);
            $eventId = (int) $db->loadResult();
            JRequest::setVar('event_id', $eventId);
            JRequest::setVar('view', 'register');
            JRequest::setVar('layout', 'group_billing');
            break;
        case 'group_confirmation':
            JRequest::setVar('view', 'confirmation');
            JRequest::setVar('layout', 'group');
            break;
        case 'view_event':
            JRequest::setVar('view', 'event');
            JRequest::setVar('layout', 'default');
            break;
        case 'view_map':
            JRequest::setVar('view', 'map');
            JRequest::setVar('layout', 'default');
            break;
        case 'registration_complete':
            JRequest::setVar('view', 'complete');
            JRequest::setVar('layout', 'default');
            break;
        case 'registration_failure':
            JRequest::setVar('view', 'failure');
            JRequest::setVar('layout', 'default');
            break;
        case 'return':
            JRequest::setVar('view', 'complete');
            JRequest::setVar('layout', 'default');
            break;
        case 'cancel':
            JRequest::setVar('view', 'cancel');
            JRequest::setVar('layout', 'default');
            break;

        #Registrants
        case 'show_history':
            JRequest::setVar('view', 'history');
            break;
        case 'show_registrants':
            JRequest::setVar('view', 'registrants');
            break;
        case 'add_registrant':
            JRequest::setVar('hidemainmenu', 1);
            JRequest::setVar('view', 'registrant');
            JRequest::setVar('edit', false);
            break;
        case 'edit_registrant':
            JRequest::setVar('view', 'registrant');
            break;
        case 'email_registrants':
            JRequest::setVar('view', 'email');
            break;
        case 'edit_members':
            JRequest::setVar('view', 'members');
            break;
        #End registrants

        case 'invite_form':
            JRequest::setVar('view', 'invite');
            JRequest::setVar('layout', 'default');
            break;
        case 'invite_complete':
            JRequest::setVar('view', 'invite');
            JRequest::setVar('layout', 'complete');
            break;
        #Cart function
        case 'view_cart':
            JRequest::setVar('view', 'cart');
            JRequest::setVar('layout', 'default');
            break;
        case 'view_checkout':
            JRequest::setVar('view', 'register');
            JRequest::setVar('layout', 'cart');
            break;
        case 'checkout':
            JRequest::setVar('view', 'register');
            JRequest::setVar('layout', 'cart');
            break;
        case 'checkout_confirmation':
            JRequest::setVar('view', 'confirmation');
            JRequest::setVar('layout', 'cart');
            break;
        #Adding, managing events from front-end
        case 'show_events':
            JRequest::setVar('view', 'events');
            JRequest::setVar('layout', 'default');
            break;
        case 'edit_event':
            JRequest::setVar('view', 'event');
            JRequest::setVar('layout', 'form');
            break;
        #Misc
        case 'show_registrant_list':
            JRequest::setVar('view', 'registrantlist');
            JRequest::setVar('layout', 'default');
            break;
        case 'waitinglist_form';
            JRequest::setVar('view', 'waitinglist');
            JRequest::setVar('layout', 'default');
            break;
        case 'waitinglist_complete':
            JRequest::setVar('view', 'waitinglist');
            JRequest::setVar('layout', 'complete');
            break;
        default:
            $view = JRequest::getVar('view', '');
            if (!$view) {
                JRequest::setVar('view', 'categories');
                JRequest::setVar('layout', 'default');
            }
            break;
        }

        parent::display($cachable);
    }

}
