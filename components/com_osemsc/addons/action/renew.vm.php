<?php
defined('_JEXEC') or die(";)");

class oseMscAddonActionRenewVm
{
	public static function renew($params)
	{
		$result = array();
		$result['success'] = true;
		
		if(empty($params['allow_work']))
		{
			$result['success'] = false;
			$result['title'] = 'Error';
			$result['content'] = JText::_("Error");
			return $result;
		}
		unset($params['allow_work']);
		
		//oseExit($params);
		$db = oseDB::instance();
		$post = JRequest::get('post');
		$msc_id = $params['msc_id'];
		$member_id = $params['member_id'];
				
		if(empty($msc_id))
		{
			$result['success'] = false;
			$result['title'] = 'Error';
			$result['content'] = JText::_("Renew Msc: No Msc ID");
			return $result;
		}
		
		// get the vm shopper group id of msc
    	$query = "SELECT * FROM `#__osemsc_ext` WHERE `id` = '{$msc_id}' AND `type` = 'vm'";
        $db->setQuery($query);
        $data = $db->loadObject();
		$data = oseJson::decode($data->params);
		
		if(!empty($data->sg_id))
	    {
	    	$vm_sg_id = $data->sg_id;
	    }else
        {
           	$query = "SELECT shopper_group_id FROM `#__vm_shopper_group` WHERE `default` = '1'";
           	$db->setQuery($query);
        	$vm_sg_id = $db->loadResult();
        }
	    
        $hash_secret = "VirtueMartIsCool";
        $user_info_id = md5(uniqid( $hash_secret));
		$query = "SELECT count(*) FROM #__vm_user_info WHERE user_id=".(int)$member_id;
        $db->setQuery($query);
        $result = $db ->loadResult();
        $option = JRequest::getVar('option');
        if (empty($result) && $option != 'com_virtuemart')
        {
    		$user = JFactory::getUser($member_id);
			$email = $user->email;
			$query ="INSERT INTO `#__vm_user_info` (`user_info_id`, `user_id`, `address_type`, `user_email`) VALUES ('{$user_info_id}', '{$member_id}', 'BT', '{$email}');";           
            $db->setQuery($query);
             if (!$db->query())
             {
             	$result['success'] = false;
				$result['title'] = 'Error';
				$result['content'] = JText::_("Join VM User Info Error.");
				return $result;
             }    
        }
        
        $query = "SELECT * FROM #__vm_shopper_vendor_xref WHERE user_id=".(int)$member_id;
        $db->setQuery($query);
        $result = $db ->loadResult();
        if (!empty($result))
        {
        	$query = "UPDATE `#__vm_shopper_vendor_xref` SET `shopper_group_id` =".(int)$vm_sg_id." WHERE `user_id` =".(int)$member_id;
        }else
        {
             $query ="INSERT INTO `#__vm_shopper_vendor_xref` (`user_id` ,`vendor_id` ,`shopper_group_id` ,`customer_number`)VALUES ('{$member_id}', '1', '{$vm_sg_id}', '');";   
        }
        $db->setQuery($query);
        if (!$db->query())
        {
           	$result['success'] = false;
			$result['title'] = 'Error';
			$result['content'] = JText::_("Join VM User Info Error.");
			return $result;
        } 
		
		//Update VM billing Info
        if($data->update_billing)
        {
        	$payment= oseRegistry :: call('payment');
			$paymentOrder = $payment->getInstance('Order');
			$billinginfo = $paymentOrder->getBillingInfo($member_id);
			$user = JFactory::getUser($member_id);
			if(!empty($billinginfo))
			{
				$bill = array();
				$bill['company'] = empty($billinginfo->company )?null:$billinginfo->company;
				$bill['first_name'] = empty($billinginfo->firstname )?null:$billinginfo->firstname;
				$bill['last_name'] = empty($billinginfo->lastname )?null:$billinginfo->lastname;
				$bill['phone_1'] = empty($billinginfo->telephone )?null:$billinginfo->telephone;
				$bill['address_1'] = empty($billinginfo->addr1 )?null:$billinginfo->addr1;
				$bill['address_2'] = empty($billinginfo->addr2 )?null:$billinginfo->addr2;
				$bill['city'] = empty($billinginfo->city )?null:$billinginfo->city;
				$bill['state'] = empty($billinginfo->state )?null:$billinginfo->state;
				$bill['zip'] = empty($billinginfo->postcode )?null:$billinginfo->postcode;
				$bill['user_email'] = empty($user->email )?null:$user->email;
				//$query = "SELECT country_2_code FROM `#__vm_country` WHERE `country_3_code` = '{$billinginfo->country}'";
				//$db->setQuery($query);
				//$country = $db->loadResult();
				$bill['country'] = empty($billinginfo->country )?null:$billinginfo->country;
				$billinfo = array();
				foreach($bill as $key => $value)
				{
					if(!empty($value))
					{
						$billinfo[$key] = "`{$key}`=".$db->Quote($value);
					}
				}
				
				$values = implode(',',$billinfo);
				$query = " UPDATE `#__vm_user_info` SET {$values}"
						." WHERE `user_id` ={$member_id} AND `address_type` = 'BT'"
						;
				$db->setQuery($query);		
				if (!$db->query())
		        {
		           	$result['success'] = false;
					$result['title'] = 'Error';
					$result['content'] = JText::_("Join VM User Info Error.");
					return $result;
		        }		
					
			}
        }
        //generate VM order
        if($data->update_order && !empty($data->product_id))
        {
        	$order_id = $params['order_id'];
        	$order_item_id = $params['order_item_id'];
        	
			$where= array();
			$where[]= "`order_id` = ".$db->quote($order_id);
			$payment= oseRegistry :: call('payment');
			$orderInfo = $payment->getOrder($where, 'obj');
			$orderInfoParams= oseJson :: decode($orderInfo->params);
			
			$str = session_id();
			$str .= (string)time();
			$order_number = $member_id .'_'. md5($str);
			$vm_order_number = substr($order_number, 0, 32);
			$query = "SELECT user_info_id FROM `#__vm_user_info` WHERE `user_id` = {$member_id} AND `address_type` = 'BT'";
			$db->setQuery($query);	
			$user_info_id = $db->loadResult();
			$timestamp = time();
			$query = " INSERT INTO `#__vm_orders` (`user_id`, `vendor_id`, `order_number`, `user_info_id`, `order_total`, `order_subtotal`, `order_tax`, `order_currency`, `order_status`, `cdate`, `mdate`)"
					." VALUES"
					." ('{$member_id}', 1, '{$vm_order_number}', '{$user_info_id}', '{$orderInfoParams->total}', '{$orderInfoParams->subtotal}', '{$orderInfoParams->gross_tax}', '{$orderInfo->payment_currency}', 'C', '{$timestamp}', '{$timestamp}')";
	        $db->setQuery($query);	
			if (!$db->query())
		    {
		       	$result['success'] = false;
				$result['title'] = 'Error';
				$result['content'] = JText::_("Join VM Order Info Error.");
				return $result;
		    }
		    
		    //order item 
		    $vm_order_id = $db->insertid();
		    $product_id = $data->product_id;
		    $query = " SELECT p.*,pp.product_price FROM `#__vm_product` AS p "
		    		." INNER JOIN `#__vm_product_price` AS pp"
		    		." ON p.`product_id` = pp.`product_id`"
		    		." WHERE p.`product_id` = ".$product_id;
		    $db->setQuery($query);
		    $proInfo = $db->loadObject();
			
		    $query = " INSERT INTO `#__vm_order_item` (`order_id`, `user_info_id`, `vendor_id`, `product_id`, `order_item_sku`, `order_item_name`, `product_quantity`, `product_item_price`, `product_final_price`, `order_item_currency`, `order_status`, `cdate`, `mdate`)"
		    		." VALUES"
		    		." ('{$vm_order_id}', '{$user_info_id}', 1, '{$product_id}', '{$proInfo->product_sku}', '{$proInfo->product_name}', 1, '{$proInfo->product_price}', '{$orderInfoParams->total}', '{$orderInfo->payment_currency}', 'C', '{$timestamp}', '{$timestamp}')";
         	$db->setQuery($query);	
			if (!$db->query())
		    {
		       	$result['success'] = false;
				$result['title'] = 'Error';
				$result['content'] = JText::_("Join VM Order Info Error.");
				return $result;
		    }

		    //order user info
		    $array = array();
		    foreach($bill as $key => $value)
    		{
    			$array[$key] = $db->Quote($value);
    		}
			$keys = array_keys($bill);
    		$keys = '`'.implode('`,`',$keys).'`';
    		$values = implode(',',$array);

			$query = "INSERT INTO `#__vm_order_user_info` (`order_id`,`user_id`,`address_type`,{$keys}) VALUES ('{$vm_order_id}','{$member_id}','BT',{$values});";
        	$db->setQuery($query);
			if (!$db->query())
		    {
		       	$result['success'] = false;
				$result['title'] = 'Error';
				$result['content'] = JText::_("Join VM Order Info Error.");
				return $result;
		    }
        }
		$result['success'] = true;
		$result['title'] = JText::_('Done');
		$result['content'] = JText::_("Done");
			
		return $result;
		
	}
	
	public static function activate($params)
	{
		return self:: renew($params);
	}
	
	
}
?>