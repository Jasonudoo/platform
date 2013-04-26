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
defined('_JEXEC') or die( 'Restricted access' );
jimport( 'joomla.html.parameter.element');

class JElementFaqid extends JElement
{
    /**
     * Element name
     *
     * @access    protected
     * @var        string
     */
    var    $_name = 'Faqid';

    function fetchElement($name, $value, &$node, $control_name)
    {
        $mainframe = JFactory::getApplication();

        $db            =& JFactory::getDBO();
        $doc         =& JFactory::getDocument();
        $template     = $mainframe->getTemplate();
        $fieldName    = $control_name.'['.$name.']';
        
        JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_fsf'.DS.'tables');
        $faq =& JTable::getInstance('faq','Table');
        if ($value) {
            $faq->load($value);
        } else {
            $faq->question = JText::_("SELECT_A_FAQ");
        }   
        
        $js = "
        function jSelectArticle(id, title, object) {
            document.getElementById(object + '_id').value = id;
            document.getElementById(object + '_name').value = title;
            document.getElementById('sbox-window').close();
        }";
        $doc->addScriptDeclaration($js);
                                     
        $link = 'index.php?option=com_fsf&amp;task=pick&amp;tmpl=component&amp;controller=faq';

        JHTML::_('behavior.modal', 'a.modal');
        $html = "\n".'<div style="float: left;"><input style="background: #ffffff;" type="text" id="'.$name.'_name" value="'.htmlspecialchars($faq->question, ENT_QUOTES, 'UTF-8').'" disabled="disabled" /></div>';
		$html .= '<div class="button2-left"><div class="blank"><a class="modal" title="'.JText::_("SELECT_A_FAQ").'"  href="'.$link.'" rel="{handler: \'iframe\', size: {x: 650, y: 410}}">'.JText::_("SELECT").'</a></div></div>'."\n";
        $html .= "\n".'<input type="hidden" id="'.$name.'_id" name="'.$fieldName.'" value="'.(int)$value.'" />';

        return $html;
    }
}


