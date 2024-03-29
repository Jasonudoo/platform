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
jimport('joomla.application.component.controller');
jimport('joomla.application.component.view');
jimport('joomla.filesystem.file');

$jversion = preg_replace('#[^0-9\.]#i','',JVERSION);
define('HIKASHOP_J16',version_compare($jversion,'1.6.0','>=') ? true : false);
define('HIKASHOP_J17',version_compare($jversion,'1.7.0','>=') ? true : false);
define('HIKASHOP_J25',version_compare($jversion,'2.5.0','>=') ? true : false);
define('HIKASHOP_J30',version_compare($jversion,'3.0.0','>=') ? true : false);

define('HIKASHOP_PHP5',version_compare(PHP_VERSION,'5.0.0', '>=') ? true : false);

class hikashop{
	function getDate($time = 0,$format = '%d %B %Y %H:%M'){ return hikashop_getDate($time,$format); }
	function isAllowed($allowedGroups,$id=null,$type='user'){ return hikashop_isAllowed($allowedGroups,$id,$type); }
	function addACLFilters(&$filters,$field,$table='',$level=2){ return hikashop_addACLFilters($filters,$field,$table,$level); }
	function currentURL($checkInRequest=''){ return hikashop_currentURL($checkInRequest); }
	function getTime($date){ return hikashop_getTime($date); }
	function getIP(){ return hikashop_getIP(); }
	function encode(&$data,$type='order',$format='') { return hikashop_encode($data,$type,$format); }
	function base($id){ return hikashop_base($id); }
	function decode($str,$type='order') { return hikashop_decode($str,$type); }
	function &array_path(&$array, $path) { return hikashop_array_path($array, $path); }
	function toFloat($val){ return hikashop_toFloat($val); }
	function loadUser($full=false,$reset=false){ return hikashop_loadUser($full,$reset); }
	function getZone($type='shipping'){ return hikashop_getZone($type); }
	function getCurrency(){ return hikashop_getCurrency(); }
	function cleanCart(){ return hikashop_cleanCart(); }
	function import( $type, $name, $dispatcher = null ){ return hikashop_import( $type, $name, $dispatcher); }
	function createDir($dir,$report = true){ return hikashop_createDir($dir,$report); }
	function initModule(){ return hikashop_initModule(); }
	function absoluteURL($text){ return hikashop_absoluteURL($text); }
	function setTitle($name,$picture,$link){ return hikashop_setTitle($name,$picture,$link); }
	function getMenu($title="",$menu_style='content_top'){ return hikashop_getMenu($title,$menu_style); }
	function getLayout($controller,$layout,$params,&$js){ return hikashop_getLayout($controller,$layout,$params,$js); }
	function setExplorer($task,$defaultId=0,$popup=false,$type=''){ return hikashop_setExplorer($task,$defaultId,$popup,$type); }
	function frontendLink($link,$popup = false){ return hikashop_frontendLink($link,$popup); }
	function backendLink($link,$popup = false){ return hikashop_backendLink($link,$popup); }
	function bytes($val) { return hikashop_bytes($val); }
	function display($messages,$type = 'success',$return = false){ return hikashop_display($messages,$type,$return); }
	function completeLink($link,$popup = false,$redirect = false){ return hikashop_completeLink($link,$popup,$redirect); }
	function table($name,$component = true){ return hikashop_table($name,$component); }
	function secureField($fieldName){ return hikashop_secureField($fieldName); }
 	function increasePerf(){ hikashop_increasePerf(); }
	function &config($reload = false){ return hikashop_config($reload); }
	function level($level){ return hikashop_level($level); }
	function footer(){ return hikashop_footer(); }
	function search($searchString,$object,$exclude=''){ return hikashop_search($searchString,$object,$exclude); }
	function get($path){ return hikashop_get($path); }
	function getCID($field = '',$int=true){ return hikashop_getCID($field,$int); }
	function tooltip($desc,$title='', $image='tooltip.png', $name = '',$href='', $link=1){ return hikashop_tooltip($desc,$title, $image, $name,$href, $link); }
	function checkRobots(){ return hikashop_checkRobots(); }
}

function hikashop_getDate($time = 0,$format = '%d %B %Y %H:%M'){
	if(empty($time))
		return '';

	if(is_numeric($format))
		$format = JText::_('DATE_FORMAT_LC'.$format);

	if(HIKASHOP_J16){
		$format = str_replace(array('%A','%d','%B','%m','%Y','%y','%H','%M','%S','%a'),array('l','d','F','m','Y','y','H','i','s','D'),$format);
		return JHTML::_('date',$time,$format,false);
	}

	static $timeoffset = null;
	if($timeoffset === null) {
		$config = JFactory::getConfig();
		$timeoffset = $config->getValue('config.offset');
	}
	return JHTML::_('date',$time- date('Z'),$format,$timeoffset);
}

function hikashop_isAllowed($allowedGroups,$id=null,$type='user'){
	if($allowedGroups == 'all') return true;
	if($allowedGroups == 'none') return false;

	if(!is_array($allowedGroups)) $allowedGroups = explode(',',$allowedGroups);
	if(!HIKASHOP_J16){
		if($type=='user'){
			$my = JFactory::getUser($id);
			if(empty($my->id)){
				$group = 29;
			}else{
				$group = (int)@$my->gid;
			}
		}else{
			$group = $id;
		}
		return in_array($group,$allowedGroups);
	}

	if($type=='user'){
		jimport('joomla.access.access');
		$my = JFactory::getUser($id);
		$config =& hikashop_config();
		$userGroups = JAccess::getGroupsByUser($my->id, (bool)$config->get('inherit_parent_group_access'));
	}else{
		$userGroups = array($id);
	}
	$inter = array_intersect($userGroups,$allowedGroups);
	if(empty($inter)) return false;
	return true;
}

function hikashop_addACLFilters(&$filters, $field, $table='', $level=2, $allowNull=false, $user_id=0){
	if(hikashop_level($level)){
		if(empty($user_id) || (int)$user_id == 0) {
			$my = JFactory::getUser();
		} else {
			$userClass = hikashop_get('class.user');
			$hkUser = $userClass->get($user_id);
			$my = JFactory::getUser($hkUser->user_cms_id);
		}
		if(!HIKASHOP_J16){
			if(empty($my->id)){
				$userGroups = array(29);
			}else{
				$userGroups = array($my->gid);
			}
		}else{
			jimport('joomla.access.access');
			$config =& hikashop_config();
			$userGroups = JAccess::getGroupsByUser($my->id, (bool)$config->get('inherit_parent_group_access'));//$my->authorisedLevels();
		}
		if(!empty($userGroups)){
			if(!empty($table)){
				$table.='.';
			}
			$acl_filters = array($table.$field." = 'all'");
			foreach($userGroups as $userGroup){
				$acl_filters[]=$table.$field." LIKE '%,".(int)$userGroup.",%'";
			}
			if($allowNull){
				$acl_filters[]='ISNULL('.$table.$field.')';
			}
			$filters[]='('.implode(' OR ',$acl_filters).')';
		}
	}
}

function hikashop_currentURL($checkInRequest='',$safe=true){
	if(!empty($checkInRequest)){
		$url = JRequest::getVar($checkInRequest,'');
		if(!empty($url)){
			if(strpos($url,'http')!==0&&strpos($url,'/')!==0){
				if($checkInRequest=='return_url'){
					$url = base64_decode(urldecode($url));
				}elseif($checkInRequest=='url'){
					$url = urldecode($url);
				}
			}
			if($safe){
				$url = str_replace(array('"',"'",'<','>',';'),array('%22','%27','%3C','%3E','%3B'),$url);
			}
			return $url;
		}
	}
	if(!empty($_SERVER["REDIRECT_URL"]) && preg_match('#.*index\.php$#',$_SERVER["REDIRECT_URL"]) && empty($_SERVER['QUERY_STRING'])&&empty($_SERVER['REDIRECT_QUERY_STRING']) && !empty($_SERVER["REQUEST_URI"])){
		$requestUri = $_SERVER["REQUEST_URI"];
	}elseif(!empty($_SERVER["REDIRECT_URL"]) && (isset($_SERVER['QUERY_STRING'])||isset($_SERVER['REDIRECT_QUERY_STRING']))){
		$requestUri = $_SERVER["REDIRECT_URL"];
		if (!empty($_SERVER['REDIRECT_QUERY_STRING'])) $requestUri = rtrim($requestUri,'/').'?'.$_SERVER['REDIRECT_QUERY_STRING'];
		elseif (!empty($_SERVER['QUERY_STRING'])) $requestUri = rtrim($requestUri,'/').'?'.$_SERVER['QUERY_STRING'];
	}elseif(isset($_SERVER["REQUEST_URI"])){
		$requestUri = $_SERVER["REQUEST_URI"];
	}else{
		$requestUri = $_SERVER['PHP_SELF'];
		if (!empty($_SERVER['QUERY_STRING'])) $requestUri = rtrim($requestUri,'/').'?'.$_SERVER['QUERY_STRING'];
	}
	$result = (hikashop_isSSL() ? 'https://' : 'http://').$_SERVER["HTTP_HOST"].$requestUri;
	if($safe){
		$result = str_replace(array('"',"'",'<','>',';'),array('%22','%27','%3C','%3E','%3B'),$result);
	}
	return $result;
}

function hikashop_getTime($date){
	static $timeoffset = null;
	if($timeoffset === null){
		$config = JFactory::getConfig();
		if(!HIKASHOP_J30){
			$timeoffset = $config->getValue('config.offset');
		} else {
			$timeoffset = $config->get('offset');
		}
		if(HIKASHOP_J16){
			$dateC = JFactory::getDate($date,$timeoffset);
			$timeoffset = $dateC->getOffsetFromGMT(true);
		}
	}
	return strtotime($date) - $timeoffset *60*60 + date('Z');
}

function hikashop_getIP(){
	$ip = '';
	if( !empty($_SERVER['HTTP_X_FORWARDED_FOR']) AND strlen($_SERVER['HTTP_X_FORWARDED_FOR'])>6 ){
		$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	}elseif( !empty($_SERVER['HTTP_CLIENT_IP']) AND strlen($_SERVER['HTTP_CLIENT_IP'])>6 ){
		$ip = $_SERVER['HTTP_CLIENT_IP'];
	}elseif(!empty($_SERVER['REMOTE_ADDR']) AND strlen($_SERVER['REMOTE_ADDR'])>6){
		$ip = $_SERVER['REMOTE_ADDR'];
	}

	return strip_tags($ip);
}

function hikashop_isSSL(){
	if((isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS']) && (strtolower($_SERVER['HTTPS']) != 'off')) || $_SERVER['SERVER_PORT'] == 443 ||
		(isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) == 'https') ) {
		return true;
	}else{
		return false;
	}
}

function hikashop_getUpgradeLink($tolevel){
	$config =& hikashop_config();
	return ' <a class="hikaupgradelink" href="'.HIKASHOP_REDIRECT.'upgrade-hikashop-'.$config->get('level').'-to-'.$tolevel.'" target="_blank">'.JText::_('ONLY_FROM_'.strtoupper($tolevel)).'</a>';
}

function hikashop_encode(&$data,$type='order', $format = '') {
	$id = null;
	if(is_object($data)){
		if($type=='order')
			$id = $data->order_id;
		if($type=='invoice')
			$id = $data->order_invoice_id;
	}else{
		$id = $data;
	}
	if(is_object($data) && ($type=='order' || $type=='invoice') && hikashop_level(1)){
		JPluginHelper::importPlugin( 'hikashop' );
		$dispatcher = JDispatcher::getInstance();
		$result='';
		$trigger_name = 'onBefore'.ucfirst($type).'NumberGenerate';
		$dispatcher->trigger($trigger_name, array( &$data, &$result) );
		if(!empty($result)){
			return $result;
		}

		$config =& hikashop_config();
		if(empty($format)) {
			$format = $config->get($type.'_number_format','{automatic_code}');
		}
		if(preg_match('#\{id *(?:size=(?:"|\')(.*)(?:"|\'))? *\}#Ui',$format,$matches)){
			$copy = $id;
			if(!empty($matches[1])){
				$copy = sprintf('%0'.$matches[1].'d', $copy);
			}
			$format = str_replace($matches[0],$copy,$format);
		}
		$matches=null;
		if(preg_match('#\{date *format=(?:"|\')(.*)(?:"|\') *\}#Ui',$format,$matches)){
			$format = str_replace($matches[0],date($matches[1],$data->order_modified),$format);
		}
		if(strpos($format,'{automatic_code}')!==false){
				$format = str_replace('{automatic_code}',hikashop_base($id),$format);
		}
		if(preg_match_all('#\{user ([a-z_0-9]+)\}#i',$format,$matches)){
			if(empty($data->customer)){
				$class = hikashop_get('class.user');
				$data->customer = $class->get($data->order_customer_id);
			}
			foreach($matches[1] as $match){
				if(isset($data->customer->$match)){
					$format = str_replace('{user '.$match.'}',$data->customer->$match,$format);
				}else{
					$format = str_replace('{user '.$match.'}','',$format);
				}
			}
		}
		if(preg_match_all('#\{([a-z_0-9]+)\}#i',$format,$matches)){
			foreach($matches[1] as $match){
				if(isset($data->$match)){
					$format = str_replace('{'.$match.'}',$data->$match,$format);
				}else{
					$format = str_replace('{'.$match.'}','',$format);
				}
			}
		}
		return $format;
	}
	return hikashop_base($id);
}

function hikashop_base($id){
	$base=23;
	$chars='ABCDEFGHJKLMNPQRSTUWXYZ';
	$str = '';
	$val2=(string)$id;
	do {
		$i = $id % $base;
		$str = $chars[$i].$str;
		$id = ($id - $i) / $base;
	} while($id > 0);
	$str2='';
	$size = strlen($val2);
	for($i=0;$i<$size;$i++){
		if(isset($str[$i]))$str2.=$str[$i];
		$str2.=$val2[$i];
	}
	if($i<strlen($str)){
		$str2.=substr($str,$i);
	}
	return $str2;
}

function hikashop_decode($str,$type='order') {
	$config =& hikashop_config();
	if($type=='order' && hikashop_level(1)){
		JPluginHelper::importPlugin( 'hikashop' );
		$dispatcher = JDispatcher::getInstance();
		$result='';
		$dispatcher->trigger( 'onBeforeOrderNumberRevert', array( & $str) );
		if(!empty($result)){
			return $result;
		}

		$format = $config->get('order_number_format','{automatic_code}');
		$format = str_replace(array('^','$','.','[',']','|','(',')','?','*','+'),array('\^','\$','\.','\[','\]','\|','\(','\)','\?','\*','\+'),$format);
		if(preg_match('#\{date *format=(?:"|\')(.*)(?:"|\') *\}#Ui',$format,$matches)){
			$format = str_replace($matches[0],'(?:'.preg_replace('#[a-z]+#i','[0-9a-z]+',$matches[1]).')',$format);
		}
		if(preg_match('#\{id *(?:size=(?:"|\')(.*)(?:"|\'))? *\}#Ui',$format,$matches)){
			$format = str_replace($matches[0],'([0-9]+)',$format);
		}
		if(strpos($format,'{automatic_code}')!==false){
				$format = str_replace('{automatic_code}','([0-9a-z]+)',$format);
		}
		if(preg_match_all('#\{([a-z_0-9]+)\}#i',$format,$matches)){
			foreach($matches[1] as $match){
				if(isset($data->$match)){
					$format = str_replace('{'.$match.'}','.*',$format);
				}else{
					$format = str_replace('{'.$match.'}','',$format);
				}
			}
		}

		$format = str_replace(array('{','}'),array('\{','\}'),$format);

		if(preg_match('#'.$format.'#i',$str,$matches)){
			foreach($matches as $i => $match){
				if($i){
					return ltrim(preg_replace('#[^0-9]#','',$match),'0');
				}
			}
		}
	}
	return preg_replace('#[^0-9]#','',$str);
}

function &hikashop_array_path(&$array, $path) {
	settype($path, 'array');
	$offset =& $array;
	foreach ($path as $index) {
		if (!isset($offset[$index])) {
			return false;
		}
		$offset =& $offset[$index];
	}
	return $offset;
}

function hikashop_toFloat($val){
	if(preg_match_all('#[0-9]+#',$val,$parts) && count($parts[0])>1){
		$dec=array_pop($parts[0]);
		return (float) implode('',$parts[0]).'.'.$dec;
	}
	return (float) $val;
}

function hikashop_loadUser($full=false,$reset=false){
	static $user= null;
	if($reset){
		$user=null;
		return true;
	}
	if(!isset($user)){
		$app = JFactory::getApplication();
		$user_id = (int)$app->getUserState( HIKASHOP_COMPONENT.'.user_id' );
		$class = hikashop_get('class.user');
		if(empty($user_id)){
			$userCMS = JFactory::getUser();
			if(!$userCMS->guest){
				$user_id = $class->getID($userCMS->get('id'));
			}else{
				return $user;
			}
		}

		$user = $class->get($user_id);
	}
	if($full){
		return $user;
	}else{
		return $user->user_id;
	}
}

function hikashop_getZone($type = 'shipping'){
	$app = JFactory::getApplication();
	$shipping_address = $app->getUserState( HIKASHOP_COMPONENT.'.'.$type.'_address',0);
	$zone_id =0;
	if(!empty($shipping_address)){
		$useMainZone=false;
		$id = $app->getUserState( HIKASHOP_COMPONENT.'.shipping_id','');
		if($id){
			$class = hikashop_get('class.shipping');
			$shipping = $class->get($id);
			if(!empty($shipping->shipping_params)) $params = unserialize($shipping->shipping_params);
			$override = 0;

			if(isset($params->shipping_override_address)) {
				$override = (int)$params->shipping_override_address;
			}
			if($override && $type == 'shipping'){
				$config =& hikashop_config();
				$zone_id = explode(',',$config->get('main_tax_zone',$zone_id));
				if(count($zone_id)){
					$zone_id = array_shift($zone_id);
				}else{
					$zone_id=0;
				}
				return $zone_id;
			}
		}

		$addressClass = hikashop_get('class.address');
		$address = $addressClass->get($shipping_address);
		if(!empty($address)){
			$field = 'address_country';
			if(!empty($address->address_state)){
				$field = 'address_state';
			}
			static $zones = array();
			if(empty($zones[$address->$field])){
				$zoneClass = hikashop_get('class.zone');
				$zones[$address->$field] = $zoneClass->get($address->$field);
			}
			if(!empty($zones[$address->$field])){
				$zone_id = $zones[$address->$field]->zone_id;
			}
		}

	}
	if(empty($zone_id)){
		$zone_id =$app->getUserState( HIKASHOP_COMPONENT.'.zone_id', 0 );
		if(empty($zone_id)){
			$config =& hikashop_config();
			$zone_id = explode(',',$config->get('main_tax_zone',$zone_id));
			if(count($zone_id)){
				$zone_id = array_shift($zone_id);
			}else{
				$zone_id=0;
			}
			$app->setUserState( HIKASHOP_COMPONENT.'.zone_id', $zone_id );
		}
	}
	return (int)$zone_id;
}

function hikashop_getCurrency(){
	$config =& hikashop_config();
	$main_currency = (int)$config->get('main_currency',1);
	$app = JFactory::getApplication();
	$currency_id = (int)$app->getUserState( HIKASHOP_COMPONENT.'.currency_id', $main_currency );

	if($currency_id!=$main_currency && !$app->isAdmin()){
		static $checked = array();
		if(!isset($checked[$currency_id])){
			$checked[$currency_id]=true;
			$db = JFactory::getDBO();
			$db->setQuery('SELECT currency_id FROM '.hikashop_table('currency').' WHERE currency_id = '.$currency_id. ' AND ( currency_published=1 OR currency_displayed=1 )');
			$currency_id = $db->loadResult();
		}
	}

	if(empty($currency_id)){
		$app->setUserState( HIKASHOP_COMPONENT.'.currency_id', $main_currency );
		$currency_id=$main_currency;
	}
	return $currency_id;
}

function hikashop_cleanCart(){
	$config =& hikashop_config();
	$period = $config->get('cart_retaining_period');
	$check = $config->get('cart_retaining_period_check_frequency',86400);
	$checked = $config->get('cart_retaining_period_checked',0);
	$max = time()-$check;
	if(!$checked || $checked<$max){
		$query = 'SELECT cart_id FROM '.hikashop_table('cart').' WHERE cart_modified < '.(time()-$period);
		$database = JFactory::getDBO();
		$database->setQuery($query);
		if(!HIKASHOP_J25){
			$ids = $database->loadResultArray();
		} else {
			$ids = $database->loadColumn();
		}
		if(!empty($ids)){
			$query = 'DELETE FROM '.hikashop_table('cart_product').' WHERE cart_id IN ('.implode(',',$ids).')';
			$database->setQuery($query);
			$database->query();
			$query = 'DELETE FROM '.hikashop_table('cart').' WHERE cart_id IN ('.implode(',',$ids).')';
			$database->setQuery($query);
			$database->query();
		}
		$options = array('cart_retaining_period_checked'=>time());
		$config->save($options);
	}
}

function hikashop_import( $type, $name, $dispatcher = null ){
	$type = preg_replace('#[^A-Z0-9_\.-]#i', '', $type);
	$name = preg_replace('#[^A-Z0-9_\.-]#i', '', $name);
	if(!HIKASHOP_J16){
		$path = JPATH_PLUGINS.DS.$type.DS.$name.'.php';
	}else{
		$path = JPATH_PLUGINS.DS.$type.DS.$name.DS.$name.'.php';
	}
	$instance=false;
	if (file_exists( $path )){
		require_once( $path );
		if($type=='editors-xtd') $typeName = 'Button';
		else $typeName = $type;
		$className = 'plg'.$typeName.$name;
		if(class_exists($className)){
			if($dispatcher==null){
				$dispatcher = JDispatcher::getInstance();
			}
			$instance = new $className($dispatcher, array('name'=>$name,'type'=>$type));
		}
	}
	return $instance;
}

function hikashop_createDir($dir,$report = true){
	if(is_dir($dir)) return true;

	jimport('joomla.filesystem.folder');
	jimport('joomla.filesystem.file');

	$indexhtml = '<html><body bgcolor="#FFFFFF"></body></html>';

	if(!JFolder::create($dir)){
		if($report) hikashop_display('Could not create the directly '.$dir,'error');
		return false;
	}
	if(!JFile::write($dir.DS.'index.html',$indexhtml)){
		if($report) hikashop_display('Could not create the file '.$dir.DS.'index.html','error');
	}
	return true;
}

function hikashop_initModule(){
	static $done = false;
	if(!$done){
		$fe = JRequest::getVar('hikashop_front_end_main',0);
		if(empty($fe)){
			$done = true;
			if(!HIKASHOP_PHP5) {
				$lang =& JFactory::getLanguage();
			} else {
				$lang = JFactory::getLanguage();
			}
			$override_path = JLanguage::getLanguagePath(JPATH_ROOT).DS.'overrides'.$lang->getTag().'.override.ini';
			if(version_compare(JVERSION,'1.6','>=')&& file_exists($override_path)){
				$lang->override = $lang->parse($override_path);
			}
			$lang->load(HIKASHOP_COMPONENT,JPATH_SITE);
			if(version_compare(JVERSION,'1.6','<') && file_exists($override_path)){
				$lang->_load($override_path,'override');
			}
		}
	}
	return true;
}

//		//-->";

function hikashop_absoluteURL($text){
	static $mainurl = '';
	if(empty($mainurl)){
		$urls = parse_url(HIKASHOP_LIVE);
		if(!empty($urls['path'])){
			$mainurl = substr(HIKASHOP_LIVE,0,strrpos(HIKASHOP_LIVE,$urls['path'])).'/';
		}else{
			$mainurl = HIKASHOP_LIVE;
		}
	}
	$text = str_replace(array('href="../undefined/','href="../../undefined/','href="../../../undefined//','href="undefined/'),array('href="'.$mainurl,'href="'.$mainurl,'href="'.$mainurl,'href="'.HIKASHOP_LIVE),$text);
	$text = preg_replace('#href="(/?administrator)?/({|%7B)#Uis','href="$2',$text);
	$replace = array();
	$replaceBy = array();
	if($mainurl !== HIKASHOP_LIVE){
		$replace[] = '#(href|src|action|background)[ ]*=[ ]*\"(?!(\{|%7B|\#|[a-z]{3,7}:|/))(?:\.\./)#i';
		$replaceBy[] = '$1="'.substr(HIKASHOP_LIVE,0,strrpos(rtrim(HIKASHOP_LIVE,'/'),'/')+1);
	}
	$replace[] = '#(href|src|action|background)[ ]*=[ ]*\"(?!(\{|%7B|\#|[a-z]{3,7}:|/))(?:\.\./|\./)?#i';
	$replaceBy[] = '$1="'.HIKASHOP_LIVE;
	$replace[] = '#(href|src|action|background)[ ]*=[ ]*\"(?!(\{|%7B|\#|[a-z]{3,7}:))/#i';
	$replaceBy[] = '$1="'.$mainurl;
	$replace[] = '#((background-image|background)[ ]*:[ ]*url\(\'?"?(?!([a-z]{3,7}:|/|\'|"))(?:\.\./|\./)?)#i';
	$replaceBy[] = '$1'.HIKASHOP_LIVE;
	return preg_replace($replace,$replaceBy,$text);
}

function hikashop_setTitle($name,$picture,$link){
	$config =& hikashop_config();
	$menu_style = $config->get('menu_style','title_bottom');
	$html='<a href="'. hikashop_completeLink($link).'">'.$name.'</a>';
	if($menu_style!='content_top'){
		$html=hikashop_getMenu($html,$menu_style);
	}
	JToolBarHelper::title( $html , $picture.'.png' );
}

function hikashop_getMenu($title="",$menu_style='content_top'){
	$document = JFactory::getDocument();
	$controller = new hikashopBridgeController(array('name'=>'menu'));
	$viewType	= $document->getType();
	if(!HIKASHOP_PHP5) {
		$view = & $controller->getView( '', $viewType, '');
	} else {
		$view = $controller->getView( '', $viewType, '');
	}
	$view->setLayout('default');
	ob_start();
	$view->display(null,$title,$menu_style);
	return ob_get_clean();
}

function hikashop_getLayout($controller,$layout,$params,&$js){
	$base_path=HIKASHOP_FRONT;
	$app = JFactory::getApplication();
	if($app->isAdmin()){
		$base_path=HIKASHOP_BACK;
	}
	$base_path=rtrim($base_path,DS);
	$document = JFactory::getDocument();

	$controller = new hikashopBridgeController(array('name'=>$controller,'base_path'=>$base_path));
	$viewType	= $document->getType();
	if(!HIKASHOP_PHP5) {
		$view = & $controller->getView( '', $viewType, '',array('base_path'=>$base_path));
	} else {
		$view = $controller->getView( '', $viewType, '',array('base_path'=>$base_path));
	}

	$folder	= JPATH_BASE.DS.'templates'.DS.$app->getTemplate().DS.'html'.DS.HIKASHOP_COMPONENT.DS.$view->getName();
	$view->addTemplatePath($folder);
	$view->setLayout($layout);
	ob_start();
	$view->display(null,$params);
	$js = @$view->js;
	return ob_get_clean();
}

function hikashop_setExplorer($task,$defaultId=0,$popup=false,$type=''){
	$document = JFactory::getDocument();
	$controller = new hikashopBridgeController(array('name'=>'explorer'));
	$viewType	= $document->getType();
	if(!HIKASHOP_PHP5) {
		$view =& $controller->getView('', $viewType, '');
	} else {
		$view = $controller->getView('', $viewType, '');
	}
	$view->setLayout('default');
	ob_start();
	$view->display(null,$task,$defaultId,$popup,$type);
	return ob_get_clean();
}

function hikashop_frontendLink($link,$popup = false){
	if($popup) $link .= '&tmpl=component';

	$config = hikashop_config();
	$app = JFactory::getApplication();
	if(!$app->isAdmin() && $config->get('use_sef',0)){
		$link = ltrim(JRoute::_($link,false),'/');
	}

	static $mainurl = '';
	static $otherarguments = false;
	if(empty($mainurl)){
		$urls = parse_url(HIKASHOP_LIVE);
		if(isset($urls['path']) AND strlen($urls['path'])>0){
			$mainurl = substr(HIKASHOP_LIVE,0,strrpos(HIKASHOP_LIVE,$urls['path'])).'/';
			$otherarguments = trim(str_replace($mainurl,'',HIKASHOP_LIVE),'/');
			if(strlen($otherarguments) > 0) $otherarguments .= '/';
		}else{
			$mainurl = HIKASHOP_LIVE;
		}
	}

	if($otherarguments AND strpos($link,$otherarguments) === false){
		$link = $otherarguments.$link;
	}

	return $mainurl.$link;
}


function hikashop_backendLink($link,$popup = false){
	static $mainurl = '';
	static $otherarguments = false;
	if(empty($mainurl)){
		$urls = parse_url(HIKASHOP_LIVE);
		if(!empty($urls['path'])){
			$mainurl = substr(HIKASHOP_LIVE,0,strrpos(HIKASHOP_LIVE,$urls['path'])).'/';
			$otherarguments = trim(str_replace($mainurl,'',HIKASHOP_LIVE),'/');
			if(!empty($otherarguments)) $otherarguments .= '/';
		}else{
			$mainurl = HIKASHOP_LIVE;
		}
	}
	if($otherarguments AND strpos($link,$otherarguments) === false){
		$link = $otherarguments.$link;
	}
	return $mainurl.$link;
}

function hikashop_bytes($val) {
	$val = trim($val);
	if(empty($val)){
		return 0;
	}
	$last = strtolower($val[strlen($val)-1]);
	switch($last) {
		case 'g':
		$val *= 1024;
		case 'm':
		$val *= 1024;
		case 'k':
		$val *= 1024;
	}
	return (int)$val;
}

function hikashop_display($messages, $type = 'success', $return = false, $close = true){
	if(empty($messages))
		return;
	if(!is_array($messages))
		$messages = array($messages);
	$app = JFactory::getApplication();
	if(($app->isAdmin() && !HIKASHOP_BACK_RESPONSIVE) || (!$app->isAdmin() && !HIKASHOP_RESPONSIVE)) {
		$html = '<div id="hikashop_messages_'.$type.'" class="hikashop_messages hikashop_'.$type.'"><ul><li>'.implode('</li><li>',$messages).'</li></ul></div>';
	} else {
		$html = '<div class="alert alert-'.$type.' alert-block">'.($close?'<button type="button" class="close" data-dismiss="alert">×</button>':'').'<p>'.implode('</p><p>',$messages).'</p></div>';
	}
	if($return){
		return $html;
	}
	echo $html;
}

function hikashop_completeLink($link,$popup = false,$redirect = false){
	if($popup) $link .= '&tmpl=component';
	return JRoute::_('index.php?option='.HIKASHOP_COMPONENT.'&ctrl='.$link,!$redirect);
}

function hikashop_table($name,$component = true){
	$prefix = $component ? HIKASHOP_DBPREFIX : '#__';
	return $prefix.$name;
}

function hikashop_secureField($fieldName){
	if (!is_string($fieldName) || preg_match('|[^a-z0-9#_.-]|i',$fieldName) !== 0 ){
		die('field "'.$fieldName .'" not secured');
	}
	return $fieldName;
}

function hikashop_increasePerf(){
	@ini_set('max_execution_time',0);
	if(hikashop_bytes(@ini_get('memory_limit')) < 60000000){
		$config = hikashop_config();
		if($config->get('hikaincreasemem','1')){
			if(!empty($_SESSION['hikaincreasemem'])){
				$newConfig = new stdClass();
				$newConfig->hikaincreasemem = 0;
				$config->save($newConfig);
				unset($_SESSION['hikaincreasemem']);
				return;
			}
			if(isset($_SESSION)) $_SESSION['hikaincreasemem'] = 1;
			@ini_set('memory_limit','64M');
			if(isset($_SESSION['hikaincreasemem'])) unset($_SESSION['hikaincreasemem']);
		}
	}
}

function &hikashop_config($reload = false){
	static $configClass = null;
	if($configClass === null || $reload){
		$configClass = hikashop_get('class.config');
		$configClass->load();
	}
	return $configClass;
}

function hikashop_level($level){
	$config =& hikashop_config();
	if($config->get($config->get('level'),0) >= $level) return true;
	return false;
}

function hikashop_footer(){
	$config =& hikashop_config();
	$description = $config->get('description_'.strtolower($config->get('level')),'Joomla!<sup style="font-size:6px">TM</sup> Ecommerce System');
	$link = 'http://www.hikashop.com';
	$aff = $config->get('partner_id');
	if(!empty($aff)){
		$link.='?partner_id='.$aff;
	}
	$text = '<!--  HikaShop Component powered by '.$link.' -->
	<!-- version '.$config->get('level').' : '.$config->get('version').' [1304251818] -->';
	if(!$config->get('show_footer',true)) return $text;
	$text .= '<div class="hikashop_footer" style="text-align:center" align="center"><a href="'.$link.'" target="_blank" title="'.HIKASHOP_NAME.' : '.strip_tags($description).'">'.HIKASHOP_NAME.' ';
	$app= JFactory::getApplication();
	if($app->isAdmin()){
		$text .= $config->get('level').' '.$config->get('version');
	}
	$text .= ', '.$description.'</a></div>';
	return $text;
}

function hikashop_search($searchString,$object,$exclude=''){
	if(empty($object) || is_numeric($object))
		return $object;
	if(is_string($object)){
		return preg_replace('#('.str_replace('#','\#',$searchString).')#i','<span class="searchtext">$1</span>',$object);
	}
	if(is_array($object)){
		foreach($object as $key => $element){
			$object[$key] = hikashop_search($searchString,$element,$exclude);
		}
	}elseif(is_object($object)){
		foreach($object as $key => $element){
			if($key!=$exclude) $object->$key = hikashop_search($searchString,$element,$exclude);
		}
	}
	return $object;
}

function hikashop_get($path){
	list($group,$class) = explode('.',$path);
	if($group=='controller'){
		$className = $class.ucfirst($group);;
	}else{
		$className = 'hikashop'.ucfirst($class).ucfirst($group);
	}
	if(class_exists($className.'Override'))
		$className .= 'Override';
	if(!class_exists($className)) {
		$app = JFactory::getApplication();
		$path = JPATH_THEMES.DS.$app->getTemplate().DS.'html'.DS.'com_hikashop'.DS.'administrator'.DS;
		$override = str_replace(HIKASHOP_BACK, $path, constant(strtoupper('HIKASHOP_'.$group))).$class.'.override.php';

		if(JFile::exists($override)) {
			$originalFile = constant(strtoupper('HIKASHOP_'.$group)).$class.'.php';
			include_once($override);
			$className .= 'Override';
		} else {
			include_once(constant(strtoupper('HIKASHOP_'.$group)).$class.'.php');
		}
	}
	if(!class_exists($className)) return null;

	$args = func_get_args();
	array_shift($args);
	switch(count($args)){
		case 3:
			return new $className($args[0],$args[1],$args[2]);
		case 2:
			return new $className($args[0],$args[1]);
		case 1:
			return new $className($args[0]);
		case 0:
		default:
			return new $className();
	}
}

function hikashop_getCID($field = '',$int=true){
	$oneResult = JRequest::getVar( 'cid', array(), '', 'array' );
	if(is_array($oneResult)) $oneResult = reset($oneResult);
	if(empty($oneResult) && !empty($field)) $oneResult=JRequest::getCmd( $field,0);
	if($int) return intval($oneResult);
	return $oneResult;
}

function hikashop_tooltip($desc,$title='', $image='tooltip.png', $name = '',$href='', $link=1){
	return JHTML::_('tooltip', str_replace(array("'","::"),array("&#039;",":"),$desc),str_replace(array("'",'::'),array("&#039;",':'),$title), $image, str_replace(array("'",'"','::'),array("&#039;","&quot;",':'),$name),$href, $link);
}

function hikashop_checkRobots(){
	if(preg_match('#(libwww-perl|python)#i',@$_SERVER['HTTP_USER_AGENT']))
		die('Not allowed for robots. Please contact us if you are not a robot');
}

function hikashop_loadJslib($name){
	static $loadLibs = array();
	$doc = JFactory::getDocument();
	$name = strtolower($name);
	$ret = false;
	if(isset($loadLibs[$name]))
		return $loadLibs[$name];

	switch($name) {
		case 'mootools':
			if(!HIKASHOP_J30)
				JHTML::_('behavior.mootools');
			else
				JHTML::_('behavior.framework');
			break;
		case 'jquery':
			$doc->addScript(HIKASHOP_JS.'jquery.min.js');
			$doc->addScript(HIKASHOP_JS.'jquery-ui.min.js');
			$ret = true;
			break;
		case 'otree':
			$doc->addScript(HIKASHOP_JS.'otree.js');
			$doc->addStyleSheet(HIKASHOP_CSS.'otree.css');
			$ret = true;
			break;
	}

	$loadLibs[$name] = $ret;
	return $ret;
}

if(!HIKASHOP_J30){
	function hikashop_getFormToken() {
		return JUtility::getToken();
	}
} else {
	function hikashop_getFormToken() {
		return JSession::getFormToken();
	}
}

if(!class_exists('hikashopBridgeController')){
	if(!HIKASHOP_J30){
		class hikashopBridgeController extends JController {}
	} else {
		class hikashopBridgeController extends JControllerLegacy {}
	}
}

class hikashopController extends hikashopBridgeController {
	var $pkey = array();
	var $table = array();
	var $groupMap = '';
	var $groupVal = null;
	var $orderingMap ='';

	var $display = array('listing','show','cancel');
	var $modify_views = array('edit','selectlisting','childlisting','newchild');
	var $add = array('add');
	var $modify = array('apply','save','save2new','store','orderdown','orderup','saveorder','savechild','addchild','toggle');
	var $delete = array('delete','remove');
	var $publish_return_view='listing';

	function __construct($config = array(),$skip=false){
		if(!$skip){
			parent::__construct($config);
			$this->registerDefaultTask('listing');
		}
	}
	function listing(){
		JRequest::setVar( 'layout', 'listing'  );
		return $this->display();
	}
	function show(){
		JRequest::setVar( 'layout', 'show'  );
		return $this->display();
	}
	function edit(){
		JRequest::setVar('hidemainmenu',1);
		JRequest::setVar( 'layout', 'form'  );
		return $this->display();
	}
	function add(){
		JRequest::setVar('hidemainmenu',1);
		JRequest::setVar( 'layout', 'form'  );
		return $this->display();
	}
	function apply(){
		$status = $this->store();
		return $this->edit();
	}
	function save(){
		$this->store();
		return $this->listing();
	}
	function save2new(){
		$this->store(true);
		return $this->edit();
	}
	function orderdown(){
		if(!empty($this->table)&&!empty($this->pkey)&&(empty($this->groupMap)||isset($this->groupVal))&&!empty($this->orderingMap)){
			$orderClass = hikashop_get('helper.order');
			$orderClass->pkey = $this->pkey;
			$orderClass->table = $this->table;
			$orderClass->groupMap = $this->groupMap;
			$orderClass->groupVal = $this->groupVal;
			$orderClass->orderingMap = $this->orderingMap;
			if(!empty($this->main_pkey)){
				$orderClass->main_pkey = $this->main_pkey;
			}
			$orderClass->order(true);
		}
		return $this->listing();
	}
	function orderup(){
		if(!empty($this->table)&&!empty($this->pkey)&&(empty($this->groupMap)||isset($this->groupVal))&&!empty($this->orderingMap)){
			$orderClass = hikashop_get('helper.order');
			$orderClass->pkey = $this->pkey;
			$orderClass->table = $this->table;
			$orderClass->groupMap = $this->groupMap;
			$orderClass->groupVal = $this->groupVal;
			$orderClass->orderingMap = $this->orderingMap;
			if(!empty($this->main_pkey)){
				$orderClass->main_pkey = $this->main_pkey;
			}
			$orderClass->order(false);
		}
		return $this->listing();
	}
	function saveorder(){
		if(!empty($this->table)&&!empty($this->pkey)&&(empty($this->groupMap)||isset($this->groupVal))&&!empty($this->orderingMap)){
			$orderClass = hikashop_get('helper.order');
			$orderClass->pkey = $this->pkey;
			$orderClass->table = $this->table;
			$orderClass->groupMap = $this->groupMap;
			$orderClass->groupVal = $this->groupVal;
			$orderClass->orderingMap = $this->orderingMap;
			if(!empty($this->main_pkey)){
				$orderClass->main_pkey = $this->main_pkey;
			}
			$orderClass->save();
		}
		return $this->listing();
	}

	function store($new=false){
		if(!HIKASHOP_PHP5) {
			$app =& JFactory::getApplication();
		} else {
			$app = JFactory::getApplication();
		}
		$class = hikashop_get('class.'.$this->type);
		$status = $class->saveForm();
		if($status) {
			if(!HIKASHOP_J30)
				$app->enqueueMessage(JText::_( 'HIKASHOP_SUCC_SAVED' ), 'success');
			else
				$app->enqueueMessage(JText::_( 'HIKASHOP_SUCC_SAVED' ));
			if(!$new) JRequest::setVar( 'cid', $status  );
			else JRequest::setVar( 'cid', 0  );
			JRequest::setVar( 'fail', null  );
		} else {
			$app->enqueueMessage(JText::_( 'ERROR_SAVING' ), 'error');
			if(!empty($class->errors)){
				foreach($class->errors as $oneError){
					$app->enqueueMessage($oneError, 'error');
				}
			}
		}
		return $status;
	}

	function remove(){
		$cids = JRequest::getVar( 'cid', array(), '', 'array' );
		$class = hikashop_get('class.'.$this->type);
		$num = $class->delete($cids);
		if($num){
			$app = JFactory::getApplication();
			$app->enqueueMessage(JText::sprintf('SUCC_DELETE_ELEMENTS',count($cids)), 'message');
		}
		return $this->listing();
	}

	function publish(){
		$cid 	= JRequest::getVar( 'cid', array(), 'post', 'array' );
		JArrayHelper::toInteger($cid);
		return $this->_toggle($cid,1);
	}

	function unpublish(){
		$cid 	= JRequest::getVar( 'cid', array(), 'post', 'array' );
		JArrayHelper::toInteger($cid);
		return $this->_toggle($cid,0);
	}

	function _toggle($cid, $publish){
		if (empty( $cid )) {
			JError::raiseWarning( 500, 'No items selected' );
		}
		if(in_array($this->type,array('product','category'))){
			JPluginHelper::importPlugin( 'hikashop' );
			$dispatcher = JDispatcher::getInstance();
			$unset = array();
			$objs = array();
			foreach($cid as $k => $id){
				$element = new stdClass();
				$name = reset($this->toggle);
				$element->$name = $id;
				$publish_name = key($this->toggle);
				$element->$publish_name = (int)$publish;
				$do = true;
				$dispatcher->trigger( 'onBefore'.ucfirst($this->type).'Update', array( & $element, & $do) );
				if(!$do){
					$unset[]=$k;
				}else{
					$objs[$k]=& $element;
				}
			}
			if(!empty($unset)){
				foreach($unset as $u){
					unset($cid[$u]);
				}
			}
		}
		$cids = implode( ',', $cid );
		$db = JFactory::getDBO();
		$query = 'UPDATE '.hikashop_table($this->type) . ' SET '.key($this->toggle).' = ' . (int)$publish . ' WHERE '.reset($this->toggle).' IN ( '.$cids.' )';
		$db->setQuery( $query );
		if (!$db->query()) {
			JError::raiseWarning( 500, $db->getErrorMsg() );
		}elseif(in_array($this->type,array('product','category'))){
			if(!empty($objs)){
				foreach($objs as $element){
					$dispatcher->trigger( 'onAfter'.ucfirst($this->type).'Update', array( & $element ) );
				}
			}
		}
		$task = $this->publish_return_view;
		return $this->$task();
	}

	function getModel($name = '', $prefix = '', $config = array(),$do=false) {
		if($do) return parent::getModel($name, $prefix , $config);
		return false;
	}


	function authorise($task){
		return $this->authorize($task);
	}

	function authorize($task){
		if(!$this->isIn($task,array('modify_views','add','modify','delete','display'))){
			return false;
		}
		if($this->isIn($task,array('modify','delete')) && !JRequest::checkToken('request')){
			return false;
		}

		$app = JFactory::getApplication();
		$name = $this->getName();
		if(!empty($name) && $app->isAdmin()){
			if(hikashop_level(2)){
				$config =& hikashop_config();
				if($this->isIn($task,array('display'))){
					$task = 'view';
				}elseif($this->isIn($task,array('modify_views','add','modify'))){
					$task = 'manage';
				}elseif($this->isIn($task,array('delete'))){
					$task = 'delete';
				}else{
					return true;
				}
				if(!hikashop_isAllowed($config->get('acl_'.$name.'_'.$task,'all'))){
					hikashop_display(JText::_('RESSOURCE_NOT_ALLOWED'),'error');
					return false;
				}
			}
		}

		return true;
	}

	function isIn($task,$lists){
		foreach($lists as $list){
			if(in_array($task,$this->$list)){
				return true;
			}
		}
		return false;
	}

	function execute($task){
		if(substr($task,0,12)=='triggerplug-'){
			JPluginHelper::importPlugin( 'hikashop' );
			$dispatcher =& JDispatcher::getInstance();
			$parts = explode('-',$task,2);
			$event = 'onTriggerPlug'.array_pop($parts);
			$dispatcher->trigger( $event, array( ) );
			return true;
		}

		return parent::execute($task);
	}

	function display($cachable = false, $urlparams = false){
		$config =& hikashop_config();
		$menu_style = $config->get('menu_style','title_bottom');
		if($menu_style=='content_top'){
			$app = JFactory::getApplication();
			if($app->isAdmin() && JRequest::getString('tmpl') !== 'component'){
				echo hikashop_getMenu('',$menu_style);
			}
		}
		return parent::display();
	}
}

class hikashopClass extends JObject{
	var $tables = array();
	var $pkeys = array();
	var $namekeys = array();

	function  __construct( $config = array() ){
		$this->database = JFactory::getDBO();
		return parent::__construct($config);
	}
	function save(&$element){
		$pkey = end($this->pkeys);
		if(empty($pkey)){
			$pkey = end($this->namekeys);
		}elseif(empty($element->$pkey)){
			$tmp = end($this->namekeys);
			if(!empty($tmp)){
				if(!empty($element->$tmp)){
					$pkey = $tmp;
				}else{
					$element->$tmp=$this->getNamekey($element);
					if($element->$tmp===false){
						return false;
					}
				}
			}
		}
		if(!HIKASHOP_J16){
			$obj = new JTable($this->getTable(),$pkey,$this->database);
			$obj->setProperties($element);
		}else{
			$obj =& $element;
		}
		if(empty($element->$pkey)){
			$query = $this->_getInsert($this->getTable(),$obj);
			$this->database->setQuery($query);
			$status = $this->database->query();
		}else{
			if(count((array) $element) > 1){
				$status = $this->database->updateObject($this->getTable(),$obj,$pkey);
			}else{
				$status = true;
			}
		}
		if($status){
			return empty($element->$pkey) ? $this->database->insertid() : $element->$pkey;
		}
		return false;
	}

	function getTable(){
		return hikashop_table(end($this->tables));
	}

	function _getInsert( $table, &$object, $keyName = NULL )
	{
		if(!HIKASHOP_J30){
			$fmtsql = 'INSERT IGNORE INTO '.$this->database->nameQuote($table).' ( %s ) VALUES ( %s ) ';
		} else {
			$fmtsql = 'INSERT IGNORE INTO '.$this->database->quoteName($table).' ( %s ) VALUES ( %s ) ';
		}
		$fields = array();
		foreach (get_object_vars( $object ) as $k => $v) {
			if (is_array($v) or is_object($v) or $v === NULL or $k[0] == '_') {
				continue;
			}
			if(!HIKASHOP_J30){
				$fields[] = $this->database->nameQuote( $k );
				$values[] = $this->database->isQuoted( $k ) ? $this->database->Quote( $v ) : (int) $v;
			} else {
				$fields[] = $this->database->quoteName( $k );
				$values[] = $this->database->Quote( $v );
			}
		}
		return sprintf( $fmtsql, implode( ",", $fields ) ,  implode( ",", $values ) );
	}


	function delete(&$elementsToDelete){
		if(!is_array($elementsToDelete)){
			$elements = array($elementsToDelete);
		}else{
			$elements = $elementsToDelete;
		}

		$isNumeric = is_numeric(reset($elements));
		$strings = array();
		foreach($elements as $key => $val){
			$strings[$key] = $this->database->Quote($val);
		}

		$columns = $isNumeric ? $this->pkeys : $this->namekeys;

		if(empty($columns) || empty($elements)) return false;

		$otherElements=array();
		$otherColumn='';
		foreach($columns as $i => $column){
			if(empty($column)){
				$query = 'SELECT '.($isNumeric?end($this->pkeys):end($this->namekeys)).' FROM '.$this->getTable().' WHERE '.($isNumeric?end($this->pkeys):end($this->namekeys)).' IN ( '.implode(',',$strings).');';
				$this->database->setQuery($query);
				if(!HIKASHOP_J25){
					$otherElements = $this->database->loadResultArray();
				} else {
					$otherElements = $this->database->loadColumn();
				}
				foreach($otherElements as $key => $val){
					$otherElements[$key] = $this->database->Quote($val);
				}
				break;
			}
		}

		$result = true;
		$tables=array();
		if(empty($this->tables)){
			$tables[0]=$this->getTable();
		}else{
			foreach($this->tables as $i => $oneTable){
				$tables[$i]=hikashop_table($oneTable);
			}
		}
		foreach($tables as $i => $oneTable){
			$column = $columns[$i];
			if(empty($column)){
				$whereIn = ' WHERE '.($isNumeric?$this->namekeys[$i]:$this->pkeys[$i]).' IN ('.implode(',',$otherElements).')';
			}else{
				$whereIn = ' WHERE '.$column.' IN ('.implode(',',$strings).')';
			}
			$query = 'DELETE FROM '.$oneTable.$whereIn;
			$this->database->setQuery($query);
			$result = $this->database->query() && $result;
		}
		return $result;
	}

	function get($element,$default=null){
		if(empty($element)) return null;
		$pkey = end($this->pkeys);
		$namekey = end($this->namekeys);
		if(!is_numeric($element) && !empty($namekey)) {
			$pkey = $namekey;
		}
		$query = 'SELECT * FROM '.$this->getTable().' WHERE '.$pkey.'  = '.$this->database->Quote($element).' LIMIT 1';
		$this->database->setQuery($query);
		return $this->database->loadObject();
	}
}


if(!class_exists('hikashopBridgeView')){
	if(!HIKASHOP_J30){
		class hikashopBridgeView extends JView {}
	} else {
		class hikashopBridgeView extends JViewLegacy {}
	}
}

class hikashopView extends hikashopBridgeView {
	var $triggerView = false;
	var $toolbar = array();
	var $direction = 'ltr';

	function display($tpl = null) {

		$lang = JFactory::getLanguage();
		if($lang->isRTL()) $this->direction = 'rtl';

		if($this->triggerView) {
			JPluginHelper::importPlugin('hikashop');
			$dispatcher = JDispatcher::getInstance();
			$dispatcher->trigger('onHikashopBeforeDisplayView', array(&$this));
		}

		if(!empty($this->toolbar)) {
			$toolbarHelper = hikashop_get('helper.toolbar');
			$toolbarHelper->process($this->toolbar);
		}

		parent::display($tpl);

		if($this->triggerView) {
			$dispatcher->trigger('onHikashopAfterDisplayView', array( &$this));
		}
	}
}

JHTML::_('select.booleanlist','hikashop');
if(HIKASHOP_J25){
	class hikaParameter extends JRegistry {
		function get($path, $default = null) {
			$value = parent::get($path, 'noval');
			if($value==='noval') $value = parent::get('data.'.$path,$default);
			return $value;
		}
	}
	class hikaLanguage extends JLanguage {
		function __construct($old = null) {
			if(is_string($old)) {
				$old = JFactory::getLanguage($old);
			}
			if(is_object($old)) {
				$this->strings = $old->strings; $this->override = $old->override; $this->paths = $old->paths;
				$this->metadata = $old->metadata; $this->locale = $old->locale; $this->lang = $old->lang;
				$this->default = $old->default; $this->debug = $old->debug; $this->orphans = $old->orphans;
			}
		}
		function publicLoadLanguage($filename, $extension = 'unknown') {
			if($extension == 'override')
				return $this->reloadOverride($filename);
			return $this->loadLanguage($filename, $extension);
		}
		function reloadOverride($filename = null) {
			$ret = false;
			if(empty($this->lang) && empty($file)) return $ret;
			if(empty($filename))
				$filename = JPATH_BASE.'/language/overrides/'.$this->lang.'.override.ini';
			if(file_exists($filename) && $contents = $this->parse($filename)) {
				if(is_array($contents)) {
					$this->override = $contents;
					$this->strings = array_merge($this->strings, $this->override);
					$ret = true;
				}
				unset($contents);
			}
			return $ret;
		}
	}
	JFactory::$language = new hikaLanguage(JFactory::$language);
}else{
	jimport('joomla.html.parameter');
	class hikaParameter extends JParameter {}
}

define('HIKASHOP_COMPONENT','com_hikashop');
define('HIKASHOP_LIVE',rtrim(JURI::root(),'/').'/');
define('HIKASHOP_ROOT',rtrim(JPATH_ROOT,DS).DS);
define('HIKASHOP_FRONT',rtrim(JPATH_SITE,DS).DS.'components'.DS.HIKASHOP_COMPONENT.DS);
define('HIKASHOP_BACK',rtrim(JPATH_ADMINISTRATOR,DS).DS.'components'.DS.HIKASHOP_COMPONENT.DS);
define('HIKASHOP_HELPER',HIKASHOP_BACK.'helpers'.DS);
define('HIKASHOP_BUTTON',HIKASHOP_BACK.'buttons');
define('HIKASHOP_CLASS',HIKASHOP_BACK.'classes'.DS);
define('HIKASHOP_INC',HIKASHOP_BACK.'inc'.DS);
define('HIKASHOP_VIEW',HIKASHOP_BACK.'views'.DS);
define('HIKASHOP_TYPE',HIKASHOP_BACK.'types'.DS);
define('HIKASHOP_MEDIA',HIKASHOP_ROOT.'media'.DS.HIKASHOP_COMPONENT.DS);
define('HIKASHOP_DBPREFIX','#__hikashop_');
$app = JFactory::getApplication();
$configClass =& hikashop_config();
if(!HIKASHOP_PHP5) {
	$lang =& JFactory::getLanguage();
	$doc =& JFactory::getDocument();
} else {
	$lang = JFactory::getLanguage();
	$doc = JFactory::getDocument();
}

if($configClass->get('bootstrap_design', HIKASHOP_J30)) {
	define('HIKASHOP_RESPONSIVE', true);
} else {
	define('HIKASHOP_RESPONSIVE', false);
}
if($configClass->get('bootstrap_back_design', HIKASHOP_J30)) {
	define('HIKASHOP_BACK_RESPONSIVE', true);
} else {
	define('HIKASHOP_BACK_RESPONSIVE', false);
}

if(HIKASHOP_J30){
	include_once(dirname(__FILE__).DS.'joomla30.php');
	function hikashop_getEscaped($text, $extra = false) {
		$db = JFactory::getDBO();
		return $db->escape($text, $extra);
	}
}else{
	class JHtmlHikaselect extends JHTMLSelect{}
	function hikashop_getEscaped($text, $extra = false) {
		$db = JFactory::getDBO();
		return $db->getEscaped($text, $extra);
	}
}

define('HIKASHOP_RESSOURCE_VERSION', str_replace('.', '', $configClass->get('version')));
if($app->isAdmin()){
	define('HIKASHOP_CONTROLLER',HIKASHOP_BACK.'controllers'.DS);
	define('HIKASHOP_IMAGES','../media/'.HIKASHOP_COMPONENT.'/images/');
	define('HIKASHOP_CSS','../media/'.HIKASHOP_COMPONENT.'/css/');
	define('HIKASHOP_JS','../media/'.HIKASHOP_COMPONENT.'/js/');
	$css_type = 'backend';
	$doc->addScript(HIKASHOP_JS.'hikashop.js?v='.HIKASHOP_RESSOURCE_VERSION);
	$doc->addStyleSheet(HIKASHOP_CSS.'menu.css?v='.HIKASHOP_RESSOURCE_VERSION);
	if(HIKASHOP_J30 && $_REQUEST['option']==HIKASHOP_COMPONENT) JHtml::_('formbehavior.chosen', 'select');
}else{
	define('HIKASHOP_CONTROLLER',HIKASHOP_FRONT.'controllers'.DS);
	define('HIKASHOP_IMAGES',JURI::base(true).'/media/'.HIKASHOP_COMPONENT.'/images/');
	define('HIKASHOP_CSS',JURI::base(true).'/media/'.HIKASHOP_COMPONENT.'/css/');
	define('HIKASHOP_JS',JURI::base(true).'/media/'.HIKASHOP_COMPONENT.'/js/');
	$css_type = 'frontend';
	$doc->addScript(HIKASHOP_JS.'hikashop.js?v='.HIKASHOP_RESSOURCE_VERSION);
	if(HIKASHOP_J30 && $configClass->get('bootstrap_forcechosen', 1)) JHtml::_('formbehavior.chosen', 'select');
}
$css = $configClass->get('css_'.$css_type,'default');
if(!empty($css)){
	$doc->addStyleSheet( HIKASHOP_CSS.$css_type.'_'.$css.'.css?v='.HIKASHOP_RESSOURCE_VERSION);
}

if(!$app->isAdmin()){
	$style = $configClass->get('css_style','');
	if(!empty($style)){
		$doc->addStyleSheet( HIKASHOP_CSS.'style_'.$style.'.css?v='.HIKASHOP_RESSOURCE_VERSION);
	}
}

$override_path = JLanguage::getLanguagePath(JPATH_ROOT).DS.'overrides'.DS.$lang->getTag().'.override.ini';
$lang->load(HIKASHOP_COMPONENT,JPATH_SITE);
if(file_exists($override_path)){
	if(!HIKASHOP_J16){
		$lang->_load($override_path,'override');
	}elseif(HIKASHOP_J25){
		$lang->publicLoadLanguage($override_path,'override');
	}
}

define('HIKASHOP_NAME','HikaShop');
define('HIKASHOP_TEMPLATE',HIKASHOP_FRONT.'templates'.DS);
define('HIKASHOP_URL','http://www.hikashop.com/');
define('HIKASHOP_UPDATEURL',HIKASHOP_URL.'index.php?option=com_updateme&ctrl=update&task=');
define('HIKASHOP_HELPURL',HIKASHOP_URL.'index.php?option=com_updateme&ctrl=doc&component='.HIKASHOP_NAME.'&page=');
define('HIKASHOP_REDIRECT',HIKASHOP_URL.'index.php?option=com_updateme&ctrl=redirect&page=');
if (is_callable("date_default_timezone_set")) date_default_timezone_set(@date_default_timezone_get());

if(!function_exists('bccomp')){
	function bccomp($Num1,$Num2,$Scale=0) {
		if(!preg_match("/^\+?(\d+)(\.\d+)?$/",$Num1,$Tmp1)||
			 !preg_match("/^\+?(\d+)(\.\d+)?$/",$Num2,$Tmp2)) return('0');

		$Num1=ltrim($Tmp1[1],'0');
		$Num2=ltrim($Tmp2[1],'0');

		if(strlen($Num1)>strlen($Num2)) return(1);
		else {
			if(strlen($Num1)<strlen($Num2)) return(-1);

			else {

				$Dec1=isset($Tmp1[2])?rtrim(substr($Tmp1[2],1),'0'):'';
				$Dec2=isset($Tmp2[2])?rtrim(substr($Tmp2[2],1),'0'):'';

				if($Scale!=null) {
					$Dec1=substr($Dec1,0,$Scale);
					$Dec2=substr($Dec2,0,$Scale);
				}

				$DLen=max(strlen($Dec1),strlen($Dec2));

				$Num1.=str_pad($Dec1,$DLen,'0');
				$Num2.=str_pad($Dec2,$DLen,'0');

				for($i=0;$i<strlen($Num1);$i++) {
					if((int)$Num1{$i}>(int)$Num2{$i}) return(1);
					else
						if((int)$Num1{$i}<(int)$Num2{$i}) return(-1);
				}

				return(0);
			}
		}
	}
}
