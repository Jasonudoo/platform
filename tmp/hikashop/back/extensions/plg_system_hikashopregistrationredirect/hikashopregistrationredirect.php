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
class plgSystemHikashopregistrationredirect extends JPlugin
{
	function plgSystemHikashopregistrationredirect(&$subject, $config){
		parent::__construct($subject, $config);
		if(!isset($this->params)){
			$plugin = JPluginHelper::getPlugin('system', 'hikashopregistrationredirect');
			if(version_compare(JVERSION,'2.5','<')){
				jimport('joomla.html.parameter');
				$this->params = new JParameter($plugin->params);
			} else {
				$this->params = new JRegistry($plugin->params);
			}
		}
	}


	function onAfterRoute(){
		$app = JFactory::getApplication();
		if ($app->isAdmin()) return true;

		if((@$_REQUEST['option']=='com_user' && @$_REQUEST['view']=='register') || (@$_REQUEST['option']=='com_users' && @$_REQUEST['view']=='registration')){
			global $Itemid;
			if(empty($Itemid)){
				$urlItemid = JRequest::getInt('Itemid');
				if($urlItemid){
					$Itemid = $urlItemid;
				}
			}
			$url_itemid = '';
			if(!empty($Itemid)){
				$url_itemid.='&Itemid='.$Itemid;
			}

			$app->redirect(JRoute::_('index.php?option=com_hikashop&ctrl=user&task=form'.$url_itemid));
		}
		return true;
	}

}
