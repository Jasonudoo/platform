<?php
define('INCLUDE_CHECK',1);
require "connect.php";

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Schedule Order Front demo</title>

<link rel="stylesheet" type="text/css" href="demo.css" />

<!--[if lt IE 7]>
<style type="text/css">
	.pngfix { behavior: url(pngfix/iepngfix.htc);}
    .tooltip{width:200px;};
</style>
<![endif]-->


<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
<script type="text/javascript" src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
<script type="text/javascript" src="simpletip/jquery.simpletip-1.3.1.pack.js"></script>
<script type="text/javascript" src="script.js"></script>
</head>

<body>
<div id="main-container">
	<div class="tutorialzine">
    <h1>Shopping cart</h1>
    <h3>The best products at the best prices</h3>
    </div>

    <div class="container">
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
	echo "<span class='top-label'>";
	echo "<span class='label-txt'><a href='demo.php?cid=". $row['virtuemart_category_id'] . "'>" . $row['category_name'] ."</a></span>";
	echo "</span>";
	
}
?>    
        <div class="content-area">
    		<div class="content drag-desired">
<?php
var_dump($_SESSION);
    $Products = $_SESSION['schedule_cart'];
    var_dump($Products);
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
		
		echo '<div class="product"><img src="' . 
		    $products[$i]['image_file_url_thumb'] . 
		    '" style="width:120px;height:120px" alt="' . 
		    htmlspecialchars($products[$i]['product_name']) . 
		    '" width="128" height="128" class="pngfix" /></div>';
	}
	unset($row);
	unset($result);
?>
       	        <div class="clear"></div>
            </div>

        </div>
        
        <div class="bottom-container-border">
        </div>

    </div>



    <div class="container" style='float:left; width:300px'>
    
    	<span class="top-label">
            <span class="label-txt">Shopping Cart</span>
        </span>
        
        <div class="content-area">
    
    		<div class="content drop-here">
            	<div id="cart-icon">
	            	<img src="img/Shoppingcart_128x128.png" alt="shopping cart" class="pngfix" width="128" height="128" />
					<img src="img/ajax_load_2.gif" alt="loading.." id="ajax-loader" width="16" height="16" />
                </div>

				<form name="checkoutForm" method="post" action="order.php">
                
                <div id="item-list">
                </div>
                
				</form>                
                <div class="clear"></div>

				<div id="total"></div>

       	        <div class="clear"></div>
                
                <a href="" onclick="document.forms.checkoutForm.submit(); return false;" class="button">Checkout</a>
                
          </div>

        </div>
        
        <div class="bottom-container-border">
        </div>

    </div>

</div>

</body>
</html>
