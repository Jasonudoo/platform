<?php
defined('_JEXEC') or die(";)");

class osePaymentOrderEBS extends osePaymentGateWay
{
	protected $postVar= array();
	protected $ccInfo = array();
	protected $orderInfo = null;

	function __construct()
	{
		parent::__construct();

	}

	function EBSOneOffPay($orderInfo,$params=array()) 
	{
		$config = oseMscConfig :: getConfig('', 'obj');
		$mode = ($config->ebs_testmode)? "TEST" : "LIVE" ;
		$ebs_merchantID = $config->ebs_merchantID;	
		$md5 = $config->ebs_secretKey;
		$DeclineURL = JURI :: base().'index.php?option=com_osemsc&view=confirm';
		$AcceptURL = JURI :: base()."components/com_osemsc/ipn/ebs_notify.php?DR={DR}";
		$orderId= $orderInfo->order_id;
		$currency = self::get_iso_code($orderInfo->payment_currency);
		$amount = $orderInfo->payment_price;
		$MD5key = md5( $currency . $amount . $orderId . $md5 );
		$url = "https://secure.ebs.in/pg/ma/sale/pay/";
		$desc = self::generateDesc($orderId);
		
		$payment= oseRegistry :: call('payment');
		$paymentOrder = $payment->getInstance('Order');
		$billinginfo = $paymentOrder->getBillingInfo($orderInfo->user_id);
		$user = JFactory::getUser($orderInfo->user_id);
		
		$html['form']= '<form action="'.$url.'" method="post">';

		// Construct variables for post
		$post_variables = array(
		
			'windowstate' =>'2',
			'language' => '2',
			'declineurl' => $DeclineURL,
			'accepturl' => $AcceptURL,
			'MD5key' => $MD5key,
			'subscription' => '0',
			'account_id' => $ebs_merchantID,
			'reference_no' => $orderId,
			'amount' => $amount,
			'description' => $desc,
			'name' => $billinginfo->firstname.' '.$billinginfo->lastname,
			'address' => $billinginfo->addr1.' '.$billinginfo->addr2,
			'city' => $billinginfo->city,
			'state' => $billinginfo->state,
			'postal_code' => $billinginfo->postcode,
			'country' => $billinginfo->country,
			'email' => $user->email,
			'phone' => $billinginfo->telephone,
			'ship_name' => $billinginfo->firstname.' '.$billinginfo->lastname,
			'ship_address' => $billinginfo->addr1.' '.$billinginfo->addr2,
			'ship_city' => $billinginfo->city,
			'ship_state' => $billinginfo->state,
			'ship_postal_code' => $billinginfo->postcode,
			'ship_country' => $billinginfo->country,
			'ship_phone' => $billinginfo->telephone,
			'return_url' => $AcceptURL,
			'mode' => $mode
			
		);
		//print_r($post_variables);exit;
		$html['form'] .= '<input type="image" id="ebs_image" name="cartImage" src="'."components/com_osemsc/assets/images/checkout.png".'" alt="'.JText :: _('Click to pay with EBS').'" />';
		// Process payment variables;
		$html['url']= $url."?";
		foreach($post_variables as $name => $value) {
			$html['form'] .= '<input type="hidden" name="'.$name.'" value="'.htmlspecialchars($value).'" />';
			$html['url'] .= $name."=".urlencode($value)."&";
		}
		$html['form'] .= '</form>';
		return $html;
	}

	
	function generateDesc($order_id)
	{
		$title = null;
        $db = oseDB::instance();
        $query = "SELECT * FROM `#__osemsc_order_item` WHERE `order_id` = '{$order_id}'";
        $db->setQuery($query);
        $obj = $db->loadObject();
        $params = oseJson::decode($obj->params);
        $msc_id = $obj->entry_id;
       
        $query = "SELECT title FROM `#__osemsc_acl` WHERE `id` = ".(int)$msc_id;
        $db->setQuery($query);
        $msc_name = $db->loadResult();
       
        $msc_option = $params->msc_option;
        $query = "SELECT params FROM `#__osemsc_ext` WHERE `type` = 'payment' AND `id` = ".(int)$msc_id;
        $db->setQuery($query);
        $result = oseJson::decode($db->loadResult());
        foreach($result as $key => $value)
        {
            if($msc_option == $key)
            {
                if($value->recurrence_mode == 'period')
                {
                    if($value->eternal)
                    {
                        $title = 'Life Time Membership';
                    }else{
                       
                        $title = $value->recurrence_num.' '.ucfirst($value->recurrence_unit).' Membership';
                    }
                }else{
                    $start_date = date("l,d F Y",strtotime($value->start_date));
                    $expired_date = date("l,d F Y",strtotime($value->expired_date));
                    $title  = $start_date.' - '. $expired_date.' Membership';
                }
               
            }
        }
        $title = $msc_name.' : '.$title;
        return $title;
	}

function get_iso_code($code) {
      switch ($code) {
      	case 'ADP': return '020'; break;
				case 'AED': return '784'; break;
				case 'AFA': return '004'; break;
				case 'ALL': return '008'; break;
				case 'AMD': return '051'; break;
				case 'ANG': return '532'; break;
				case 'AOA': return '973'; break;
				case 'ARS': return '032'; break;
				case 'AUD': return '036'; break;
				case 'AWG': return '533'; break;
				case 'AZM': return '031'; break;
				case 'BAM': return '977'; break;
				case 'BBD': return '052'; break;
				case 'BDT': return '050'; break;
				case 'BGL': return '100'; break;
				case 'BGN': return '975'; break;
				case 'BHD': return '048'; break;
				case 'BIF': return '108'; break;
				case 'BMD': return '060'; break;
				case 'BND': return '096'; break;
				case 'BOB': return '068'; break;
				case 'BOV': return '984'; break;
				case 'BRL': return '986'; break;
				case 'BSD': return '044'; break;
				case 'BTN': return '064'; break;
				case 'BWP': return '072'; break;
				case 'BYR': return '974'; break;
				case 'BZD': return '084'; break;
				case 'CAD': return '124'; break;
				case 'CDF': return '976'; break;
				case 'CHF': return '756'; break;
				case 'CLF': return '990'; break;
				case 'CLP': return '152'; break;
				case 'CNY': return '156'; break;
				case 'COP': return '170'; break;
				case 'CRC': return '188'; break;
				case 'CUP': return '192'; break;
				case 'CVE': return '132'; break;
				case 'CYP': return '196'; break;
				case 'CZK': return '203'; break;
				case 'DJF': return '262'; break;
				case 'DKK': return '208'; break;
				case 'DOP': return '214'; break;
				case 'DZD': return '012'; break;
				case 'ECS': return '218'; break;
				case 'ECV': return '983'; break;
				case 'EEK': return '233'; break;
				case 'EGP': return '818'; break;
				case 'ERN': return '232'; break;
				case 'ETB': return '230'; break;
				case 'EUR': return '978'; break;
				case 'FJD': return '242'; break;
				case 'FKP': return '238'; break;
				case 'GBP': return '826'; break;
				case 'GEL': return '981'; break;
				case 'GHC': return '288'; break;
				case 'GIP': return '292'; break;
				case 'GMD': return '270'; break;
				case 'GNF': return '324'; break;
				case 'GTQ': return '320'; break;
				case 'GWP': return '624'; break;
				case 'GYD': return '328'; break;
				case 'HKD': return '344'; break;
				case 'HNL': return '340'; break;
				case 'HRK': return '191'; break;
				case 'HTG': return '332'; break;
				case 'HUF': return '348'; break;
				case 'IDR': return '360'; break;
				case 'ILS': return '376'; break;
				case 'INR': return '356'; break;
				case 'IQD': return '368'; break;
				case 'IRR': return '364'; break;
				case 'ISK': return '352'; break;
				case 'JMD': return '388'; break;
				case 'JOD': return '400'; break;
				case 'JPY': return '392'; break;
				case 'KES': return '404'; break;
				case 'KGS': return '417'; break;
				case 'KHR': return '116'; break;
				case 'KMF': return '174'; break;
				case 'KPW': return '408'; break;
				case 'KRW': return '410'; break;
				case 'KWD': return '414'; break;
				case 'KYD': return '136'; break;
				case 'KZT': return '398'; break;
				case 'LAK': return '418'; break;
				case 'LBP': return '422'; break;
				case 'LKR': return '144'; break;
				case 'LRD': return '430'; break;
				case 'LSL': return '426'; break;
				case 'LTL': return '440'; break;
				case 'LVL': return '428'; break;
				case 'LYD': return '434'; break;
				case 'MAD': return '504'; break;
				case 'MDL': return '498'; break;
				case 'MGF': return '450'; break;
				case 'MKD': return '807'; break;
				case 'MMK': return '104'; break;
				case 'MNT': return '496'; break;
				case 'MOP': return '446'; break;
				case 'MRO': return '478'; break;
				case 'MTL': return '470'; break;
				case 'MUR': return '480'; break;
				case 'MVR': return '462'; break;
				case 'MWK': return '454'; break;
				case 'MXN': return '484'; break;
				case 'MXV': return '979'; break;
				case 'MYR': return '458'; break;
				case 'MZM': return '508'; break;
				case 'NAD': return '516'; break;
				case 'NGN': return '566'; break;
				case 'NIO': return '558'; break;
				case 'NOK': return '578'; break;
				case 'NPR': return '524'; break;
				case 'NZD': return '554'; break;
				case 'OMR': return '512'; break;
				case 'PAB': return '590'; break;
				case 'PEN': return '604'; break;
				case 'PGK': return '598'; break;
				case 'PHP': return '608'; break;
				case 'PKR': return '586'; break;
				case 'PLN': return '985'; break;
				case 'PYG': return '600'; break;
				case 'QAR': return '634'; break;
				case 'ROL': return '642'; break;
				case 'RUB': return '643'; break;
				case 'RUR': return '810'; break;
				case 'RWF': return '646'; break;
				case 'SAR': return '682'; break;
				case 'SBD': return '090'; break;
				case 'SCR': return '690'; break;
				case 'SDD': return '736'; break;
				case 'SEK': return '752'; break;
				case 'SGD': return '702'; break;
				case 'SHP': return '654'; break;
				case 'SIT': return '705'; break;
				case 'SKK': return '703'; break;
				case 'SLL': return '694'; break;
				case 'SOS': return '706'; break;
				case 'SRG': return '740'; break;
				case 'STD': return '678'; break;
				case 'SVC': return '222'; break;
				case 'SYP': return '760'; break;
				case 'SZL': return '748'; break;
				case 'THB': return '764'; break;
				case 'TJS': return '972'; break;
				case 'TMM': return '795'; break;
				case 'TND': return '788'; break;
				case 'TOP': return '776'; break;
				case 'TPE': return '626'; break;
				case 'TRL': return '792'; break;
				case 'TRY': return '949'; break;
				case 'TTD': return '780'; break;
				case 'TWD': return '901'; break;
				case 'TZS': return '834'; break;
				case 'UAH': return '980'; break;
				case 'UGX': return '800'; break;
				case 'USD': return '840'; break;
				case 'UYU': return '858'; break;
				case 'UZS': return '860'; break;
				case 'VEB': return '862'; break;
				case 'VND': return '704'; break;
				case 'VUV': return '548'; break;
				case 'XAF': return '950'; break;
				case 'XCD': return '951'; break;
				case 'XOF': return '952'; break;
				case 'XPF': return '953'; break;
				case 'YER': return '886'; break;
				case 'YUM': return '891'; break;
				case 'ZAR': return '710'; break;
				case 'ZMK': return '894'; break;
				case 'ZWD': return '716'; break;
      }
      //
      // As default return 208 for Danish Kroner
      //
      return '208';
    }

}
?>