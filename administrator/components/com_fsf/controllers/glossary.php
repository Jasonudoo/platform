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

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );



class FsfsControllerGlossary extends FsfsController
{

	function __construct()
	{
		parent::__construct();

		// Register Extra tasks
		$this->registerTask( 'add'  , 	'edit' );
		$this->registerTask( 'unpublish', 'unpublish' );
		$this->registerTask( 'publish', 'publish' );
		$this->registerTask( 'orderup', 'orderup' );
		$this->registerTask( 'orderdown', 'orderdown' );
		$this->registerTask( 'saveorder', 'saveorder' );
	}

	function cancellist()
	{
		$link = 'index.php?option=com_fsf&view=fsfs';
		$this->setRedirect($link, $msg);
	}

    function pick()
    {
        JRequest::setVar( 'view', 'glossarys' );
        JRequest::setVar( 'layout', 'pick'  );
        
        parent::display();
    }

	function edit()
	{
		JRequest::setVar( 'view', 'glossary' );
		JRequest::setVar( 'layout', 'form'  );
		JRequest::setVar('hidemainmenu', 1);

		parent::display();
	}

	function save()
	{
		$model =& $this->getModel('glossary');

        $post = JRequest::get('post');
        $post['description'] = JRequest::getVar('description', '', 'post', 'string', JREQUEST_ALLOWRAW);
        $post['longdesc'] = JRequest::getVar('longdesc', '', 'post', 'string', JREQUEST_ALLOWRAW);
       
		if ($model->store($post)) {
			$msg = JText::_("GLOSSARY_ITEM_SAVED");
		} else {
			$msg = JText::_("ERROR_SAVING_GLOSSARY_ITEM");
		}

		$link = 'index.php?option=com_fsf&view=glossarys';
		$this->setRedirect($link, $msg);
	}


	function remove()
	{
		$model = $this->getModel('glossary');
		if(!$model->delete()) {
			$msg = JText::_("ERROR_ONE_OR_MORE_GLOSSARY_ITEM_COULD_NOT_BE_DELETED");
		} else {
			$msg = JText::_("GLOSSARY_ITEM_S_DELETED" );
		}

		$this->setRedirect( 'index.php?option=com_fsf&view=glossarys', $msg );
	}


	function cancel()
	{
		$msg = JText::_("OPERATION_CANCELLED");
		$this->setRedirect( 'index.php?option=com_fsf&view=glossarys', $msg );
	}

	function unpublish()
	{
		$model = $this->getModel('glossary');
		if (!$model->unpublish())
			$msg = JText::_("ERROR_THERE_HAS_BEEN_AN_ERROR_UNPUBLISHING_AN_GLOSSARY_ITEM");

		$this->setRedirect( 'index.php?option=com_fsf&view=glossarys', $msg );
	}

	function publish()
	{
		$model = $this->getModel('glossary');
		if (!$model->publish())
			$msg = JText::_("ERROR_THERE_HAS_BEEN_AN_ERROR_PUBLISHING_AN_GLOSSARY_ITEM");

		$this->setRedirect( 'index.php?option=com_fsf&view=glossarys', $msg );
	}
}



