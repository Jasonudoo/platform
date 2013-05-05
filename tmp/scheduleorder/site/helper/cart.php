<?php
/**
 * @version		1.0.0
 * @package		Joomla
 * @subpackage	Schedule Order
 * @author      Jason<jason@netwebx.com>
 * @copyright	Copyright (C) 2010 Ossolution Team
 * @license		GNU/GPL, see LICENSE.php
 */
// no direct access
defined('_JEXEC') or die('Restricted Access');

class ScheduleCart
{

	public function ScheduleCart()
	{
		$session = JFactory::getSession();
		$cart = $session->get('schedule_cart');
		if ($cart == null)
		{
			$cart = $this->getDefaultCart();
			$session->set('schedule_cart', $cart);
		}
	}
	
	public function getDefaultCart()
	{
		$db = JFactory::getDBO();
		$userId = JFactory::getUser()->get('id');		
		$sql = "SELECT a.* FROM #__schorder_cart WHERE mem_id = " . $userId;
		$db->setQuery($sql);
		$cart = $db->loadObjectList();
		
		$sql = "SELECT a.product_id, a.quantity FROM #__schorder_cart_product WHERE cart_id = " . $cart->cart_id;
		$db->setQuery($sql);
		$products = $db->loadObjectList();
		
		$cartInfo = array('items' => array(), 'quantities' => array());
	    for($i = 0; $i < count($products); $i++)
	    {
	    	$product = $products[$i];
	    	$cartInfo['items'][] = $product->product_id;
	    	$cartInfo['quantities'][] = $product->quantity;
	    }
	    
	    return $cartInfo;
		
	}

	/**
	 * Add an item to the cart
	 *
	 * @param int $id
	 */
	function add($id)
	{
		$config = ScheduleOrderHelper::getConfig();
		$session = JFactory::getSession();
		$cart = $session->get('schedule_cart');
		$quantities = $cart['quantities'];
		$items = $cart['items'];
		if (!in_array($id, $items))
		{
			array_push($items, $id);
			array_push($quantities, 1);
		}
		else
		{
			//Find the id
			for ($i = 0, $n = count($items); $i < $n; $i++)
			{
				if ($items[$i] == $i)
				{
					if ($config->prevent_duplicate_registration == 1)
						$quantities[$i] = 1;
					else
						$quantities[$i] += 1;
					break;
				}
			}
		}
		$cart['items'] = $items;
		$cart['quantities'] = $quantities;
		$session->set('schedule_cart', $cart);
	}

	/**
	 *	Remove an item from shopping cart
	 *
	 * @param int $id
	 */
	function remove($id)
	{
		$session = JFactory::getSession();
		$cart = $session->get('schedule_cart');
		$items = $cart['items'];
		$quantities = $cart['quantities'];
		$newItems = array();
		$newQuantities = array();
		for ($i = 0, $n = count($items); $i < $n; $i++)
		{
			if ($items[$i] != $id)
			{
				$newItems[] = $items[$i];
				$newQuantities[] = $quantities[$i];
			}
		}
		$cart['items'] = $newItems;
		$cart['quantities'] = $newQuantities;
		$session->set('schedule_cart', $cart);
	}

	/**
	 * Reset the cart
	 *
	 */
	function reset()
	{
		$session = JFactory::getSession();
		$cart = array('items' => array(), 'quantities' => array());
		$session->set('schedule_cart', $cart);
	}

	/**
	 * Get all items from cart
	 * @return array
	 */
	function getItems()
	{
		$session = JFactory::getSession();
		$cart = $session->get('schedule_cart');
		if (isset($cart['items']))
			return $cart['items'];
		else
			return array();
	}

	/**
	 * Get quantities
	 * @return array
	 */
	function getQuantities()
	{
		$session = JFactory::getSession();
		$cart = $session->get('schedule_cart');
		if (isset($cart['quantities']))
			return $cart['quantities'];
		else
			return array();
	}

	/**
	 * Get item couns
	 *
	 * @return int
	 */
	function getCount()
	{
		$session = JFactory::getSession();
		$cart = $session->get('schedule_cart');
		if (isset($cart['items']))
			return count($cart['items']);
		else
			return 0;
	}

	/**
	 * Update cart with new quantities		 
	 * @param array $productIds
	 * @param array $quantities
	 */
	function updateCart($productIds, $quantities)
	{
		$session = JFactory::getSession();
		$newItems = array();
		$newQuantities = array();
		for ($i = 0, $n = count($productIds); $i < $n; $i++)
		{
			if (($productIds[$i] > 0) && ($quantities[$i] > 0))
			{
				$newItems[] = $productIds[$i];
				$newQuantities[] = $quantities[$i];
			}
		}
		$cart = array('items' => $newItems, 'quantities' => $newQuantities);
		$session->set('schedule_cart', $cart);
		return true;
	}

	/**
	 * Canculate total price of the registration
	 * @return decimal
	 */
	function calculateTotal()
	{
		$db = JFactory::getDBO();
		$items = $this->getItems();
		$quantities = $this->getQuantities();
		$total = 0;
		for ($i = 0, $n = count($items); $i < $n; $i++)
		{
			$total += $quantities[$i];
		}
		return $total;
	}

	/**
	 * Get list of products in the cart
	 * return array
	 */
	function getProducts()
	{
		$db = JFactory::getDBO();
		$items = $this->getItems();
		$quantities = $this->getQuantities();
		$quantityArr = array();
		$language = "en_gb";
		
		for ($i = 0, $n = count($items); $i < $n; $i++)
		{
			$quantityArr[$items[$i]] = $quantities[$i];
		}
		
		if (count($items))
		{
			$sql = 'SELECT a.* FROM #__virtuemart_products AS a 
					LEFT JOIN #__virtuemart_products_en_gb AS b ON a.virtuemart_product_id = b.virtuemart_product_id 
					WHERE a.virtuemart_prodcut_id IN (' . implode(',', $items) . ')';
		    $db->setQuery($sql);
			$products = $db->loadObjectList();
			for ($i = 0, $n = count($products); $i < $n; $i++)
			{
				$product = $products[$i];
				$sql = 'SELECT c.* FROM #__virtuemart_product_medias AS b 
						LEFT JOIN #__virtuemart_medias AS c ON b.virtuemart_media_id = c.virtuemart_media_id 
						WHERE b.virtuemart_product_id = ' . $product->virtuemart_product_id;
				$db->setQuery($sql);
				$images = $db->loadObjectList();
				$product->quantity = $quantityArr[$product->virtuemart_product_id];
				$product->image_file_title = $images[0]->file_title;
				$product->image_file_url = $images[0]->file_url;
				$product->image_file_url_thumb = $images[0]->file_url_thumb;
				
				$sql = 'SELECT p.product_price FROM #__virtuemart_product_prices AS p WHERE p.virtuemart_product_id = ' . $product->virtuemart_product_id;
				$db->setQuery($sql);
				$price = $db->loadObjectList();
				
				$sql = 'SELECT c.custom_value, c.custom_price FROM #__virtuemart_product_customfields AS c 
						WHERE c.virtuemart_custom_id = 3 and c.virtuemart_product_id = ' .$product->virtuemart_product_id;
				$db->setQuery($sql);
				$custPrice = $db->loadObjectList();
				
				$product->custom_value = $custPrice->custom_value;
				$product->custom_price = $custPrice->custom_price;
				$product->price = $customPrice->custom_price * $product->quantity; 
				$result[] = $product;
			}
		}
		else
		{
			$result = array();
		}
		return $result;
	}

}
?>