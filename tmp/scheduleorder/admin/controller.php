<?php
/**
 * @version		1.0.0
 * @package		Joomla
 * @subpackage	Schedule Order
 * @author      Jason <jason@netwebx.com>
 * @copyright	Copyright (C) 2013 NetWebX.COM
 * @license		GNU/GPL, see LICENSE.php
 */
// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');

/**
 * ScheduleOrder Component Controller
 *
 * @package     Joomla
 * @subpackage  Schedule Order
 * @since       v1.0.0
 */
class ScheduleOrderController extends JController
{
    /**
     * display task
     *
     * @return void
     */
    function display($cachable = false)
    {
        $document = JFactory::getDocument();
        $document->addStyleSheet(JURI::base(true) . '/components/com_scheduleorder/assets/css/style.css');
        $task = $this->getTask();
        switch ($task)
        {
            case 'edit_category':
                JRequest::setVar('view', 'category');
                JRequest::setVar('edit', true);
                break;
            case 'new_category':
                JRequest::setVar('view', 'category');
                JRequest::setVar('edit', false);
                break;
            case 'add_member':
                JRequest::setVar('view', 'registrant');
                JRequest::setVar('edit', false);
                break;
            case 'edit_member':
                JRequest::setVar('view', 'registrant');
                JRequest::setVar('edit', true);
                break;
            case 'show_members':
                JRequest::setVar('view', 'members');
                break;
            case 'show_translation':
                JRequest::setVar('view', 'language');
                break;
            default:
                $view = JRequest::getVar('view', '');
                if (!$view)
                {
                    JRequest::setVar('view', 'cart');
                }
                break;
        }
        if (version_compare(JVERSION, '3.0', 'le')) {
            ScheduleOrderHelper::loadBootstrap();
        }
        ScheduleOrderHelper::addSubmenus(JRequest::getVar('view', 'scheduleorder'));
        ScheduleOrderHelper::displayCopyRight();
        // call parent behavior
        parent::display($cachable);
    }
}
