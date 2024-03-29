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

class os_payments
{
	public static $methods = null;
	/**
	 * Get list of payment methods
	 *
	 * @return array
	 */
	public static function getPaymentMethods($methodIds = null)
	{
		if (!self::$methods)
		{
			define('JPAYMENT_METHODS_PATH', JPATH_ROOT . '/components/com_eventbooking/payments/');
			$db = JFactory::getDBO();
			if ($methodIds)
			{
				$sql = 'SELECT * FROM #__eb_payment_plugins WHERE published=1 AND id IN (' . $methodIds . ') ORDER BY ordering';
			}
			else
			{
				$sql = 'SELECT * FROM #__eb_payment_plugins WHERE published=1 ORDER BY ordering';
			}
			$db->setQuery($sql);
			$rows = $db->loadObjectList();
			foreach ($rows as $row)
			{
				if (file_exists(JPAYMENT_METHODS_PATH . $row->name . '.php'))
				{
					require_once JPAYMENT_METHODS_PATH . $row->name . '.php';
					$method = new $row->name(new JRegistry($row->params));
					$method->setTItle($row->title);
					self::$methods[] = $method;
				}
			}
		}
		return self::$methods;
	}

	/**
	 * Write the javascript objects to show the page
	 *
	 * @return string
	 */
	public static function writeJavascriptObjects()
	{
		$methods = self::getPaymentMethods();
		$jsString = " methods = new PaymentMethods();\n";
		if (count($methods))
		{
			foreach ($methods as $method)
			{
				$jsString .= " method = new PaymentMethod('" . $method->getName() . "'," . $method->getCreditCard() . "," . $method->getCardType() . "," . $method->getCardCvv() . "," . $method->getCardHolderName() . ");\n";
				$jsString .= " methods.Add(method);\n";
			}
		}
		echo $jsString;
	}

	/**
	 * Load information about the payment method
	 *
	 * @param string $name Name of the payment method
	 */
	public static function loadPaymentMethod($name)
	{
		$db = JFactory::getDBO();
		$sql = 'SELECT * FROM #__eb_payment_plugins WHERE name="' . $name . '"';
		$db->setQuery($sql);
		return $db->loadObject();
	}

	/**
	 * Get default payment gateway
	 *
	 * @return string
	 */
	public static function getDefautPaymentMethod()
	{
		$db = JFactory::getDBO();
		$sql = 'SELECT name FROM #__eb_payment_plugins WHERE published=1 ORDER BY ordering LIMIT 1';
		$db->setQuery($sql);
		return $db->loadResult();
	}

	/**
	 * Get the payment method object based on it's name
	 *
	 * @param string $name
	 * @return object
	 */
	public static function getPaymentMethod($name)
	{
		$methods = self::getPaymentMethods();
		foreach ($methods as $method)
		{
			if ($method->getName() == $name)
			{
				return $method;
			}
		}
		return null;
	}
}
?>