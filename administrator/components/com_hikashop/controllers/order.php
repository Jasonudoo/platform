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
class OrderController extends hikashopController{
	var $type='order';
	function __construct($config = array()){
		parent::__construct($config);
		$this->modify_views[]='changestatus';
		$this->modify_views[]='product';
		$this->modify_views[]='product_select';
		$this->modify_views[]='product_add';
		$this->modify_views[]='product_delete';
		$this->modify_views[]='address';
		$this->modify_views[]='state';
		$this->modify_views[]='mail';
		$this->modify_views[]='partner';
		$this->display[]='invoice';
		$this->display[]='address';
		$this->display[]='export';
		$this->modify_views[]='discount';
		$this->modify_views[]='fields';
		$this->modify_views[]='changeplugin';
		$this->modify[]='savechangestatus';
		$this->modify[]='saveproduct';
		$this->modify_views[]='neworder';
		$this->modify[]='saveproduct_delete';
		$this->modify[]='saveaddress';
		$this->modify[]='savemail';
		$this->modify[]='savechangeplugin';
		$this->modify[]='savediscount';
		$this->modify[]='savepartner';
		$this->modify[]='savefields';
		$this->modify[]='saveuser';
		$this->modify_views[]='user';
		$this->modify[]='deleteentry';
		$this->display[]='download';
		$this->display[]='remove_history_data';
	}

	function neworder(){
		$null = new stdClass();
		$class = hikashop_get('class.order');
		$class->sendEmailAfterOrderCreation = false;
		if($class->save($null)){
			$this->_terminate($null,1);
		}else{
			$this->listing();
		}
	}

	function download(){
		$file_id = JRequest::getInt('file_id');
		if(empty($file_id)){
			$field_table = JRequest::getWord('field_table');
			$field_namekey = JRequest::getString('field_namekey');
			$name = JRequest::getString('name');
			if(empty($field_table)||empty($field_namekey)||empty($name)){
				$app=JFactory::getApplication();
				$app->enqueueMessage(JText::_('FILE_NOT_FOUND'));
				return false;
			}else{
				$fileClass = hikashop_get('class.file');
				$fileClass->downloadFieldFile(urldecode(base64_decode($name)),$field_table,urldecode(base64_decode($field_namekey)));
			}

		}
		$file_pos = JRequest::getInt('file_pos',1);
		$order_id = JRequest::getInt('order_id',0);
		$fileClass = hikashop_get('class.file');
		$fileClass->download($file_id,$order_id,$file_pos);
	}


	function changestatus(){
		JRequest::setVar( 'layout', 'changestatus'  );
		return parent::display();
	}

	function product(){
		JRequest::setVar( 'layout', 'product'  );
		return parent::display();
	}
	function user(){
		JRequest::setVar( 'layout', 'user'  );
		JRequest::setVar('cart_id',JRequest::getString('cart_id','0'));
		JRequest::setVar('cart_type',JRequest::getString('cart_type','0'));
		return parent::display();
	}
	function product_select(){
		JRequest::setVar( 'layout', 'product_select'  );
		$cart_type = JRequest::getString('cart_type','cart');
		JRequest::setVar('cart_type',$cart_type);
		JRequest::setVar($cart_type.'_id',JRequest::getInt('cart_id','0'));
		return parent::display();
	}
	function product_add(){
		$config =& hikashop_config();
		$currencyClass = hikashop_get('class.currency');
		$classOrder = hikashop_get('class.order');
		$data = $this->_cleanOrder();
		$product_ids = JRequest::getVar( 'cid', array(), '', 'array' );
		$quantities = JRequest::getVar( 'quantity', array(), '', 'array' );
		$rows = array();
		if(!empty($product_ids)){
			JArrayHelper::toInteger($product_ids);
			$database	= JFactory::getDBO();
			$query = 'SELECT * FROM '.hikashop_table('product').' WHERE product_id IN ('.implode(',',$product_ids).')';
			$database->setQuery($query);
			$rows = $database->loadObjectList();
		}
		$user_id = 0;
		$main_currency = (int)$config->get('main_currency',1);
		$discount_before_tax = (int)$config->get('discount_before_tax',0);
		if(!empty($data->order_id)){
			$orderData = $classOrder->get($data->order_id);
			$currency_id = $orderData->order_currency_id;
			$user_id = $orderData->order_user_id;
		}else{
			$currency_id = hikashop_getCurrency();
		}

		if($config->get('tax_zone_type','shipping')=='billing'){
			$zone_id = hikashop_getZone('billing');
		}else{
			$zone_id = hikashop_getZone('shipping');
		}
		$currencyClass->getPrices($rows, $product_ids, $currency_id, $main_currency, $zone_id, $discount_before_tax, $user_id);

		$element = array();
		if(!empty($rows)){
			foreach($rows as $k => $row){
				$obj = new stdClass();
				$obj->order_product_name = $row->product_name;
				$obj->order_product_code = $row->product_code;
				$obj->order_product_quantity = (!empty($quantities[$row->product_id]) ? $quantities[$row->product_id]:1 );
				$currencyClass->pricesSelection($row->prices,$obj->order_product_quantity);
				$obj->product_id = $row->product_id;
				$obj->order_id = (int)$data->order_id;
				if(!empty($row->prices)){
					foreach($row->prices as $price){
						$obj->order_product_price = $price->price_value;
						$obj->order_product_tax = ($price->price_value_with_tax-$price->price_value);
						$obj->order_product_tax_info = $price->taxes;
					}
				}
				$element[$k]=$obj;
			}
		}

		$result = false;
		$cart_type = JRequest::getString('cart_type','cart');
		$cart_id = JRequest::getString($cart_type.'_id','0');
		if(!empty($data->order_id)){
			$data->product = $element;
			$classOrder = hikashop_get('class.order');
			$classOrder->recalculateFullPrice($data);
			$result = $classOrder->save($data);
		}else if($cart_id != '0'){ //cart type
			$classCart = hikashop_get('class.cart');
			$result = true;
			foreach($element as $data){
				if(!$classCart->update($data->product_id, $data->order_product_quantity,1,'cart',true,true)){
					$result=false;
				}
			}

			$element = new stdClass();
			$element->cart_type = $cart_type;
			$element->cart_id = $cart_id;
			if($result)
				$this->_terminate($element,'showcart');
			else
				$this->product_select();
		}
		if($result){
			$this->_terminate($data,1);
		}
	}
	function address(){
		JRequest::setVar( 'layout', 'address'  );
		return parent::display();
	}

	function invoice(){
		JRequest::setVar( 'layout', 'invoice'  );
		return parent::display();
	}
	function export(){
		JRequest::setVar( 'layout', 'export'  );
		return parent::display();
	}
	function discount(){
		JRequest::setVar( 'layout', 'discount'  );
		return parent::display();
	}
	function fields(){
		JRequest::setVar( 'layout', 'fields'  );
		return parent::display();
	}
	function savefields(){
		$this->_save(1,'fields');
	}
	function savediscount(){
		$this->_save();
	}
	function partner(){
		JRequest::setVar( 'layout', 'partner'  );
		return parent::display();
	}
	function savepartner(){
		$this->_save();
	}
	function saveuser(){
		$set_address = JRequest::getInt('set_address', 0);
		if($set_address) {
			$formData = JRequest::getVar( 'data', array(), '', 'array');
			if(isset($formData['order']['order_user_id'])) {
				$user_id = $formData['order']['order_user_id'];
				$db = JFactory::getDBO();
				if(JRequest::getString('cart_id','0') != '0'){
					$userClass = hikashop_get('class.user');
					$user = $userClass->get($user_id);
					$user_id = $user->user_cms_id;

					$query = 'UPDATE '.hikashop_table('cart').' SET user_id = '.$user_id.' WHERE cart_id = '.JRequest::getString('cart_id','0');
					$db->setQuery($query);
					$db->query();
					JRequest::setVar('user_id',$user_id,'GET',true);
					$element = new stdClass();
					$element->user_id = $user_id;
					$element->cart_id = JRequest::getString('cart_id','0');
					$element->cart_type = JRequest::getString('cart_type','cart');
					$this->_terminate($element,'showcart');
				}else{
					$db->setQuery('SELECT address_id FROM '.hikashop_table('address').' WHERE address_user_id = '. (int)$user_id . ' AND address_published = 1 ORDER BY address_default DESC, address_id ASC LIMIT 1');
					$address_id = $db->loadResult();
					if($address_id) {
						$formData['order']['order_billing_address_id'] = $address_id;
						JRequest::setVar('data', $formData);
					}
				}
			}
		}
		$this->_save();
	}
	function mail(){
		JRequest::setVar( 'layout', 'mail'  );
		return parent::display();
	}
	function changeplugin(){
		JRequest::setVar( 'layout', 'changeplugin'  );
		return parent::display();
	}
	function savechangeplugin(){
		$this->_save();
	}

	function savemail(){
		$element = $this->_cleanOrder();
		if(!empty($element->mail)){
			$class = hikashop_get('class.mail');
			$class->sendMail($element->mail);
			if(!$class->mail_success){
				return true;
			}
		}
		$this->_terminate($element,2);
	}

	function saveproduct(){
		if(JRequest::getInt('cart_id','0') != '0'){ //Check the quantity too ?
			$cart_id = JRequest::getString('cart_id','0');
			$cart_type = JRequest::getString('cart_type','cart');
			JRequest::setVar('cart_id',$cart_id);
			JRequest::setVar('cart_type',$cart_type);
			$classCart = hikashop_get('class.cart');
			$classCart->update(JRequest::getInt('product_id','0'), 0,0,'product',true,true);
			$element = new stdClass();
			$element->cart_type = $cart_type;
			$element->cart_id = $cart_id;
			$this->_terminate($element,'showcart');
		}
		$this->_save();
	}

	function saveaddress(){
		$result = false;
		$class = hikashop_get('class.address');
		$oldData = null;

		if(!empty($_REQUEST['address']['address_id'])){
			$oldData = $class->get($_REQUEST['address']['address_id']);
		}

		$fieldsClass = hikashop_get('class.field');
		$address = $fieldsClass->getInput('address',$oldData);
		if(empty($address)){
			return false;
		}
		$element = $this->_cleanOrder();


		if(!empty($element->order_id)){
			$type = JRequest::getCmd('type');
			$result = $class->save($address,$element->order_id,$type);
			if($result){
				$name = 'order_'.$type.'_address_id';
				$element->$name = $result;
				$class = hikashop_get('class.order');
				$result = $class->save($element);
				if($result){
					$this->_terminate($element);
				}
			}
		}
	}

	function remove_history_data(){
		$history_id = JRequest::getInt( 'history_id', 0);
		if($history_id){
			$class = hikashop_get('class.history');
			$history = $class->get($history_id);
			if($history){
				$newHistoryObj = new stdClass();
				$newHistoryObj->history_id = $history_id;
				$newHistoryObj->history_data = '';
				$class->save($newHistoryObj);
			}
			JRequest::setVar( 'order_id', $history->history_order_id );
			return $this->edit();
		}else{
			return $this->listing();
		}
	}

	function product_delete(){
		JRequest::setVar( 'layout', 'product_delete'  );
		JRequest::setVar( 'cart_id', JRequest::getInt('cart_id','0')  );
		JRequest::setVar( 'product_id',JRequest::getInt('product_id','0')  );
		JRequest::setVar( 'cart_type',JRequest::getString('cart_type','cart')  );
		return parent::display();
	}

	function savechangestatus(){
		$this->_save(JRequest::getInt('edit', 0));
	}
	function _cleanOrder(){
		$element = new stdClass();
		$formData = JRequest::getVar( 'data', array(), '', 'array' );
		$fieldsClass = hikashop_get('class.field');
		$old = null; //$fieldsClass->get($formData['order']['product']['order_product_id']);

		foreach($formData['order'] as $column => $value){
			hikashop_secureField($column);
			if($column == 'product') {
				$formData['item'] = $formData['order']['product'];
				JRequest::setVar('data', $formData);
				$fieldsClass->getInput('item',$old,false);
				$element->product = $_SESSION['hikashop_item_data'];
			} elseif(in_array($column,array('history','mail'))){
				$element->$column = new stdClass();
				foreach($value as $k => $v){
					$k = hikashop_secureField($k);
					$element->$column->$k = strip_tags($v);
				}
			}else{
				if(is_array($value)){
					$value = implode(',',$value);
				}
				$element->$column = strip_tags($value);
			}
		}
		if(!isset($element->mail)) $element->mail = new stdClass();
		$element->mail->body = JRequest::getVar('hikashop_mail_body','','','string',JREQUEST_ALLOWRAW);

		return $element;
	}

	function _save($type=1,$data=''){
		$element = $this->_cleanOrder();

		$result = false;
		$app = JFactory::getApplication();
		if(!empty($element->order_id)){
			$order_id = $element->order_id;
			$class = hikashop_get('class.order');
			if($data == 'fields'){
				$field = hikashop_get('class.field');
				$old = $class->get($element->order_id);
				$element = $field->getInput('order',$old,false);
				if($element === false) {
					$app->enqueueMessage(JText::sprintf('PLEASE_FILL_THE_FIELD', JText::_('REQUIRED')), 'error');
				} else if(empty($element)) {
					$app->enqueueMessage(JText::_('ERROR_SAVING'), 'error');
				} else {
					$element->mail->body = JRequest::getVar('hikashop_mail_body','','','string',JREQUEST_ALLOWRAW);
				}
			}

			if(!empty($element)) {
				$result = $class->save($element);
			}
		}
		if($result && $class->mail_success){
			$this->_terminate($element,$type);
		}

	}

	function save(){
	}

	function deleteentry(){
		$entry = JRequest::getInt('entry_id',0);
		if($entry){
			$entryClass = hikashop_get('class.entry');
			$oldData = $entryClass->get($entry);
			if(!empty($oldData)){
				$entryClass->delete($entry);
				JRequest::setVar('cid',$oldData->order_id);
			}
		}
		$this->edit();
	}

	function _terminate(&$element,$type=1){
		$js = '';
		if($type == 2){
			$js = 'parent.hikashop.closeBox();';
		}elseif($type === 'showcart'){
			if($element != null){
				$js = 'parent.window.location.href=\''.hikashop_completeLink('cart&task=edit&cart_type='.$element->cart_type.'&cid[]='.@$element->cart_id,false,true).'\';';
			}else{
				$js = 'parent.window.location.reload();';
			}
		}elseif($type){
			$js = 'parent.window.location.href=\''.hikashop_completeLink('order&task=edit&cid[]='.@$element->order_id,false,true).'\';';
		}
		else{
			$js = 'parent.document.getElementById(\'filter_status_'.@$element->order_id.'\').value=\''.@$element->order_status.'\';parent.default_filter_status_'.@$element->order_id.'=\''.@$element->order_status.'\';if(typeof(parent.jQuery)!=\'undefined\'){parent.jQuery(parent.document.getElementById(\'filter_status_'.@$element->order_id.'\')).trigger(\'liszt:updated\');} window.parent.hikashop.closeBox();';
		}
		if(!headers_sent()){
			header( 'Cache-Control: no-store, no-cache, must-revalidate' );
			header( 'Cache-Control: post-check=0, pre-check=0', false );
			header( 'Pragma: no-cache' );
		}
		echo '<html><head><script type="text/javascript">'.$js.'</script></head><body></body></html>';
		exit;
	}

}
