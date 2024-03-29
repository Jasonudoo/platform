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
class CategoryViewCategory extends HikaShopView {
	var $type = 'product';
	var $ctrl= 'category';
	var $nameListing = 'HIKA_CATEGORIES';
	var $nameForm = 'HIKA_CATEGORIES';
	var $icon = 'category';
	var $module=false;
	function display($tpl = null,$params=array()){
		$this->paramBase = HIKASHOP_COMPONENT.'.'.$this->getName();
		$function = $this->getLayout();
		$this->params = $params;
		if(method_exists($this,$function)) $this->$function();
		parent::display($tpl);
	}
	function listing(){
		$config =& hikashop_config();
		$this->assignRef('config',$config);
		$module = hikashop_get('helper.module');
		$module->initialize($this);
		$this->paramBase.='_'.$this->params->get('main_div_name');
		$filters = array();
		$app = JFactory::getApplication();
		$pageInfo = new stdClass();
		$pageInfo->filter = new stdClass();
		$pageInfo->filter->order = new stdClass();
		$pageInfo->limit = new stdClass();
		$catData = null;
		$database = JFactory::getDBO();
		$content_type = $this->params->get('content_type');
		$defaultParams = $config->get('default_params');
		if($content_type=='manufacturer'){
			$category_type = 'manufacturer';
			$id = JRequest::getInt("cid");
			$class = hikashop_get('class.category');
			$new_id = 'manufacturer';
			$class->getMainElement($new_id);
			$this->params->set('selectparentlisting',$new_id);
		}else{
			$category_type = 'product';
		}

		if($this->params->get('limit','')==''){
			$this->params->set('limit',@$defaultParams['limit']);
		}
		if($this->params->get('order_dir','inherit')=='inherit'){
			$this->params->set('order_dir',@$defaultParams['order_dir']);
		}
		if( (int)$this->params->get('limit') == 0 ) {
			$this->params->set('limit', 1);
		}
		if($this->params->get('margin','')==''){
			$this->params->set('margin',@$defaultParams['margin']);
		}
		if($this->params->get('border_visible','-1')=='-1'){
			$this->params->set('border_visible',@$defaultParams['border_visible']);
		}
		if($this->params->get('div_item_layout_type','inherit')=='inherit'){
			$this->params->set('div_item_layout_type',@$defaultParams['div_item_layout_type']);
		}
		if($this->params->get('text_center','-1')=='-1'){
			$this->params->set('text_center',@$defaultParams['text_center']);
		}
		if($this->params->get('columns','')==''){
			$this->params->set('columns',@$defaultParams['columns']);
		}
		if($this->params->get('number_of_products','-1')=='-1'){
			$this->params->set('number_of_products',@$defaultParams['number_of_products']);
		}
		if($this->params->get('background_color','')==''){
			$this->params->set('background_color',@$defaultParams['background_color']);
		}
		if($this->params->get('link_to_product_page','-1')=='-1'){
			$this->params->set('link_to_product_page',@$defaultParams['link_to_product_page']);
		}
		if($this->params->get('only_if_products','-1')=='-1'){
			$this->params->set('only_if_products',@$defaultParams['only_if_products']);
		}
		if($this->params->get('child_display_type','inherit')=='inherit'){
			$this->params->set('child_display_type',@$defaultParams['child_display_type']);
		}
		if($this->params->get('child_limit','')==''){
			$this->params->set('child_limit',@$defaultParams['child_limit']);
		}
		if($this->params->get('links_on_main_categories','-1')=='-1'){
			if(empty($defaultParams['links_on_main_categories']))$defaultParams['links_on_main_categories']=1;
			$this->params->set('links_on_main_categories',@$defaultParams['links_on_main_categories']);
		}
		if($this->params->get('layout_type','inherit')=='inherit'){
			$this->params->set('layout_type',@$defaultParams['layout_type']);
		}

		if($this->params->get('content_synchronize')){
			if(JRequest::getString('option','')==HIKASHOP_COMPONENT){
				if(JRequest::getString('ctrl','category')=='product'){
					$product_id = hikashop_getCID('product_id');
					if(!empty($product_id)){
						$query = 'SELECT category_id FROM '.hikashop_table('product_category').' WHERE product_id='.$product_id;
						$database->setQuery($query);
						if(!HIKASHOP_J25){
							$pageInfo->filter->cid = $database->loadResultArray();
						} else {
							$pageInfo->filter->cid = $database->loadColumn();
						}
					}else{
						$pageInfo->filter->cid = $this->params->get('selectparentlisting');
					}
				}elseif(JRequest::getString('ctrl','category')=='category'){
					$pageInfo->filter->cid = JRequest::getInt("cid",$this->params->get('selectparentlisting'));
				}else{
					$pageInfo->filter->cid = $this->params->get('selectparentlisting');
				}
			}else{
				$pageInfo->filter->cid = $this->params->get('selectparentlisting');
			}
		}else{
			if(empty($this->module)){
				$pageInfo->filter->cid = JRequest::getInt("cid",$this->params->get('selectparentlisting'));
			}else{
				$pageInfo->filter->cid = $this->params->get('selectparentlisting');
			}
		}
		if(!empty($pageInfo->filter->cid)){
			$acl_filters = array();
			hikashop_addACLFilters($acl_filters,'category_access');
			if(!empty($acl_filters)){
				if(!is_array($pageInfo->filter->cid)){
					$category = hikashop_get('class.category');
					$catData = $category->get($pageInfo->filter->cid);
					if(!empty($catData->category_type)) $category_type = $catData->category_type;
					$pageInfo->filter->cid = array($pageInfo->filter->cid);
				}
				$acl_filters[]='category_type=\''.$category_type.'\'';
				$acl_filters[]='category_id IN ('.implode(',',$pageInfo->filter->cid).')';
				$query = 'SELECT category_id FROM '.hikashop_table('category').' WHERE '.implode(' AND ',$acl_filters);
				$database->setQuery($query);
				if(!HIKASHOP_J25){
					$pageInfo->filter->cid = $database->loadResultArray();
				} else {
					$pageInfo->filter->cid = $database->loadColumn();
				}
			}
		}
		if(empty($pageInfo->filter->cid)){
			$pageInfo->filter->cid = 'product';
		}
		$category_selected='';
		if(!is_array($pageInfo->filter->cid)){
			$category_selected = '_'.$pageInfo->filter->cid;
			$this->paramBase.=$category_selected;
		}
		$this->assignRef('category_selected',$category_selected);
		if($this->params->get('category_order','inherit')=='inherit'){
			$defaultParams = $config->get('default_params');
			$this->params->set('category_order',$defaultParams['category_order']);
			if($this->params->get('category_order','inherit')=='inherit'){
				$this->params->set('category_order','category_ordering');
			}
		}

		if($this->params->get('order_dir','inherit')=='inherit' || $this->params->get('order_dir','inherit')==''){
			$defaultParams = $config->get('default_params');
			$this->params->set('order_dir',@$defaultParams['order_dir']);
			if($this->params->get('order_dir','inherit')=='inherit' || $this->params->get('order_dir','inherit')==''){
				$this->params->set('order_dir','ASC');
			}
		}
		$pageInfo->filter->order->value = $app->getUserStateFromRequest( $this->paramBase.".filter_order", 'filter_order_'.$this->params->get('main_div_name').$category_selected,	'a.'.$this->params->get('category_order','category_ordering'),'cmd' );
		$pageInfo->filter->order->dir	= $app->getUserStateFromRequest( $this->paramBase.".filter_order_Dir", 'filter_order_Dir_'.$this->params->get('main_div_name').$category_selected,	$this->params->get('order_dir','ASC'),	'word' );

		$oldValue = $app->getUserState($this->paramBase.'.list_limit');
		if($this->params->get('limit','')==''){
			$defaultParams = $config->get('default_params');
			$this->params->set('limit',$defaultParams['limit']);
		}
		if(empty($oldValue)){
			$oldValue = $this->params->get('limit');
		}
		$pageInfo->limit->value = $app->getUserStateFromRequest( $this->paramBase.'.list_limit', 'limit_'.$this->params->get('main_div_name').$category_selected, $this->params->get('limit'), 'int' );
		if($oldValue!=$pageInfo->limit->value){
			JRequest::setVar('limitstart_'.$this->params->get('main_div_name').$category_selected,0);
		}
		$pageInfo->limit->start = $app->getUserStateFromRequest( $this->paramBase.'.limitstart', 'limitstart_'.$this->params->get('main_div_name').$category_selected, 0, 'int' );

		if(empty($this->module)){
				if($config->get('hikarss_format') != 'none'){
					$doc_title = $config->get('hikarss_name','');
					if(empty($doc_title)){
						if(!isset($catData)){
							$category = hikashop_get('class.category');
							if(is_array($pageInfo->filter->cid)){
								$cat = reset($pageInfo->filter->cid);
							}else{
								$cat = $pageInfo->filter->cid;
							}
							$catData = $category->get($cat);
						}
						if($catData) $doc_title = $catData->category_name;
					}
					if(!HIKASHOP_PHP5) {
						$doc =& JFactory::getDocument();
					} else {
						$doc = JFactory::getDocument();
					}
					if($config->get('hikarss_format') != 'both'){
						$link	= '&format=feed&limitstart=';
						$attribs = array('type' => 'application/rss+xml', 'title' => $doc_title.' RSS 2.0');
						$doc->addHeadLink(JRoute::_($link.'&type='.$config->get('hikarss_format')), 'alternate', 'rel', $attribs);
					}else{
						$link	= '&format=feed&limitstart=';
						$attribs = array('type' => 'application/rss+xml', 'title' => $doc_title.' RSS 2.0');
						$doc->addHeadLink(JRoute::_($link.'&type=rss'), 'alternate', 'rel', $attribs);
						$attribs = array('type' => 'application/atom+xml', 'title' => $doc_title.' Atom 1.0');
						$doc->addHeadLink(JRoute::_($link.'&type=atom'), 'alternate', 'rel', $attribs);

					}
				}

			$cid = JRequest::getInt("cid",0);
			if(empty($cid)){
				JRequest::setVar("no_cid",1);
			}
			if(is_array($pageInfo->filter->cid)){
				JRequest::setVar("cid",reset($pageInfo->filter->cid));
			}else{
				JRequest::setVar("cid",$pageInfo->filter->cid);
			}
			JRequest::setVar('menu_main_category',$this->params->get('selectparentlisting'));
		}

		$searchMap = array('a.category_name','a.category_description','a.category_id');

		$order = '';
		if(!empty($pageInfo->filter->order->value)){
			$order = ' ORDER BY '.$pageInfo->filter->order->value.' '.$pageInfo->filter->order->dir;
		}
		if($this->params->get('random','-1')=='-1'){
			$defaultParams = $config->get('default_params');
			$this->params->set('random',$defaultParams['random']);
		}
		if($this->params->get('random')){
			$order = ' ORDER BY RAND()';
		}

		$class = hikashop_get('class.category');
		$class->parentObject =& $this;
		if($this->params->get('filter_type',2)==2){
			$config =& hikashop_config();
			$defaultParams = $config->get('default_params');
			$this->params->set('filter_type',$defaultParams['filter_type']);
		}
		$rows = $class->getChilds($pageInfo->filter->cid,$this->params->get('filter_type'),$filters,$order,$pageInfo->limit->start,$pageInfo->limit->value,true);

		$pageInfo->elements = new stdClass();
		if(!empty($class->query)){
			$database->setQuery('SELECT COUNT(*) '.$class->query);
			$pageInfo->elements->total = $database->loadResult();
			$pageInfo->elements->page = count($rows);
		}else{
			$pageInfo->elements->total = 0;
			$pageInfo->elements->page = 0;
		}
		if($pageInfo->elements->page){
			$ids = array();
			foreach($rows as $key => $row){
				$ids[(int)$row->category_id]=(int)$row->category_id;
				$class->addAlias($rows[$key]);
			}
			if($this->params->get('number_of_products','-1')=='-1'){
				$defaultParams = $config->get('default_params');
				$this->params->set('number_of_products',@$defaultParams['number_of_products']);
			}
			if($this->params->get('only_if_products','-1')=='-1'){
				$defaultParams = $config->get('default_params');
				$this->params->set('only_if_products',@$defaultParams['only_if_products']);
			}
			$number_of_products = $this->params->get('number_of_products',0) || $this->params->get('only_if_products',0);
			if($this->params->get('child_display_type','inherit')=='inherit'){
				$defaultParams = $config->get('default_params');
				$this->params->set('child_display_type',$defaultParams['child_display_type']);
			}
			if($this->params->get('child_display_type')!='nochild' || $number_of_products ){
				$childs = $class->getChilds($ids,true,array(),$order,0,0,false);
				if(!empty($childs)){
					$this->_associateChilds($rows,$childs);
					foreach($childs as $child){
						$ids[(int)$child->category_id]=(int)$child->category_id;
					}
				}
				if($number_of_products){

					if($content_type=='manufacturer'){
						$database->setQuery('SELECT count(a.product_id) AS number_of_products,a.product_manufacturer_id as category_id FROM '.hikashop_table('product').' AS a WHERE  a.product_published>0 AND a.product_parent_id=0 AND a.product_manufacturer_id IN ('.implode(',',$ids).') GROUP BY a.product_manufacturer_id');
					}else{
						$database->setQuery('SELECT count(a.product_id) AS number_of_products,a.category_id FROM '.hikashop_table('product_category').' AS a INNER JOIN '.hikashop_table('product').' AS b ON a.product_id=b.product_id AND b.product_published>0 AND b.product_parent_id=0 WHERE a.category_id IN ('.implode(',',$ids).') GROUP BY a.category_id');
					}
					$counts = $database->loadObjectList('category_id');
					$this->_getCount($rows,$counts);
				}
			}
		}
		$this->assignRef('rows',$rows);

		$this->assignRef('modules',$this->modules);
		$image=hikashop_get('helper.image');
		$this->assignRef('image',$image);

		$this->assignRef('category_image',$category_image);
		$menu_id = '';
		if(empty($this->module)){
			if(is_array($pageInfo->filter->cid)){
				$pageInfo->filter->cid = reset($pageInfo->filter->cid);
			}
			$element = $class->get($pageInfo->filter->cid,true);
			$fieldsClass = hikashop_get('class.field');
			$fields = $fieldsClass->getFields('frontcomp',$element,'category','checkout&task=state');
			$this->assignRef('fieldsClass',$fieldsClass);
			$this->assignRef('fields',$fields);
			$this->assignRef('element',$element);
			$use_module = $this->params->get('use_module_name');
			$title = $this->params->get('page_title');
			if(empty($title)){
				$title = $this->params->get('title');
			}
			if(empty($use_module) && !empty($element->category_name)){
				$title = $element->category_name;
			}
			if(!empty($element->category_page_title)){
				$page_title = $element->category_page_title;
			}else{
				$page_title = $title;
			}
			if (empty($title)) {
				$page_title = $app->getCfg('sitename');
			}
			elseif ($app->getCfg('sitename_pagetitles', 0) == 1) {
				$page_title = JText::sprintf('JPAGETITLE', $app->getCfg('sitename'), $page_title);
			}
			elseif ($app->getCfg('sitename_pagetitles', 0) == 2) {
				$page_title = JText::sprintf('JPAGETITLE', $page_title, $app->getCfg('sitename'));
			}
			$this->params->set('page_title',$title);
			if(!HIKASHOP_PHP5) {
				$document =& JFactory::getDocument();
			} else {
				$document = JFactory::getDocument();
			}
			if(!empty($element->category_keywords)){
				$document->setMetadata('keywords', $element->category_keywords);
			}
			if(!empty($element->category_meta_description)){
				$document->setMetadata('description', $element->category_meta_description);
			}
			$document->setTitle(strip_tags($page_title));

			$pagination = hikashop_get('helper.pagination', $pageInfo->elements->total, $pageInfo->limit->start, $pageInfo->limit->value);
			$pagination->hikaSuffix = '_'.$this->params->get('main_div_name').$category_selected;
			$this->assignRef('pagination',$pagination);
			$this->params->set('show_limit',1);

			if(!HIKASHOP_PHP5) {
				$pathway =& $app->getPathway();
			} else {
				$pathway = $app->getPathway();
			}

			$categories = $class->getParents($cid,$this->params->get('selectparentlisting'));
			global $Itemid;
			if(!empty($Itemid)){
				$menu_id = '&Itemid='.$Itemid;
			}

			$one = true;
			if(is_array($categories)){
				foreach($categories as $category){
					if($one){
						$one = false;
					}
					else{
						$class->addAlias($category);
						$alias = $category->alias;
						$pathway->addItem($category->category_name,hikashop_completeLink('category&task=listing&cid='.(int)$category->category_id.'&name='.$alias.$menu_id));
					}
				}
			}
		}else{
			$menu_id = $this->params->get('itemid',0);
			if(!empty($menu_id)){
				$menu_id = '&Itemid='.$menu_id;
			}else{
				$menu_id = '';
			}
		}
		$this->assignRef('pageInfo',$pageInfo);
		$this->assignRef('menu_id',$menu_id);
		$this->assignRef('params',$this->params);

	}

	function getLink($cid,$alias){
		global $Itemid;
		$config =& hikashop_config();
		if(empty($this->module) && !empty($Itemid) && $config->get('forward_to_submenus',1)){
			$app = JFactory::getApplication();
			$menus	= $app->getMenu();
			if(!HIKASHOP_J16){
				$query = 'SELECT a.id as itemid FROM `#__menu` as a WHERE a.access = 0 AND a.parent='.(int)$Itemid;
			}else{
				$query = 'SELECT a.id as itemid FROM `#__menu` as a WHERE a.client_id=0 AND a.parent_id='.(int)$Itemid;
			}
			$db = JFactory::getDBO();
			$db->setQuery($query);
			$submenus = $db->loadObjectList();
			foreach($submenus as $submenu){
				$menu	= $menus->getItem($submenu->itemid);
				if(!empty($menu) && !empty($menu->link) && strpos($menu->link,'option='.HIKASHOP_COMPONENT)!==false && (strpos($menu->link,'view=category')!==false || strpos($menu->link,'view=')===false || strpos($menu->link,'view=product')===false)){
					$params = $config->get( 'menu_'.$submenu->itemid );
					if(!empty($params) && $params['selectparentlisting']==$cid){
						return JRoute::_('index.php?option=com_hikashop&Itemid='.$submenu->itemid);
					}
				}
			}
		}
		return hikashop_completeLink('category&task=listing&cid='.$cid.'&name='.$alias.$this->menu_id);
	}

	function _associateChilds(&$rows,&$childs){
		$class = hikashop_get('class.category');
		foreach($rows as $k => $row){
			$rows[$k]->childs = array();
			foreach($childs as $child){
				if($child->category_parent_id==$row->category_id){
					$class->addAlias($child);
					$rows[$k]->childs[]=$child;
				}
			}
			$this->_associateChilds($rows[$k]->childs,$childs);
		}
	}

	function _getCount(&$rows,&$counts){

		foreach($rows as $k => $row){
			$rows[$k]->number_of_products = (int)@$counts[$row->category_id]->number_of_products;
			if(!empty($rows[$k]->childs)){
				$this->_getCount($rows[$k]->childs,$counts);
				foreach($rows[$k]->childs as $child){
					$rows[$k]->number_of_products+=$child->number_of_products;
				}
			}
		}
	}

	function pagination_display($type, $divName, $id, $currentId, $position, $products){
		if($position=='top' || $position=='bottom'){
			if($type=='numbers'){
				echo '<a id="slide_number_'.$divName.'_'.$id.'" class="hikashop_slide_numbers '.($currentId<$products ? ' hikashop_slide_pagination_selected' : '').'" style="cursor:pointer; text-decoration:none">'.($id+1).'</a>';
			}
			if($type=='rounds'){
				echo '<span class="hikashop_slide_dot_basic'.($currentId<$products ? ' hikashop_slide_dot_selected' : '').'" id="slide_number_'.$divName.'_'.$id.'"></span>';
			}
			if($type=='thumbnails'){
				echo '<span class="'.($currentId<$products ? ' hikashop_pagination_images_selected' : 'hikashop_pagination_images').'" id="slide_number_'.$divName.'_'.$id.'">';
			}
			if($type=='names'){
				echo '<span id="slide_number_'.$divName.'_'.$id.'" class="hikashop_slide_numbers '.($currentId<$products ? ' hikashop_slide_pagination_selected' : '').'">';
			}
		}
		else{
			if($type=='numbers'){
				echo '<a id="slide_number_'.$divName.'_'.$id.'" class="hikashop_slide_numbers '.($currentId<$products ? ' hikashop_slide_pagination_selected' : '').'" style="cursor:pointer; text-decoration:none">'.($id+1).'</a><br/>';
			}
			if($type=='rounds'){
				echo '<span class="hikashop_slide_dot_basic'.($currentId<$products ? ' hikashop_slide_dot_selected' : '').'" id="slide_number_'.$divName.'_'.$id.'"></span><br/>';
			}
			if($type=='thumbnails'){
				echo '<span class="'.($currentId<$products ? ' hikashop_pagination_images_selected' : 'hikashop_pagination_images').'" id="slide_number_'.$divName.'_'.$id.'">';
			}
			if($type=='names'){
				echo '<span id="slide_number_'.$divName.'_'.$id.'" class="hikashop_slide_numbers '.($currentId<$products ? ' hikashop_slide_pagination_selected' : '').'">';
			}
		}
	}
}
