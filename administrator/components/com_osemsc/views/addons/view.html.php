<?php/**  * @version     5.0 +  * @package        Open Source Membership Control - com_osemsc  * @subpackage    Open Source Access Control - com_osemsc  * @author        Open Source Excellence (R) {@link  http://www.opensource-excellence.com}  * @author        Created on 15-Nov-2010  * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html  *  *  *  This program is free software: you can redistribute it and/or modify  *  it under the terms of the GNU General Public License as published by  *  the Free Software Foundation, either version 3 of the License, or  *  (at your option) any later version.  *  *  This program is distributed in the hope that it will be useful,  *  but WITHOUT ANY WARRANTY; without even the implied warranty of  *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the  *  GNU General Public License for more details.  *  *  You should have received a copy of the GNU General Public License  *  along with this program.  If not, see <http://www.gnu.org/licenses/>.  *  @Copyright Copyright (C) 2010- Open Source Excellence (R)*/defined('_JEXEC') or die("Direct Access Not Allowed");class oseMscViewAddons extends oseMscView {	function display($tpl= null) {		$tmpl = JRequest::getVar('tmpl');
		if (empty($tmpl))
		{
			JRequest::setVar('tmpl', 'component');
		}				oseHTML :: initScript();		$com= OSECPU_PATH_JS.'/com_ose_cpu/extjs';		oseHTML :: script($com.'/grid/SearchField.js');		oseHTML :: script($com.'/grid/limit.js');		oseHTML :: script($com.'/ose/app.msg.js');		oseHTML :: script(OSEMSC_F_URL.'/libraries/init.js', '1.5');		JHTML :: stylesheet('style.css', 'administrator/components/com_osemsc/assets/css/');		$coreaddons= self :: getAddonsSimple('core');		$addons= self :: getAddonsSimple('3rd');		$this->assignRef('coreaddons', $coreaddons);		$this->assignRef('addons', $addons);				$OSESoftHelper= new OSESoftHelper();
		$footer= $OSESoftHelper -> renderOSETM();
		$this->assignRef('footer', $footer);
		$preview_menus= $OSESoftHelper -> getPreviewMenus();
		$this->assignRef('preview_menus', $preview_menus);
		$this->assignRef('OSESoftHelper', $OSESoftHelper);
		
		$title = JText :: _('OSE Membership™ Addon Management');
		$this->assignRef('title', $title);
				parent :: display($tpl);	}	function getAddons() {		return oseMscAddon :: getAddonList('member', true, null, 'obj');	}	function getAddonsSimple($type) {		jimport('joomla.html.html.select');		$addon= array();		switch($type)		{			case "3rd":				$addon[]= self :: genAddon("VirtueMart", "vm", "com_virtuemart");				$addon[]= self :: genAddon("AcyMailing(Link AcyMailing list with membership)", "acymailing", "com_acymailing");				$addon[]= self :: genAddon("AcyMailing(Link AcyMailing list with membership options)", "acymailing2", "com_acymailing");				$addon[]= self :: genAddon("OSE License™ License Keys Addon", "license_keymode", "com_oselic");				//$addon[]= self :: genAddon("OSE License Control User Mode", "license_usermode", "com_oselic");				//$addon[]= self :: genAddon("JomSocial", "jomsocial", "com_community");				$addon[]= self :: genAddon("JSPT (JomSocial Profile Type)", "jspt", "com_xipt");				$addon[]= self :: genAddon("K2", "k2item", "com_k2");				$addon[]= self :: genAddon("K2 Group", "k2group", "com_k2");				$addon[]= self :: genAddon("PhocaDownlaod", "phoca", "com_phocadownload");				//$addon[]= self :: genAddon("OSE Credits Extensions", "credit", "com_ose_credit");				$addon[]= self :: genAddon("PAP (Post Affiliate Pro)", "pap", "3rd_affiliate");				$addon[]= self :: genAddon("iDevAffiliate (Coupon 10% OFF: <font color='red'>OSE10</font>)", "idev", "3rd_affiliate");				$addon[]= self :: genAddon("PHPBB", "phpbb", "3rd_forum");				$addon[]= self :: genAddon("DocMan", "docman", "com_docman");				$addon[]= self :: genAddon("Sobi2", "sobi2", "com_sobi2");				$addon[]= self :: genAddon("SobiPro", "sobipro", "com_sobipro");				$addon[]= self :: genAddon("HWDVideo", "hwdvideo", "com_hwdvideoshare");				$addon[]= self :: genAddon("HWDMedia", "hwdmedia", "com_hwdmediashare");				$addon[]= self :: genAddon("MosetTree", "mtree", "com_mtree");				$addon[]= self :: genAddon("MosetTree Listing Submission", "mtree_submit", "com_mtree");				$addon[]= self :: genAddon("RokDownload", "rokdownload", "com_rokdownloads");				$addon[]= self :: genAddon("Zoo", "zoocat", "com_zoo");				$addon[]= self :: genAddon("OSE Credit", "osecredit", "com_ose_credit");				$addon[]= self :: genAddon("ARI Quiz", "ariquizcat", "com_ariquiz");				$addon[]= self :: genAddon("MailChimp", "mailchimp", "mailchimp");				$addon[]= self :: genAddon("JDownloads", "jdownloads", "com_jdownloads");				$addon[]= self :: genAddon("EasyBlog", "easyblog", "com_easyblog");				$addon[]= self :: genAddon("JoomShopping", "jshopping", "com_jshopping");				$addon[]= self :: genAddon("Community Builder", "cb", "com_comprofiler");				$addon[]= self :: genAddon("OSE Download™", "osedownload", "com_ose_download");				$addon[]= self :: genAddon("Event Booking", "eventbooking", "com_eventbooking");			break;			case "core":				$addon[]= self :: genAddon("Login Box", "login", "com_osemsc");				$addon[]= self :: genAddon("Billing Information", "billinginfo", "com_osemsc");				$addon[]= self :: genAddon("Payment Section", "payment", "com_osemsc");				$addon[]= self :: genAddon("Change Membership Dropdown to Text", "msc_list_var7", "com_osemsc");				$addon[]= self :: genAddon("Hide Membership and Currency Dropdown", "msc_list_var8", "com_osemsc");				$addon[]= self :: genAddon("Coupon", "coupon", "com_osemsc");				$addon[]= self :: genAddon("Renewal Preference", "payment_mode", "com_osemsc");				$addon[]= self :: genAddon("Additional Information", "profile", "com_osemsc");				$addon[]= self :: genAddon("Europa VAT Validation", "billinginfo_var1", "com_osemsc");				$addon[]= self :: genAddon("Credit Card Update (Paypal PRO, Authorize.net and BeanStream ONLY)", "creditcardupdate", "com_osemsc");				$addon[]= self :: genAddon("Payment Method Control", "hidepayment", "com_osemsc");				$addon[]= self :: genAddon("Different Custom Fields for Different Memberships (Need to enable \"Change Membership Dropdown to Text\")", "profilecontrol", "com_osemsc");				$addon[]= self :: genAddon("Merge \"My Account Information\" and \"Billing Information\" for Back-end Member Management)", "juserbill", "com_osemsc");				$addon[]= self :: genAddon("Facebook Login Box", "login_fb", "com_osemsc");			break;		}		return $addon;	}	function getOptions($status, $id, $folder) {		$installed= self :: checkAddonInstalled($id, $folder);		if($installed) {			$return= array();			$return[0]= '<option value ="" >'.JText :: _("PLEASE_SELECT_AN_OPTION").'</option>';			$return[1]= '<option value ="0" >'.JText :: _("DISABLED").'</option>';			$return[2]= '<option value ="1" >'.JText :: _("ENABLED").'</option>';			$returnHtml= '<select name ="'.$id.'" id ="'.$id.'" onChange="updateAddon(\''.$id.'\')">'.implode("\n", $return).'</select>';		} else {			$returnHtml= JText :: _("THIS_COMPONENT_IS_NOT_INSTALLED");		}		return $returnHtml;	}	function checkAddonInstalled($id, $folder) {		if(preg_match("/3rd/ms", $folder)) {			$folder= str_replace("3rd_", "", JPATH_SITE.DS.$folder);			return true;		}		elseif (in_array($folder, array("com_xipt")))		{			return (boolean) file_exists(JPATH_ADMINISTRATOR.DS.'components'.DS.$folder.DS.'xipt.php');		}		elseif (in_array($folder, array("com_rokdownloads")))		{			return (boolean) file_exists(JPATH_ADMINISTRATOR.DS.'components'.DS.$folder.DS.'admin.rokdownloads.php');		}		elseif (in_array($folder, array("com_zoo")))		{			return (boolean) file_exists(JPATH_ADMINISTRATOR.DS.'components'.DS.$folder.DS.'zoo.php');		}		elseif (in_array($folder, array("com_ariquiz")))		{			return (boolean) file_exists(JPATH_ADMINISTRATOR.DS.'components'.DS.$folder.DS.'admin.ariquiz.php');		}		elseif (in_array($folder, array("com_sobi2")))		{			return (boolean) file_exists(JPATH_ADMINISTRATOR.DS.'components'.DS.$folder.DS.'admin.sobi2.php');		}elseif (in_array($folder, array("com_sobipro")))		{			return (boolean) file_exists(JPATH_ADMINISTRATOR.DS.'components'.DS.$folder.DS.'sobipro.php');		}elseif (in_array($folder, array("com_jshopping")))		{			return (boolean) file_exists(JPATH_ADMINISTRATOR.DS.'components'.DS.$folder.DS.'admin.jshopping.php');		}elseif (in_array($folder, array("com_comprofiler")))		{			return (boolean) file_exists(JPATH_ADMINISTRATOR.DS.'components'.DS.$folder.DS.'admin.comprofiler.php');		}elseif (in_array($folder, array("com_hwdmediashare")))		{			return (boolean) file_exists(JPATH_ADMINISTRATOR.DS.'components'.DS.$folder.DS.'hwdmediashare.php');		}elseif($folder == 'mailchimp')		{			return true;		}		else {			if (file_exists(JPATH_ADMINISTRATOR.DS.'components'.DS.$folder.DS.'install.'.str_replace("com_", "", $folder).'.php') || file_exists(JPATH_ADMINISTRATOR.DS.'components'.DS.$folder.DS.'admin.'.str_replace("com_", "", $folder).'.php') || file_exists(JPATH_ADMINISTRATOR.DS.'components'.DS.$folder.DS.str_replace("com_", "", $folder).'.php'))			{				return true;			}			else			{				return false;			}		}	}	function genAddon($addonName, $addonID, $folder) {		// name -> addon name; status --> whether it is enabled or not;		$addon= new stdClass();		$addon->name= JText :: _($addonName);		$db= oseDB :: instance();		$tempID = '';		if ($addonID =='license_keymode' || $addonID =='license_usermode')		{			$tempID = $addonID;			$addonID = ($addonID =='license_keymode')?"licuser":"lic_msc";		}		if ($addonID=='payment')		{			$query= "SELECT count(*) FROM `#__osemsc_addon` WHERE `name`= '{$addonID}' AND (`addon_name` = 'oseMscAddon.payment' AND `frontend` = 1 AND `frontend_enabled` = 1 AND `type` = 'registerOS_body')";		}		elseif ($addonID=='billinginfo')		{			$query= "SELECT count(*) FROM `#__osemsc_addon` WHERE `name`= '{$addonID}' AND (`addon_name` = 'oseMscAddon.billinginfo' AND `frontend` = 1 AND `frontend_enabled` = 1 AND `type` = 'registerOS_body')";		}elseif($addonID =='vm')		{			$query= "SELECT count(*) FROM `#__osemsc_addon` WHERE (`name`= 'vm' OR `name` = 'vm2') AND (`backend_enabled` = 1 OR `frontend_enabled` = 1 )";		}		else		{			$query= "SELECT count(*) FROM `#__osemsc_addon` WHERE `name`= '{$addonID}' AND (`backend_enabled` = 1 OR `frontend_enabled` = 1 )";		}		$db->setQuery($query);		$result= $db->loadResult();		$addon->status=(!empty($result)) ? "1" : 0;		if ($addonID =='licuser' || $addonID =='lic_msc')		{			$addonID = $tempID ;		}		if($addonID == 'mtree_submit')		{			if (file_exists(JPATH_SITE.DS.'components'.DS.'com_mtree'.DS.'mtree.php.obk') )			{				$addon->status = true;			}			else			{				$addon->status = false;			}		}		$addon->options= self :: getOptions($addon->status, "addon_".$addonID, $folder);		return $addon;	}}?>