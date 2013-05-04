<?php
/**
  * @version     5.0 +
  * @package        Open Source Membership Control - com_osemsc
  * @subpackage    Open Source Access Control - com_osemsc
  * @author        Open Source Excellence (R) {@link  http://www.opensource-excellence.com}
  * @author        Created on 15-Nov-2010
  * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
  *
  *
  *  This program is free software: you can redistribute it and/or modify
  *  it under the terms of the GNU General Public License as published by
  *  the Free Software Foundation, either version 3 of the License, or
  *  (at your option) any later version.
  *
  *  This program is distributed in the hope that it will be useful,
  *  but WITHOUT ANY WARRANTY; without even the implied warranty of
  *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  *  GNU General Public License for more details.
  *
  *  You should have received a copy of the GNU General Public License
  *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
  *  @Copyright Copyright (C) 2010- Open Source Excellence (R)
*/
defined('_JEXEC') or die(";)");
class oseMscAddonActionContentSobipro extends oseMscAddon {
	public static function save($params) {}
	public static function delete($params) {}
	function getList() {
		$node= JRequest :: getString('node', null);
		switch(substr($node, 0, 4)) {
			case('cat-') :
			case('sec-') :
				return $this->getCatList($node);
				break;
			case('art-') :
				return '';
				break;
			default :
				return $this->getSectionList();
				break;
		}
	}
	
	function & getCatList($node) {
		$db= oseDB :: instance();
		if(strstr($node,'sec'))
		{
			$parentid= str_replace('sec-', '', $node);
		}else{
			$parentid= str_replace('cat-', '', $node);
		}
		$search= JRequest :: getString('search', null);
		$search= JString :: strtolower($search);
		$msc_id= JRequest :: getInt('msc_id', 0);
		$start= JRequest :: getInt('start', 0);
		$limit= JRequest :: getInt('limit', 20);
		$where= array();
		if($parentid) {
			$where[]= 'c.parent = '.$parentid;
		}	
		$where[] = " c.`approved` = 1 ";
		$where[] = " c.`oType` = 'category' ";
		$where=(count($where) ? ' WHERE ('.implode(') AND (', $where).')' : '');
		$query= " SELECT CONCAT('cat-',c.id) AS id,c.id AS catid, c.name FROM `#__sobipro_object` AS c ".$where." GROUP BY c.id ORDER BY c.name";
		$db->setQuery($query);
		$rows= oseDB :: loadList();
		//$total= count($rows);
		foreach($rows as $key =>$row) {
			$obj= oseRegistry :: call('content')->getInstance('msc')->getItem('sobipro', 'category', $row['catid'], 'msc', $msc_id, null, 'obj');
			$controlled= empty($obj) ? 0 : $obj->status;
			if($controlled == '1') {
				$rows[$key]['controlled']= JText :: _('SHOW_TO_MEMBERS');
			}
			elseif($controlled == '-1') {
				$rows[$key]['controlled']= JText :: _('HIDE_TO_MEMBERS');
			} else {
				$rows[$key]['controlled']= JText :: _('SHOW_TO_ALL');
			}
			$rows[$key]['iconCls']= 'task-folder';	
		}
		
		return $rows;
	}
	function & getSectionList() {
		$db= oseDB :: instance();
		$search= JRequest :: getString('search', null);
		$search= JString :: strtolower($search);
		$msc_id= JRequest :: getInt('msc_id', 0);
		$start= JRequest :: getInt('start', 0);
		$limit= JRequest :: getInt('limit', 20);
		$where= array();
		$where[] = " c.`oType` = 'section' ";
		$where[] = " c.`approved` = 1 ";
		$where=(count($where) ? ' WHERE ('.implode(') AND (', $where).')' : '');
		$query= "SELECT CONCAT('sec-',c.id) AS id,c.id AS secid, c.name FROM `#__sobipro_object` AS c ".$where." GROUP BY c.id";
		$db->setQuery($query);
		$rows= oseDB :: loadList('obj');
		$total= count($rows);
		foreach($rows as $item) {
			$obj= oseRegistry :: call('content')->getInstance('msc')->getItem('sobipro', 'section', $item->secid, 'msc', $msc_id, null, 'obj');
			$controlled= empty($obj) ? 0 : $obj->status;
			if($controlled == '1') {
				$item->controlled= JText :: _('SHOW_TO_MEMBERS');
			}
			elseif($controlled == '-1') {
				$item->controlled= JText :: _('Hide to Members');
			} else {
				$item->controlled= JText :: _('Show to All');
			}
			$item->iconCls= 'task-folder';
			$item->checked= false;
		}
		$result= array();
		$result['total']= $total;
		$result['results']= $rows;
		return $rows;
	}
	
	function changeStatus() {
		$db= oseDB :: instance();
		$msc_id= JRequest :: getInt('msc_id', 0);
		$content_ids= JRequest :: getVar('sbc_ids', array());
		$newStatus= JRequest :: getInt('status', 0);
		$result= array();
		$result['success']= true;
		$result['title']= JText::_('DONE');
		$result['content']= JText::_('SUCCESSFULLY');
		if(empty($content_ids)) {
			return $result;
		}
		foreach($content_ids as $key => $content_id) {
			$node= explode('-', $content_id);
			$content_type= $node[0];
			$content_id= $node[1];
			switch($content_type) {
				case('sec') :
					$updated= $this->changeSecStatus($content_id, $msc_id, $newStatus);
					break;
				case('cat') :
					$updated= $this->changeCatStatus($content_id, $msc_id, $newStatus);
					break;
				case('art') :
					$updated= $this->changeArtStatus($content_id, $msc_id, $newStatus);
					break;
				default :
					$updated= array('success' => true);
					break;
			}
		}
		if(!$updated['success']) {
			$result['success']= false;
			$result['title']= JText::_('ERROR');
			$result['content']= JText::_('ERROR');
		}
		return $result;
	}
	function changeSecStatus($content_id, $msc_id, $newStatus,$params = array()) 
	{
		$db = oseDB::instance();

		$content= oseRegistry :: call('content')->getInstance('msc');
		
		$item= $content->getItem('sobipro', 'section', $content_id, 'msc', $msc_id, '', 'obj');
		if(empty($item)) 
		{
			$updated= $content->insert('sobipro', 'section', $content_id, 'msc', $msc_id, $newStatus);
			if(!$updated) 
			{
				$result= array();
				$result['success']= false;
				$result['title']= JText::_('ERROR');
				$result['content']= JText::_('ERROR');
				return $result;
			}
		} else {
			$status= $item->status;
			if($status != $newStatus) 
			{
				$updated= $content->update($item, $newStatus);
				if(!$updated) 
				{
					$result= array();
					$result['success']= false;
					$result['title']= JText::_('ERROR');
					$result['content']= JText::_('ERROR');
					return $result;
				}
			}
		}
		
		$query = "SELECT * FROM `#__sobipro_object` WHERE `parent` = '{$content_id}'";
		$db->setQuery($query);
		$objs= $db->loadObjectList();
		foreach($objs as $obj)
		{
			$updateItem = $this->changeCatStatus($obj->id, $msc_id, $newStatus);
			if(!$updateItem['success'])
			{
				$result= array();
				$result['success']= false;
				$result['title']= JText::_('ERROR');
				$result['content']= JText::_('ERROR');
				return $result;
			}
		}
		
		$result = array();
		$result['success'] = true;
		$result['title'] = JText::_('DONE');
		$result['content'] = JText::_('SUCCESSFULLY');
		return $result;
			
	}	
	
	function changeArtStatus($item_id, $msc_id, $newStatus) 
	{

		$content = oseRegistry::call('content')->getInstance('msc');
		$item = $content->getItem('sobipro','article',$item_id,'msc',$msc_id, '','obj');
	
		if(empty($item))
		{
			$updated = $content->insert('sobipro','article',$item_id,'msc',$msc_id, $newStatus);
			$db = oseDB::instance();
			
			if(!$updated)
			{
				$result = array();
				$result['success'] = false;
				$result['title'] = JText::_('ERROR');
				$result['content'] = JText::_('ERROR');
				return $result;
			}
		}
		else
		{
			$status = $item->status;
			
			if($status != $newStatus)
			{
				$updated = $content->update($item, $newStatus);
			
				if(!$updated)
				{
					$result = array();
					$result['success'] = false;
					$result['title'] = JText::_('ERROR');
					$result['content'] = JText::_('ERROR');
					return $result;
				}
			}
		}
		
		$result = array();
		$result['success'] = true;
		$result['title'] = JText::_('DONE');
		$result['content'] = JText::_('SUCCESSFULLY');
		return $result;
	}
	function changeCatStatus($catid, $msc_id, $newStatus,$params = array()) {
		$db = oseDB::instance();

		$content= oseRegistry :: call('content')->getInstance('msc');

		$query = "SELECT id,parent FROM `#__sobipro_object` WHERE `oType` = 'category'";
		$db->setQuery($query);
		$rows = $db->loadObjectList();
		$children = array();
		if (!empty($rows))
		{
			foreach ($rows as $v )
			{
				$pt = $v->parent;
				$list = @$children[$pt] ? $children[$pt] : array();
				array_push( $list, $v );
				$children[$pt] = $list;
			}
		}
		$contentIds = self::getSubCats($catid, array(), $children);
		array_push($contentIds,$catid);
		$contentIds = array_unique($contentIds);
		foreach($contentIds as  $content_id)
		{
			$item= $content->getItem('sobipro', 'category', $content_id, 'msc', $msc_id, '', 'obj');
			if(empty($item)) 
			{
				$updated= $content->insert('sobipro', 'category', $content_id, 'msc', $msc_id, $newStatus);
				if(!$updated) 
				{
					$result= array();
					$result['success']= false;
					$result['title']= JText::_('ERROR');
					$result['content']= JText::_('ERROR');
					return $result;
				}
			} else {
				$status= $item->status;
				if($status != $newStatus) 
				{
					$updated= $content->update($item, $newStatus);
					if(!$updated) 
					{
						$result= array();
						$result['success']= false;
						$result['title']= JText::_('ERROR');
						$result['content']= JText::_('ERROR');
						return $result;
					}
				}
			}
			/*
			$query = "SELECT * FROM `#__sobipro_relations` WHERE `pid` = '{$content_id}' AND `oType` = 'entry'";
			$db->setQuery($query);
			$objs= $db->loadObjectList();
			foreach($objs as $obj)
			{
				$updateItem = $this->changeArtStatus($obj->id, $msc_id, $newStatus);
				if(!$updateItem['success'])
				{
					$result= array();
					$result['success']= false;
					$result['title']= 'Error';
					$result['content']= 'Error';
					return $result;
				}
			}
			*/
		}

		$result = array();
		$result['success'] = true;
		$result['title'] = JText::_('DONE');
		$result['content'] = JText::_('SUCCESSFULLY');
		return $result;
	}
	
	
	function getSubCats($id, $list, &$children)
	{
		if (@$children[$id])
		{
			foreach ($children[$id] as $v)
			{
				$id = $v->id;
				$list[] = $id;
				$list = $this->getSubCats($id, $list, $children);
			}
		}
		return $list;
	}
	
}
?>