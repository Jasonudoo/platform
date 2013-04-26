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
jimport('joomla.form.formfield');

class JFormFieldEBEvent extends JFormField
{

	/**
	 * Element name
	 *
	 * @access	protected
	 * @var		string
	 */
	var $_name = 'ebevent';

	function getInput()
	{
		$db = JFactory::getDBO();
		$sql = "SELECT id, title  FROM #__eb_events WHERE published = 1 ORDER BY title ";
		$db->setQuery($sql);
		$options = array();
		$options[] = JHTML::_('select.option', '0', JText::_('Select Event'), 'id', 'title');
		$options = array_merge($options, $db->loadObjectList());
		return JHTML::_('select.genericlist', $options, $this->name, ' class="inputbox" ', 'id', 'title', $this->value);
	}
}
