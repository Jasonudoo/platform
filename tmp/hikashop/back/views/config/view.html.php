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
class configViewConfig extends hikashopView
{
	var $triggerView = true;

	function display($tpl = null)
	{
		$this->paramBase = HIKASHOP_COMPONENT.'.'.$this->getName();
		$function = $this->getLayout();
		if(method_exists($this,$function)) $this->$function();
		parent::display($tpl);
	}

	function config($tpl = null)
	{
		JHTML::_('behavior.modal');
		$config =& hikashop_config();
		$toggleClass = hikashop_get('helper.toggle');
		hikashop_setTitle(JText::_('HIKA_CONFIGURATION'),'config','config');
		$manage = hikashop_isAllowed($config->get('acl_config_manage','all'));
		$this->assignRef('manage',$manage);

		$this->toolbar = array(
			'|',
			array('name' => 'save', 'display' => $manage),
			array('name' => 'apply', 'display' => $manage),
			'close',
			'|',
			array('name' => 'pophelp', 'target' => 'config'),
			'dashboard'
		);

		$elements = new stdClass();
		if (!HIKASHOP_PHP5) {
			$lg =& JFactory::getLanguage();
		}else{
			$lg = JFactory::getLanguage();
		}
		$language = $lg->getTag();
		$styleRemind= 'float:right;margin-right:30px;position:relative;';
		$loadLink = '<a onclick="window.document.getElementById(\'hikashop_messages_warning\').style.display = \'none\';return true;" class="modal" rel="{handler: \'iframe\', size: {x: 800, y: 500}}" href="index.php?option=com_hikashop&amp;tmpl=component&amp;ctrl=config&amp;task=latest&amp;code='.$language.'">'.JText::_('LOAD_LATEST_LANGUAGE').'</a>';
		if(!file_exists(HIKASHOP_ROOT.'language'.DS.$language.DS.$language.'.com_hikashop.ini')){
			if($config->get('errorlanguagemissing',1)){
				$notremind = '<small style="'.$styleRemind.'">'.$toggleClass->delete('hikashop_messages_warning','errorlanguagemissing-0','config',false,JText::_('DONT_REMIND')).'</small>';
				hikashop_display(JText::_('MISSING_LANGUAGE').' '.$loadLink.' '.$notremind,'warning');
			}
		}elseif(version_compare(JText::_('HIKA_LANG_VERSION'),$config->get('version'),'<')){
			if($config->get('errorlanguageupdate',1)){
				$notremind = '<small style="'.$styleRemind.'">'.$toggleClass->delete('hikashop_messages_warning','errorlanguageupdate-0','config',false,JText::_('DONT_REMIND')).'</small>';
				hikashop_display(JText::_('UPDATE_LANGUAGE').' '.$loadLink.' '.$notremind,'warning');
			}
		}

		$elements->add_names = JHTML::_('hikaselect.booleanlist', "config[add_names]" , '',$config->get('add_names',true) );
		$elements->embed_images = JHTML::_('hikaselect.booleanlist', "config[embed_images]" , '',$config->get('embed_images',0) );
		$elements->embed_files = JHTML::_('hikaselect.booleanlist', "config[embed_files]" , '',$config->get('embed_files',1) );
		$elements->multiple_part = JHTML::_('hikaselect.booleanlist', "config[multiple_part]" , '',$config->get('multiple_part',0) );
		$encoding = hikashop_get('type.encoding');
		$elements->encoding_format = $encoding->display("config[encoding_format]",$config->get('encoding_format','base64'));
		$charset = hikashop_get('type.charset');
		$elements->charset = $charset->display("config[charset]",$config->get('charset','UTF-8'));
		$editorType = hikashop_get('type.editor');
		$elements->editor = $editorType->display('config[editor]',$config->get('editor'));
		$elements->show_footer = JHTML::_('hikaselect.booleanlist', "config[show_footer]" , '',$config->get('show_footer',1) );

		$cssFiles = hikashop_get('type.css');
		$cssFiles->type = 'frontend';
		$elements->css_frontend = $cssFiles->display('config[css_frontend]',$config->get('css_frontend','default'));
		$cssFiles->type = 'backend';
		$elements->css_backend = $cssFiles->display('config[css_backend]',$config->get('css_backend','default'));
		$cssFiles->type = 'style';
		$elements->css_style = $cssFiles->display('config[css_style]',$config->get('css_style',''));
		$menuType = hikashop_get('type.menus');
		$elements->hikashop_menu = $menuType->display('config[checkout_itemid]',$config->get('checkout_itemid','0'));
		$popup = hikashop_get('helper.popup');
		$this->assignRef('popup', $popup);

		if(hikashop_level(1)){
			$cronTypeReport = hikashop_get('type.cronreport');
			$elements->cron_sendreport = $cronTypeReport->display('config[cron_sendreport]',$config->get('cron_sendreport',2));
			$cronTypeReportSave = hikashop_get('type.cronreportsave');
			$elements->cron_savereport = $cronTypeReportSave->display('config[cron_savereport]',$config->get('cron_savereport',0));
			$elements->deleteReport = $popup->display(
				JText::_('REPORT_DELETE'),
				'REPORT_DELETE',
				hikashop_completeLink('config&task=cleanreport',true),
				'deleteReport',
				760, 480, '', '', 'button'
			);
			$elements->seeReport = $popup->display(
				JText::_('REPORT_SEE'),
				'REPORT_SEE',
				hikashop_completeLink('config&task=seereport',true),
				'seeReport',
				760, 480, '', '', 'button'
			);
			$elements->editReportEmail = $popup->display(
				JText::_('REPORT_EDIT'),
				'REPORT_EDIT',
				hikashop_completeLink('email&task=edit&mail_name=cron_report',true),
				'editReportEmail',
				760, 480, '', '', 'button'
			);
			$delayType = hikashop_get('type.delay');
			$elements->cron_frequency = $delayType->display('config[cron_frequency]',$config->get('cron_frequency',0),0);
			$elements->cron_url = HIKASHOP_LIVE.'index.php?option=com_hikashop&ctrl=cron';
			$item = $config->get('itemid');
			if(!empty($item)) $elements->cron_url.= '&Itemid='.$item;
			$elements->cron_edit = $popup->display(
				JText::_('CREATE_CRON'),
				'CREATE_CRON',
				'http://www.hikashop.com/index.php?option=com_updateme&ctrl=launcher&task=edit&cronurl='.urlencode($elements->cron_url),
				'cron_edit',
				760, 480, '', '', 'button'
			);
		}

		jimport('joomla.filesystem.folder');
		$path = JLanguage::getLanguagePath(JPATH_ROOT);
		$dirs = JFolder::folders( $path );
		$edit_image = HIKASHOP_IMAGES.'icons/icon-16-edit.png';
		$new_image = HIKASHOP_IMAGES.'icons/icon-16-new.png';

		foreach ($dirs as $dir){
			$xmlFiles = JFolder::files( $path.DS.$dir, '^([-_A-Za-z]*)\.xml$' );
			$xmlFile = array_pop($xmlFiles);
			if($xmlFile=='install.xml') $xmlFile = array_pop($xmlFiles);
			if(empty($xmlFile)) continue;
			$data = JApplicationHelper::parseXMLLangMetaFile($path.DS.$dir.DS.$xmlFile);
			$oneLanguage = new stdClass();
			$oneLanguage->language 	= $dir;
			$oneLanguage->name = $data['name'];
			$languageFiles = JFolder::files( $path.DS.$dir, '^(.*)\.com_hikashop\.ini$' );
			$languageFile = reset($languageFiles);

			if(!empty($languageFile)){
				$oneLanguage->edit = $popup->display(
					'<img id="image'.$oneLanguage->language.'" src="'. $edit_image.'" alt="'.JText::_('EDIT_LANGUAGE_FILE').'"/>',
					'EDIT_LANGUAGE_FILE',
					'index.php?option=com_hikashop&amp;tmpl=component&amp;ctrl=config&amp;task=language&amp;code='.$oneLanguage->language,
					'edit_language_'.$oneLanguage->language,
					760, 480, '', '', 'link'
				);
			}else{
				$oneLanguage->edit = $popup->display(
					'<img id="image'.$oneLanguage->language.'" src="'. $new_image.'" alt="'.JText::_('ADD_LANGUAGE_FILE').'"/>',
					'ADD_LANGUAGE_FILE',
					'index.php?option=com_hikashop&amp;tmpl=component&amp;ctrl=config&amp;task=language&amp;code='.$oneLanguage->language,
					'edit_language_'.$oneLanguage->language,
					760, 480, '', '', 'link'
				);
			}

			$languages[] = $oneLanguage;
		}

		$db = JFactory::getDBO();
		if(version_compare(JVERSION,'1.6','<')){
			$db->setQuery("SELECT name,published,id FROM `#__plugins` WHERE `folder` = 'hikashop' ||
		(`folder` != 'hikashoppayment' AND `folder` != 'hikashopshipping' AND `element` LIKE '%hikashop%') ORDER BY published DESC, ordering ASC");
		}else{
			$db->setQuery("SELECT name,enabled as published,extension_id as id FROM `#__extensions` WHERE (`folder` = 'hikashop' ||
		(`folder` != 'hikashoppayment' AND `folder` != 'hikashopshipping' AND `element` LIKE '%hikashop%')) AND type='plugin' ORDER BY enabled DESC, ordering ASC");
		}
		$plugins = $db->loadObjectList();
		$this->assignRef('config',$config);
		$this->assignRef('languages',$languages);
		$this->assignRef('elements',$elements);
		$this->assignRef('plugins',$plugins);

		$app = JFactory::getApplication();
		$defaultPanel = $app->getUserStateFromRequest( $this->paramBase.'.default_panel', 'default_panel', 0, 'int' );
		$this->assignRef('default_tab', $defaultPanel);

		$tabs = hikashop_get('helper.tabs');
		$this->assignRef('tabs', $tabs);

		$this->assignRef('toggleClass', $toggleClass);

		$pluginClass = hikashop_get('class.plugins');
		$plugin = JPluginHelper::getPlugin('system', 'hikashopaffiliate');
		if(empty($plugin)){
			$affiliate_active = false;
			$plugin= new stdClass();
			$plugin->params=array();
		}else{
			$affiliate_active = true;
			$plugin = $pluginClass->getByName($plugin->type,$plugin->name);
		}
		if(empty($plugin->params['partner_key_name'])){
			$plugin->params['partner_key_name']='partner_id';
		}

		$js = "
	function jSelectArticle(id, title, object) {
		document.getElementById('affiliate_terms').value = id;
		hikashop.closeBox();
	}
	function setVisible(value){
		value=parseInt(value);
		if(value==1){
			document.getElementById('sef_cat_name').style.display = '';
			document.getElementById('sef_prod_name').style.display = '';
		}else{
			document.getElementById('sef_cat_name').style.display = 'none';
			document.getElementById('sef_prod_name').style.display = 'none';
		}
	}
	function displaySslField(){
		if(document.getElementById('config_force_sslurl').checked==true){
			document.getElementById('force_ssl_url').style.display='';
		}else{
			document.getElementById('force_ssl_url').style.display='none';
		}
	}
";
		if (!HIKASHOP_PHP5) {
			$doc =& JFactory::getDocument();
		}else{
			$doc = JFactory::getDocument();
		}
		$doc->addScriptDeclaration($js);
		$this->assignRef('affiliate_params',$plugin->params);
		$this->assignRef('affiliate_active',$affiliate_active);
		$rates_active = false;
		if(hikashop_level(1)){
			$plugin = $pluginClass->getByName('hikashop','rates');
			if(!empty($plugin)){
				$rates_active = true;
				$this->assignRef('rates_params',$plugin->params);
			}
		}
		$this->assignRef('rates_active',$rates_active);
		$selectType = hikashop_get('type.select');
		$this->assignRef('auto_select',$selectType);
		$contactType = hikashop_get('type.contact');
		$this->assignRef('contact',$contactType);
		$waitlistType = hikashop_get('type.waitlist');
		$this->assignRef('waitlist',$waitlistType);
		$compareType = hikashop_get('type.compare');
		$this->assignRef('compare',$compareType);
		$delayTypeRates = hikashop_get('type.delay');
		$this->assignRef('delayTypeRates',$delayTypeRates);
		$delayTypeCarts = hikashop_get('type.delay');
		$this->assignRef('delayTypeCarts',$delayTypeCarts);
		$delayTypeRetaining = hikashop_get('type.delay');
		$this->assignRef('delayTypeRetaining',$delayTypeRetaining);
		$delayTypeDownloads = hikashop_get('type.delay');
		$this->assignRef('delayTypeDownloads',$delayTypeDownloads);
		$delayTypeAffiliate = hikashop_get('type.delay');
		$this->assignRef('delayTypeAffiliate',$delayTypeAffiliate);
		$delayTypeOrder = hikashop_get('type.delay');
		$this->assignRef('delayTypeOrder',$delayTypeOrder);
		$delayTypeClick = hikashop_get('type.delay');
		$this->assignRef('delayTypeClick',$delayTypeClick);
		$csvType = hikashop_get('type.csv');
		$this->assignRef('csvType',$csvType);
		if(hikashop_level(1)){
			$registration = hikashop_get('type.registration');
			$this->assignRef('registration',$registration);
		}
		$discountDisplayType = hikashop_get('type.discount_display');
		$this->assignRef('discountDisplayType',$discountDisplayType);

		$currencyType = hikashop_get('type.currency');
		$this->assignRef('currency',$currencyType);
		$tax = hikashop_get('type.tax');
		$this->assignRef('tax',$tax);
		$tax_zone = hikashop_get('type.tax_zone');
		$this->assignRef('tax_zone',$tax_zone);
		$zoneClass = hikashop_get('class.zone');
		$zone = $zoneClass->get($config->get('main_tax_zone'));
		$this->assignRef('zone',$zone);
		$currency = hikashop_get('type.currency');
		$this->assignRef('currency',$currency);
		$order_status = hikashop_get('type.order_status');
		$this->assignRef('order_status',$order_status);
		$button = hikashop_get('type.button');
		$this->assignRef('button',$button);
		$pagination = hikashop_get('type.pagination');
		$this->assignRef('paginationType',$pagination);
		$menu_style = hikashop_get('type.menu_style');
		$this->assignRef('menu_style',$menu_style);
		$vat = hikashop_get('type.vat');
		$this->assignRef('vat',$vat);
		$checkout = hikashop_get('type.checkout');
		$this->assignRef('checkout',$checkout);
		$cart_redirect = hikashop_get('type.cart_redirect');
		$this->assignRef('cart_redirect',$cart_redirect);
		$multilang = hikashop_get('type.multilang');
		$this->assignRef('multilang',$multilang);

		$js = null;
		$this->assignRef('js',$js);
		$contentType = hikashop_get('type.content');
		$this->assignRef('contentType',$contentType);
		$layoutType = hikashop_get('type.layout');
		$this->assignRef('layoutType',$layoutType);
		$default_params=$config->get('default_params',null);
		$orderdirType = hikashop_get('type.orderdir');
		$this->assignRef('orderdirType',$orderdirType);
		$childdisplayType = hikashop_get('type.childdisplay');
		$this->assignRef('childdisplayType',$childdisplayType);
		if(empty($default_params['selectparentlisting'])){
			$query = 'SELECT category_id FROM '.hikashop_table('category').' WHERE category_type=\'root\' AND category_parent_id=0 LIMIT 1';
			$db->setQuery($query);
			$root = $db->loadResult();
			$query = 'SELECT category_id FROM '.hikashop_table('category').' WHERE category_type=\'product\' AND category_parent_id='.$root.' LIMIT 1';
			$db->setQuery($query);
			$default_params['selectparentlisting'] = $db->loadResult();
		}
		$this->assignRef('default_params',$default_params);
		$class=hikashop_get('class.category');
		$element = $class->get($default_params['selectparentlisting']);
		$this->assignRef('element',$element);
		$orderType = hikashop_get('type.order');
		$this->assignRef('orderType',$orderType);
		$pricetaxType = hikashop_get('type.pricetax');
		$this->assignRef('pricetaxType',$pricetaxType);
		$colorType = hikashop_get('type.color');
		$this->assignRef('colorType',$colorType);
		$listType = hikashop_get('type.list');
		$this->assignRef('listType',$listType);
		$itemType = hikashop_get('type.item');
		$this->assignRef('itemType',$itemType);
		if(hikashop_level(2)){
			$filterButtonType = hikashop_get('type.filter_button_position');
			$this->assignRef('filterButtonType',$filterButtonType);
		}
		$priceDisplayType = hikashop_get('type.pricedisplay');
		$this->assignRef('priceDisplayType',$priceDisplayType);
		$image = hikashop_get('helper.image');
		$this->assignRef('image',$image);
		$toggle = hikashop_get('helper.toggle');
		$this->assignRef('toggle',$toggle);
		$characteristicdisplayType = hikashop_get('type.characteristicdisplay');
		$this->assignRef('characteristicdisplayType',$characteristicdisplayType);
		$characteristicorderType = hikashop_get('type.characteristicorder');
		$this->assignRef('characteristicorderType',$characteristicorderType);
		$quantity = hikashop_get('type.quantity');
		$this->assignRef('quantity',$quantity);
		$productSyncType = hikashop_get('type.productsync');
		$this->assignRef('productSyncType',$productSyncType);

		$productDisplayType = hikashop_get('type.productdisplay');
		$this->assignRef('productDisplayType',$productDisplayType);

		$images = array('icon-48-user.png'=>'header','icon-48-category.png'=>'header','icon-32-save.png'=>'toolbar','icon-32-new.png'=>'toolbar','icon-32-apply.png'=>'toolbar','icon-32-print.png'=>'toolbar','icon-32-edit.png'=>'toolbar','icon-32-help.png'=>'toolbar','icon-32-cancel.png'=>'toolbar','icon-32-back.png'=>'toolbar');
		jimport('joomla.filesystem.file');

		$hikarss_format = array(
			JHTML::_('select.option', 'none', JText::_('NO_FEED') ),
			JHTML::_('select.option', 'rss', JText::_('RSS_ONLY') ),
			JHTML::_('select.option', 'atom', JText::_('ATOM_ONLY')),
			JHTML::_('select.option', 'both', JText::_('ALL_FEED') )
		);
		$elements->hikarss_format =  JHTML::_('hikaselect.genericlist', $hikarss_format, "config[hikarss_format]" , 'size="1"', 'value', 'text', $config->get('hikarss_format','both'));

		$hikarss_order = array(
			JHTML::_('select.option', 'product_sale_start',  JText::_('PRODUCT_SALE_START' )),
			JHTML::_('select.option', 'product_id', 'ID' ),
			JHTML::_('select.option', 'product_created', JText::_('ORDER_CREATED') ),
			JHTML::_('select.option', 'product_modified', JText::_('HIKA_LAST_MODIFIED') )
		);
		$elements->hikarss_order =  JHTML::_('hikaselect.genericlist', $hikarss_order, "config[hikarss_order]" , 'size="1"', 'value', 'text', $config->get('hikarss_order','product_id'));
		$elements->hikarss_child =  JHTML::_('hikaselect.booleanlist', "config[hikarss_child]" , 'size="1"', $config->get('hikarss_child','yes'));

		if(version_compare(JVERSION,'1.6','<')){
			$from = HIKASHOP_ROOT.DS.'images'.DS.'M_images'.DS.'edit.png';
			$to = HIKASHOP_MEDIA.'images'.DS.'icons'.DS.'icon-16-edit.png';
			if(!file_exists($to) AND file_exists($from)){
				if(!JFile::copy($from,$to)){
					hikashop_display('Could not copy the file '.$from.' to '.$to.'. Please check the persmissions of the folder '.dirname($to));
				}
			}
			$from = HIKASHOP_ROOT.DS.'images'.DS.'M_images'.DS.'new.png';
			$to = HIKASHOP_MEDIA.'images'.DS.'icons'.DS.'icon-16-new.png';
			if(!file_exists($to) AND file_exists($from)){
				if(!JFile::copy($from,$to)){
					hikashop_display('Could not copy the file '.$from.' to '.$to.'. Please check the persmissions of the folder '.dirname($to));
				}
			}
			$from = HIKASHOP_ROOT.DS.'images'.DS.'M_images'.DS.'con_info.png';
			$to = HIKASHOP_MEDIA.'images'.DS.'icons'.DS.'icon-16-info.png';
			if(!file_exists($to) AND file_exists($from)){
				if(!JFile::copy($from,$to)){
					hikashop_display('Could not copy the file '.$from.' to '.$to.'. Please check the persmissions of the folder '.dirname($to));
				}
			}
			$from = rtrim(JPATH_ADMINISTRATOR,DS).DS.'templates'.DS.'khepri'.DS.'images'.DS.'menu'.DS.'icon-16-user.png';
			$to = HIKASHOP_MEDIA.'images'.DS.'icons'.DS.'icon-16-levels.png';
			if(!file_exists($to) AND file_exists($from)){
				if(!JFile::copy($from,$to)){
					hikashop_display('Could not copy the file '.$from.' to '.$to.'. Please check the persmissions of the folder '.dirname($to));
				}
			}
		}else{
			$images['icon-16-edit.png']='menu';
			$images['icon-16-new.png']='menu';
			$images['icon-16-levels.png']='menu';
			$images['icon-16-info.png']='menu';
		}

		foreach($images as $oneImage=>$folder){
			$to = HIKASHOP_MEDIA.'images'.DS.'icons'.DS.$oneImage;
			if(!HIKASHOP_J16){
				$from = rtrim(JPATH_ADMINISTRATOR,DS).DS.'templates'.DS.'khepri'.DS.'images'.DS.$folder.DS.$oneImage;
			}else{
				$from = rtrim(JPATH_ADMINISTRATOR,DS).DS.'templates'.DS.'bluestork'.DS.'images'.DS.$folder.DS.$oneImage;
			}
			if(!file_exists($to) AND file_exists($from)){
				if(!JFile::copy($from,$to)){
					hikashop_display('Could not copy the file '.$from.' to '.$to.'. Please check the persmissions of the folder '.dirname($to));
				}
			}
		}
		if(!HIKASHOP_J16){
			$path = rtrim(JPATH_SITE,DS).DS.'plugins'.DS.'hikashop'.DS.'history.php';
		}else{
			$path = rtrim(JPATH_SITE,DS).DS.'plugins'.DS.'hikashop'.DS.'history'.DS.'history.php';
		}
		if(!file_exists($path)){
	 		$folders = array('* Joomla / Plugins','* Joomla / Plugins / User','* Joomla / Plugins / System','* Joomla / Plugins / Search');
			hikashop_display(JText::_('ERROR_PLUGINS_1').'<br/>'.JText::_('ERROR_PLUGINS_2').'<br/>'.implode('<br/>',$folders).'<br/><a href="index.php?option=com_hikashop&amp;ctrl=update&amp;task=install">'.JText::_('ERROR_PLUGINS_3').'</a>','warning');
		}
	}

	function language(){
		$code = JRequest::getString('code');
		if(empty($code)){
			hikashop_display('Code not specified','error');
			return;
		}
		$file = new stdClass();
		$file->name = $code;
		$path = JLanguage::getLanguagePath(JPATH_ROOT).DS.$code.DS.$code.'.com_hikashop.ini';
		$file->path = $path;
		jimport('joomla.filesystem.file');
		$showLatest = true;
		$loadLatest = false;
		if(JFile::exists($path)){
			$file->content = JFile::read($path);
			if(empty($file->content)){
				hikashop_display('File not found : '.$path,'error');
			}
		}else{
			$loadLatest = true;
			hikashop_display(JText::_('LOAD_ENGLISH_1').'<br/>'.JText::_('LOAD_ENGLISH_2').'<br/>'.JText::_('LOAD_ENGLISH_3'),'info');
			$file->content = JFile::read(JLanguage::getLanguagePath(JPATH_ROOT).DS.'en-GB'.DS.'en-GB.com_hikashop.ini');
		}
		if($loadLatest OR JRequest::getString('task') == 'latest'){
			$doc = JFactory::getDocument();
			$doc->addScript(HIKASHOP_UPDATEURL.'languageload&code='.JRequest::getString('code'));
			$showLatest = false;
		}elseif(JRequest::getString('task') == 'save') $showLatest = false;
		$override_content = '';
		$override_path = JLanguage::getLanguagePath(JPATH_ROOT).DS.'overrides'.DS.$code.'.override.ini';
		if(JFile::exists($override_path)){
			$override_content = JFile::read($override_path);
		}
		$this->assignRef('override_content',$override_content);
		$this->assignRef('showLatest',$showLatest);
		$this->assignRef('file',$file);
	}
	function css(){
		$file = JRequest::getCmd('file');
		if(!preg_match('#^([-_A-Za-z0-9]*)_([-_A-Za-z0-9]*)$#i',$file,$result)){
			hikashop_display('Could not load the file '.$file.' properly');
			exit;
		}
		$type = $result[1];
		$fileName = $result[2];
		$content = JRequest::getString('csscontent');
		if(empty($content)) $content = file_get_contents(HIKASHOP_MEDIA.'css'.DS.$type.'_'.$fileName.'.css');
		if($fileName == 'default'){
			$fileName = 'custom';
			$i = 1;
			while(file_exists(HIKASHOP_MEDIA.'css'.DS.$type.'_'.$fileName.'.css')){
				$fileName = 'custom'.$i;
				$i++;
			}
		}
		$this->assignRef('content',$content);
		$this->assignRef('fileName',$fileName);
		$this->assignRef('type',$type);
	}
	function share(){
		$file = new stdClass();
		$file->name = JRequest::getString('code');
		$this->assignRef('file',$file);
	}

	function leftmenu($name, $data) {
		$this->menuname = $name;
		$this->menudata = $data;
		$this->setLayout('leftmenu');
		return $this->loadTemplate();
	}
}
