<?php
defined('_JEXEC') or die("Direct Access Not Allowed");
class oseMscViewLevels extends oseMscView
{
    function display($tpl = null)
    {
        JHTML::stylesheet('style.css', 'administrator/components/com_osemsc/assets/css/');
        JToolBarHelper::title(JText::_('OSE Joomla Membership Control Manager') 
        $this->assignRef('list', $list);
        parent::display($tpl);
    }
}