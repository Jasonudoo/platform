<?php
/**
 * @version		1.5.3
 * @package		Joomla
 * @subpackage	Event Booking
 * @author  Tuan Pham Ngoc
 * @copyright	Copyright (C) 2010 Ossolution Team
 * @license		GNU/GPL, see LICENSE.php
 */
// no direct access
defined('_JEXEC') or die();

class EBCart
{

	function EBCart()
	{
		$session = JFactory::getSession();
		$cart = $session->get('eb_cart');
		if ($cart == null)
		{
			$cart = array('items' => array(), 'quantities' => array());
			$session->set('eb_cart', $cart);
		}
	}

	/**
	 * Add an item to the cart
	 *
	 * @param int $id
	 */
	function add($id)
	{
		$config = EventBookingHelper::getConfig();
		$session = JFactory::getSession();
		$cart = $session->get('eb_cart');
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
		$session->set('eb_cart', $cart);
	}

	/**
	 * Add serveral events into shopping cart		 
	 * @param array $cid
	 */
	function addEvents($cid)
	{
		$config = EventBookingHelper::getConfig();
		$session = JFactory::getSession();
		$cart = $session->get('eb_cart');
		$quantities = $cart['quantities'];
		$items = $cart['items'];
		if (count($cid))
		{
			foreach ($cid as $id)
			{
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
						if ($items[$i] == $id)
						{
							if ($config->prevent_duplicate_registration)
								$quantities[$i] = 1;
							else 
								$quantities[$i] += 1;
							break;
						}
					}
				}
			} //End Foreach
		}
		$cart['items'] = $items;
		$cart['quantities'] = $quantities;
		$session->set('eb_cart', $cart);
	}

	/**
	 *	Remove an item from shopping cart
	 *
	 * @param int $id
	 */
	function remove($id)
	{
		$session = JFactory::getSession();
		$cart = $session->get('eb_cart');
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
		$session->set('eb_cart', $cart);
	}

	/**
	 * Reset the cart
	 *
	 */
	function reset()
	{
		$session = JFactory::getSession();
		$cart = array('items' => array(), 'quantities' => array());
		$session->set('eb_cart', $cart);
	}

	/**
	 * Get all items from cart
	 * @return array
	 */
	function getItems()
	{
		$session = JFactory::getSession();
		$cart = $session->get('eb_cart');
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
		$cart = $session->get('eb_cart');
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
		$cart = $session->get('eb_cart');
		if (isset($cart['items']))
			return count($cart['items']);
		else
			return 0;
	}

	/**
	 * Update cart with new quantities		 
	 * @param array $eventIds
	 * @param array $quantities
	 */
	function updateCart($eventIds, $quantities)
	{
		$session = JFactory::getSession();
		$newItems = array();
		$newQuantities = array();
		for ($i = 0, $n = count($eventIds); $i < $n; $i++)
		{
			if (($eventIds[$i] > 0) && ($quantities[$i] > 0))
			{
				$newItems[] = $eventIds[$i];
				$newQuantities[] = $quantities[$i];
			}
		}
		$cart = array('items' => $newItems, 'quantities' => $newQuantities);
		$session->set('eb_cart', $cart);
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
			$total += $quantities[$i] * EventBookingHelper::getRegistrationRate($items[$i], $quantities[$i]);
		}
		return $total;
	}

	/**
	 * Get list of events in the cart
	 * return array
	 */
	function getEvents()
	{
		$db = JFactory::getDBO();
		$items = $this->getItems();
		$quantities = $this->getQuantities();
		$quantityArr = array();
		for ($i = 0, $n = count($items); $i < $n; $i++)
		{
			$quantityArr[$items[$i]] = $quantities[$i];
		}
		if (count($items))
		{
			$sql = 'SELECT a.*, SUM(b.number_registrants) AS total_registrants  FROM #__eb_events AS a LEFT JOIN #__eb_registrants AS b ON (a.id = b.event_id AND b.group_id=0 AND (b.published=1 OR (b.payment_method LIKE "os_offline%" AND b.published != 2))) WHERE a.id IN (' .
				 implode(',', $items) . ') GROUP BY a.id';
				$db->setQuery($sql);
				$events = $db->loadObjectList();
				for ($i = 0, $n = count($events); $i < $n; $i++)
				{
					$event = $events[$i];
					$event->rate = EventBookingHelper::getRegistrationRate($event->id, $quantityArr[$event->id]);
					$event->quantity = $quantityArr[$event->id];
				}
			}
			else
			{
				$events = array();
			}
			return $events;
		}

		/**
	 * Calculate total discount for the registration
	 * @return float
	 * 
	 */
		function calculateTotalDiscount()
		{
			$user = JFactory::getUser();
			$db = JFactory::getDBO();
			$nullDate = $db->getNullDate();
			$events = $this->getEvents();
			$totalDiscount = 0;
			for ($i = 0, $n = count($events); $i < $n; $i++)
			{
				$event = $events[$i];
				$registrantTotalAmount = $event->rate * $event->quantity;
				$registrantDiscount = 0;
				//Member discount
				if ($user->get('id'))
				{
					if ($event->discount > 0)
					{
						if ($event->discount_type == 1)
						{
							$registrantDiscount = $registrantTotalAmount * $event->discount / 100;
						}
						else
						{
							$registrantDiscount = $event->quantity * $event->discount;
						}
					}
				}
				//Calculate the coupon discount
				if (isset($_SESSION['coupon_id']))
				{
					$sql = 'SELECT * FROM #__eb_coupons WHERE id=' . (int) $_SESSION['coupon_id'];
					$db->setQuery($sql);
					$coupon = $db->loadObject();
					if ($coupon && ($coupon->event_id == 0 || $coupon->event_id == $event->id))
					{
						if ($coupon->coupon_type == 0)
						{
							$registrantDiscount += $registrantTotalAmount * $coupon->discount / 100;
						}
						else
						{
							$registrantDiscount += $registrantDiscount + $coupon->discount;
						}
					}
				}
				//Early bird discount
				if (($event->early_bird_discount_amount > 0) && ($event->early_bird_discount_date != $nullDate) &&
					 (strtotime($event->early_bird_discount_date) >= mktime()))
					{
						if ($event->early_bird_discount_type == 1)
						{
							$registrantDiscount += $registrantTotalAmount * $event->early_bird_discount_amount / 100;
						}
						else
						{
							$registrantDiscount += $event->quantity * $event->early_bird_discount_amount;
						}
					}
					$totalDiscount += $registrantDiscount;
				}
				return $totalDiscount;
			}
		}
		?>