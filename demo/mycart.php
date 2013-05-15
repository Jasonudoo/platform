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
		simpleCart = new cart("jasonudoo@gmail.com");
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

		.cartHeaders div{
			color:#333;
		}
		.simpleCart_items{
			float:left;
			clear:left;
			width:446px;
			margin:20px;
		}
		.itemContainer{
			clear:both;
			width:543px;
			padding:15px;
			font-size:11px;
			line-height:25px;
			height:10px;
		}
		.itemImage{
			float:left;
			width:80px;
		}
		.itemImage img{
			vertical-align:middle;
		}
		.itemName{
			float:left;
			width:160px;
		}
		.itemPrice{
			float:left;
			width:55px;
			color:#418932;
		}
		.itemQuantity{
			float:left;
			width:105px;
			vertical-align:middle;
			text-align:center;
		}
		.itemQuantity input{
			width:20px;
			border:1px solid #ccc;
			padding:3px 2px;
			margin:0 auto;
			display:block;
			font-size:11px;
		}
		.itemTotal{
			float:left;
			color:#c23f26;
			text-align:right;
			width:45px;
		}
		.totalRow{
			clear:both;
			float:left;
			margin-top:10px;
		}
		.totalItems{
			float:left;
			width:105px;
			margin-left:295px;
			text-align:center;
		}
		.totalPrice{
			float:right;
			text-align:right;
		}
		.itemDesc{
			float:left;
			width:120px;
		}

	</style>
</head>
<body>
	<div id="topFrame"></div>
	<div id="content">
		<div id="sidebar" style="margin-top:20px">
			<h2>You Might Also Like</h2>
			<div class="alsoContainer">
				<div class="alsoImage">
					<img src="images/thumbs/blackGold.jpg" alt="" />
				</div>
				<div class="alsoInfo">
					Black Gold<br/>
					<a href="#" onclick="simpleCart.add('name=Black Gold','price=58','image=images/thumbs/blackGold.jpg');return false;">add to cart</a>
				</div>
				<div class="alsoPrice">$58</div>
			</div>
			<div class="alsoContainer">
				<div class="alsoImage">
					<img src="images/thumbs/goldShoe.jpg" alt="" />
				</div>
				<div class="alsoInfo">
					Gold Shoe<br/>
					<a href="#" onclick="simpleCart.add('name=Gold Shoe','price=70','image=images/thumbs/goldShoe.jpg');return false;">add to cart</a>
				</div>
				<div class="alsoPrice">$58</div>
			</div>
				
			<div class="alsoContainer">
				<div class="alsoImage">
					<img src="images/thumbs/greenStripe.jpg" alt="" />
				</div>
				<div class="alsoInfo">
					Green Stripe<br/>
					<a href="#" onclick="simpleCart.add('name=Green Stripe','price=90','image=images/thumbs/greenStripe.jpg');return false;">add to cart</a>
				</div>
				<div class="alsoPrice">$58</div>
			</div>	
			<div id="downloadContainer" style="margin:250px 0 35px 0;float:left">
				<h3>Total Price</h3>
				<a href="#"><img src="images/smallLogo.gif" alt="Download simpleCart(js)" style="margin-top:2px;vertical-align:bottom"/></a>&nbsp;&nbsp;&nbsp;&nbsp;
				<a href="#">Version 1.0</a>&nbsp;&nbsp;<a href="#"><img src="images/arrow.gif" alt="Download simpleCart(js)"></a>
			</div>	
			<!--End #sidebar-->	
		</div>
		<div id="header">
			<span class="logo_font">Shopping Cart</span>
		</div>
		<div id="left">
		<!--Add a Div with the class "simpleCart_items" to show your shopping cart area.-->
			<div class="simpleCart_item" >
			    <div class="cartHeader">
			        <div class="itemImage">Image</div>
			        <div class="itemName">Name</div>
			        <div class="itemDesc">Description</div>
			        <div class="itemPrice">Price</div>
			        <div class="itemQuantity">Quantity</div>
			        <div class="itemTotal">Total</div>
			    </div>
			    <div class="totalRow">
			        <div class="totalItems"></div>
			        <div class="totalPrice"></div>
			    </div>
			</div>
				
			<div class="checkoutEmptyLinks">
				<!--Here's the Links to Checkout and Empty Cart-->
				<a href="#" class="simpleCart_empty">Modify Cart</a>
				<a href="#" class="simpleCart_checkout">Checkout</a>
			</div>
		</div>
			
		<div id="footer" class="cartFoot">
		</div>	
		<!--End #content-->		
	</div>
</body>
</html>	