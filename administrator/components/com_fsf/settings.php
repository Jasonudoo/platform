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
<?php defined('_JEXEC') or die('Restricted access'); ?>
<?php
if (file_exists(JPATH_SITE.DS.'components'.DS.'com_fsf'.DS.'helper'.DS.'settings.php'))
{
	require_once (JPATH_SITE.DS.'components'.DS.'com_fsf'.DS.'helper'.DS.'settings.php');
} else if (file_exists(JPATH_SITE.DS.'components'.DS.'com_fsf'.DS.'settings.php'))
{
	require_once (JPATH_SITE.DS.'components'.DS.'com_fsf'.DS.'settings.php');
}
	

function FSF_GetPublishedText($ispub)
{
	if (FSF_Helper::Is16())
	{
		if ($ispub)
		{
			return '<span class="state publish"><span class="text">'.JText::_('Published').'</span></span>';
		} else {
			return '<span class="state unpublish"><span class="text">'.JText::_('Unpublished').'</span></span>';
		}
	} else {
		$img = 'publish_g.png';
		$alt = JText::_("PUBLISHED");

		if ($ispub == 0)
		{
			$img = 'publish_x.png';
			$alt = JText::_("UNPUBLISHED");
		}
	
		return '<img src="images/' . $img . '" width="16" height="16" border="0" alt="' . $alt .'" />';	
	} 
}

function FSF_GetModerationText($ispub)
{
	$src = JURI::base() . "components/com_fsf/assets/images/mod";
	if ($ispub == 2)
	{
		return "<img src='$src/declined.png' width='24' height='24' border='0' alt='".JText::_('DECLINED')."'/>";	
	}
	if ($ispub == 1)
	{
		return "<img src='$src/accepted.png' width='24' height='24' border='0' alt='".JText::_('ACCEPTED')."'/>";	
	}
	if ($ispub == 0)
	{
		return "<img src='$src/waiting.png' width='24' height='24' border='0' alt='".JText::_('AWAITING_MODERATION')."'/>";	
	}
}

function FSF_GetYesNoText($ispub)
{
	$img = 'tick.png';
	$alt = JText::_("YES");

	if ($ispub == 0)
	{
		$img = 'cross.png';
		$alt = JText::_("NO");
	}
	$src = JURI::base() . "/components/com_fsf/assets";
	return '<img src="' . $src . '/' . $img . '" width="16" height="16" border="0" alt="' . $alt .'" />';	
}
