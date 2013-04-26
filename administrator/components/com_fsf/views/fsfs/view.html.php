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

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

jimport( 'joomla.application.component.view' );
require_once (JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_fsf'.DS.'settings.php');
jimport('joomla.html.pane');


class FsfsViewFsfs extends JView
{
 
    function display($tpl = null)
	{
		JToolBarHelper::title( JText::_( 'FREESTYLE_FAQS' ), 'fsf.png' );
		FSFAdminHelper::DoSubToolbar();
	

		parent::display($tpl);
	}
	
	function Item($title, $link, $icon, $help)
	{
?>
		<div class="fsf_main_item fsj_tip" title="<?php echo JText::_($help); ?>">	
			<div class="fsf_main_icon">
				<a href="<?php echo JRoute::_($link); ?>">
					<img src="<?php echo JURI::root( true ); ?>/administrator/components/com_fsf/assets/images/<?php echo $icon;?>-48x48.png" width="48" height="48">
				</a>
			</div>
			<div class="fsf_main_text">
				<a href="<?php echo JRoute::_($link); ?>">
					<?php echo JText::_($title); ?>
				</a>
			</div>
		</div>	
<?php
	}
}


