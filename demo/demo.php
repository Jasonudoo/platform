<?php
define('INCLUDE_CHECK',1);
require "connect.php";

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

	<title>My Shopping Cart</title>
	<link rel="stylesheet" href="example.css" type="text/css" media="screen" charset="utf-8" />
	<!--[if lte IE 6]><link rel="stylesheet" href="ie6.css" type="text/css" media="screen" charset="utf-8" /><![endif]-->
	<!--[if IE 7]><link rel="stylesheet" href="ie7.css" type="text/css" media="screen" charset="utf-8" /><![endif]-->
	<!--Include the SimpleCart(js) script-->
	<script src="javascripts/simpleCart_uncompressed.js" type="text/javascript" charset="utf-8"></script>
	
	<!--Make a new cart instance with your paypal login email-->
	<script type="text/javascript">
		simpleCart = new cart("mycart");
	</script>
	
	<!--CSS for the Cart. Customize anything you damn well please.
		Use Firebug or Webkit's Element Inspector to get a more 
		indepth idea of what simpleCart Generates.
	-->
	<style>
        .product-grid-1 .tab-content { border:1px solid #DDDDDD; border-top:none; background:#fff }
        .product-grid-1 .nav { margin-bottom:0 } /* overrides from bootstrap */
        .product-grid-1 .nav a { color:#aaa; }
        .product-grid-1 .nav .active a { color:#000 }
        .product-grid-1 .tab-content { padding:15px }
        .product-grid-1 .title { font-size:20px; color:#000; font-weight:bold }
        .product-grid-1 .subtitle { font-size:14px; font-weight:bold; margin-bottom:10px }
        .product-grid-1 .highlight { background:#F8F7F6; border:1px solid #EBEAEA; padding:5px 10px; font-size:13px; font-weight:bold; color:#5F5F5F }
        .product-grid-1 address { font-size:12px; }
        .product-grid-1 .about .u { float:left; width:49% } 
		.cartHeaders,.totalRow{display:none;}
		.simpleCart_items{
			overflow-y:auto;
			overflow-x:hidden;
			height:324px;
			width:243px;
			margin-bottom:20px;
		}
		.itemContainer{
			clear:both;
			width:229px;
			padding:11px 0;
			font-size:11px;
		}
		.itemImage{
			float:left;
			width:60px;
		}
		.itemName{
			float:left;
			width:85px;
		}
		.itemPrice{
			float:left;
			width:85px;
			color:#418932;
		}
		.itemQuantity{
			float:left;
			width:33px;
			margin-top:-12px;
			vertical-align:middle;
		}
		.itemQuantity input{
			width:20px;
			border:1px solid #ccc;
			padding:3px 2px;
		}
		
		.itemTotal{
			float:left;
			color:#c23f26;
			margin-top:-6px
		}
	</style>
</head>
<body>
	<div id="topFrame">
<?php
$sql = "SELECT a.*, b.* FROM tbl_virtuemart_categories a
		LEFT JOIN tbl_virtuemart_categories_en_gb AS b ON a.virtuemart_category_id = b.virtuemart_category_id
		WHERE a.published = 1 ORDER BY a.virtuemart_category_id ASC";
$result = mysql_query($sql);
$default_category_id = null;
while($row = mysql_fetch_assoc($result))
{
	if( is_null($default_category_id) )
	{
		$default_category_id = $row['virtuemart_category_id'];
	}
	//echo "<li>";
	echo "<span class='top-label'>";
	echo "<span class='label-txt'><a href='demo.php?cid=". $row['virtuemart_category_id'] . "'>" . $row['category_name'] ."</a></span>";
	echo "</span>";
	//echo "</li>";

}

?>
    </div>
	<div id="content">
		<div id="header">
				<img class="logo" src="images/logo.gif" width="1" height="1" /><span class="logo_font">Shopping Cart</span>
			</div>
			<!--Here's the Catalog Items. You can make anything into a product, 
				just copy and paste the onclick attribute from one of the products 
				below.
			-->
			<ul id="catalog">
<?php
    //var_dump($_SESSION);
    $products = $_SESSION['schedule_cart'];
    //var_dump($products);
    unset($row);
    unset($result);
    $sql = 'SELECT virtuemart_product_id, virtuemart_category_id FROM tbl_virtuemart_product_categories ORDER BY ordering ASC';
    $result = mysql_query($sql);
    
    $category_product = array();
    while($row = mysql_fetch_assoc($result))
    {
    	$category_product[$row['virtuemart_category_id']][] = $row['virtuemart_product_id'];
    }
    $cgid = (isset($_REQUEST['cid']) && !empty($_REQUEST['cid'])) ? $_REQUEST['cid'] : $default_category_id;
    
    unset($row);
    unset($result);
    $items = $category_product[$cgid];
    $sql = 'SELECT a.*, b.* FROM tbl_virtuemart_products AS a 
			LEFT JOIN tbl_virtuemart_products_en_gb AS b ON a.virtuemart_product_id = b.virtuemart_product_id 
			WHERE a.virtuemart_product_id IN (' . implode(',', $items) . ')';
	$result = mysql_query($sql);
	$products = array();
	$i = 0;
	while($row = mysql_fetch_assoc($result))
	{
		$products[$i] = $row;
		
		$sql = 'SELECT c.* FROM tbl_virtuemart_product_medias AS b
				LEFT JOIN tbl_virtuemart_medias AS c ON b.virtuemart_media_id = c.virtuemart_media_id
    			WHERE b.virtuemart_product_id = ' . $row['virtuemart_product_id'];
		$result_1 = mysql_query($sql);
		$row_1 = mysql_fetch_assoc($result_1);
        $products[$i]['image_file_title'] = $row_1['file_title'];
		$products[$i]['image_file_url'] = $row_1['file_url'];
		$products[$i]['image_file_url_thumb'] = $row_1['file_url_thumb'];
        unset($row_1);
        unset($result_1);
        
		$sql = 'SELECT p.product_price, d.currency_code_3, d.currency_symbol FROM tbl_virtuemart_product_prices AS p
				LEFT JOIN tbl_virtuemart_currencies AS d ON d.virtuemart_currency_id = p.product_currency
				WHERE p.virtuemart_product_id = ' . $row['virtuemart_product_id'];
		$result_1 = mysql_query($sql);
		$row_1 = mysql_fetch_assoc($result_1);
		$priceInfo = $row_1;
		unset($row_1);
		unset($result_1);
		
		$sql = 'SELECT c.custom_value, c.custom_price FROM tbl_virtuemart_product_customfields AS c
				WHERE c.virtuemart_custom_id = 3 and c.virtuemart_product_id = ' . $row['virtuemart_product_id'];
		$result_1 = mysql_query($sql);
		$row_1 = mysql_fetch_assoc($result_1);
		$custPrice = $row_1;
		unset($row_1);
		unset($result_1);
		
		$products[$i]['custom_value'] = $custPrice['custom_value'];
		$products[$i]['custom_price'] = $custPrice['custom_price'];
		$products[$i]['currency_symbol'] = $priceInfo['currency_symbol'];
		$products[$i]['currency_code'] = $priceInfo['currency_code_3'];
		$products[$i]['price'] = $custPrice['custom_price'];

		echo '<li>';
		echo '<img src="/' . $products[$i]['image_file_url'] . '" alt="' . htmlspecialchars($products[$i]['product_name']) . '" width="220" height="110"/>';
		echo '<span class="price">' . $products[$i]['custom_value'] . '</span><b>' . htmlspecialchars($products[$i]['product_name']) . 
		    '</b><br/><b><a href="#" onclick="simpleCart.add(\'name=' . htmlspecialchars($products[$i]['product_name']) . 
		    '\',\'price=50\',\'image=/' . $products[$i]['image_file_url_thumb'] . ' \');return false;"> add to cart</a></b>';
		echo '</li>';
		
	}
	unset($row);
	unset($result);
?>
			</ul>
			<!--/ Catalog-->
			
			
			<div id="sidebar">
				<h2>Your Cart</h2>
				
				
				<!--Add a Div with the class "simpleCart_items" to show your shopping cart area.-->
				<div class="simpleCart_items" >
				</div>
				
				
				<!--Here's the Links to Checkout and Empty Cart-->
				<a href="#" class="simpleCart_empty">empty cart</a>
				<a href="#" class="simpleCart_checkout">Checkout</a>
				
			<!--End #sidebar-->	
			</div>
			<div id="footer">
				Created by <a href="http://www.netwebx.com"></a> <a href="http://netwebx.com">NetWebX.COM</a> &nbsp;&nbsp;
			</div>	
		<!--End #content-->		
		</div>
</body>
</html>
