<?php
/**
 * @package	HikaShop for Joomla!
 * @version	2.1.2
 * @author	hikashop.com
 * @copyright	(C) 2010-2013 HIKARI SOFTWARE. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><?php
class ZoneController extends hikashopController{
	var $type='zone';
	var $toggle = array('zone_published'=>'zone_id');
	var $modify = array('apply','save','save2new','store','orderdown','orderup','saveorder','savechild','toggle','copy');
	function __construct($config = array()){
		parent::__construct($config);
		$this->modify_views[] = 'addchild';
		$this->modify_views[] = 'unpublish';
		$this->modify_views[] = 'publish';
		$this->modify_views[] = 'selectchildlisting';
		$this->display[] = 'addchild';
	}

	function copy(){
		$zones = JRequest::getVar( 'cid', array(), '', 'array' );
		$result = true;
		if(!empty($zones)){
			$zoneClass = hikashop_get('class.zone');
			foreach($zones as $zone){
				$data = $zoneClass->get($zone);
				if($data){
					$childs = $zoneClass->getChilds($data->zone_id);
					unset($data->zone_id);
					unset($data->zone_namekey);
					if(!$zoneClass->save($data)){
						$result=false;
					}elseif(!empty($childs)){
						$childNamekeys = array();
						foreach($childs as $child){
							$childNamekeys[]=$child->zone_namekey;
						}
						$zoneClass->addChilds($data->zone_namekey,$childNamekeys);
					}

				}
			}
		}
		if($result){
			$app =& JFactory::getApplication();
			$app->enqueueMessage(JText::_( 'HIKASHOP_SUCC_SAVED' ), 'success');
		}
		return $this->listing();
	}

	function savechild(){
		$new_id = $this->store();
		$main_id = JRequest::getInt('main_id');
		if($main_id && $new_id){
			$zoneObject = hikashop_get('class.zone');
			$insertedNamekeys = $zoneObject->addChilds($main_id,array($new_id));
			JRequest::setVar('cid',$new_id);
			JRequest::setVar( 'layout', 'savechild'  );
			return parent::display();
		}else{
			$this->selectchildlisting();
		}
	}

	function selectchildlisting(){
		JRequest::setVar( 'task', 'selectchildlisting'  );
		JRequest::setVar( 'layout', 'selectchildlisting'  );
		return parent::display();
	}

	function addchild(){
		$type=JRequest::getWord('type');
		if(!in_array($type,array('discount','shipping','payment','config','tax'))){
			$childNamekeys = JRequest::getVar( 'cid', array(), '', 'array' );
			$mainNamekey = JRequest::getVar( 'main_id', 0, '', 'int' );
			$zoneObject = hikashop_get('class.zone');
			$insertedNamekeys = $zoneObject->addChilds($mainNamekey,$childNamekeys);
			JRequest::setVar( 'cid', $insertedNamekeys );
			JRequest::setVar( 'layout', 'newchild'  );
		}else{
			JRequest::setVar( 'layout', 'addchild'  );
		}
		return parent::display();
	}

	function newchild(){
		JRequest::setVar( 'layout', 'newchildform'  );
		return parent::display();
	}

}
