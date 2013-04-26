<?php
/**
* Javascript file take from: http://woork.blogspot.com/2008/07/fantastic-news-ticker-newsvine-like.html
* *** Requirements ***
* VirtueMart component v2.0.14 Beta
* 
*
* @Copyright Copyright (C) 2009 - 2010 ... Oh-Taek Im
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*
*/
if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' );
define('JPATH_VM_ADMINISTRATOR',JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_virtuemart');

require_once (JPATH_SITE.DS.'components'.DS.'com_content'.DS.'helpers'.DS.'route.php');
require_once( JPATH_VM_ADMINISTRATOR.DS.'helpers'.DS.'config.php' );

require_once( JPATH_VM_ADMINISTRATOR.DS.'models'.DS.'product.php' );


	class modVMScrollerHelper
	{
                var $loadjQuery;
                var $loadjQueryUI;
                var $loadScript; 

		var $itemWidth;
		var $titleHeight;
		var $spacer;
		var $speed;
		var $delay;
		var $transition;
		var $moduleWidth;
		var $moduleHeight;

		var $imageSize;
		var $title;

		var $bgcolor;
		var $panelcolor;
		var $bordercolor;
		var $borderthick;
		var $panelmargin;
		var $titlecolor;
		var $titlecolorhover;
		var $titlealign;
		var $imagealign;
		var $detailcolor;
		var $detailalign;
		var $effects;
		var $slideid;
		var $load_mootools;
		var $direct;

		var $show_product_name;
		var $show_addtocart;
		var $show_price;
		var $show_detail;
		var $cart_text;
		var $category_id;
		var $NumberOfProducts;
		var $featuredProducts;
		var $ScrollSortMethod;

		var $rows;
		var $ps_product;
		var $ps_product_attribute;
		var $prodlist;
		var $rand_prods;

		function modVMScrollerHelper(&$params)
		{
			global $scroller_id;

                        VmConfig::loadConfig();
                          
			
			$document =& JFactory::getDocument();
                        
                        $this->loadjQuery 				= $params->get('loadjQuery');
                        $this->loadjQueryUI 				= $params->get('loadjQueryUI');
                        $this->loadScript                               = $params->get('loadScript');

			$this->slideid						= ++$scroller_id;
			

                        if ($this->loadjQuery==1) {
				echo '
                                   <script type="text/javascript"> 
                                   if(jQuery) jQuery.noConflict();
                                   </script>
                                '; 
                                echo '<script type="text/javascript" src="'.JURI::base() . 'modules/mod_vmscroller/jquery.min.js'.'" ></script>';
			}
                        if ($this->loadjQueryUI==1) {
				
                                echo '<script type="text/javascript" src="'.JURI::base() . 'modules/mod_vmscroller/jquery-ui.min.js'.'" ></script>';
			} 
                        
                        if($this->loadScript != 0) 
                        echo '<script type="text/javascript" src="'.JURI::base() . 'modules/mod_vmscroller/hot_vmscroller.js'.'" ></script>';
			

			$this->titlecolor 						= $params->get('titlecolor');
			$this->titlecolorhover					= $params->get('titlecolorhover');
			$this->titlealign 						= $params->get('titlealign');
			$this->detailcolor 						= $params->get('detailcolor');
			
			
// style declaration - added by HotJoomlaTemplates.com



$document->addStyleDeclaration( '

#VMScroller'.$this->slideid.'{
	position:relative;
}

#ScrollerHorizontal'.$this->slideid.' {
	display: block;
	overflow: hidden;
	position: relative;
	text-align: center;
}

#VMScrollBoth'.$this->slideid.' {
	display: block;
	list-style: none;
	margin: 0;
	padding: 0;
}

#VMScrollBoth'.$this->slideid.' li {
	text-align: center;
	color: #cccccc;
	float: left;
	display: inline;
	background-image:none;
}

#VMScrollBoth'.$this->slideid.' li .VMTitle'. $this->slideid.' {
	display: block;
	color: #c3c138;
	text-align:'. $this->titlealign.';
	font-size: 12px;
	font-weight:bold;
	padding-top: 4px;
	line-height: 1.1em;
	background-image:none;
}

#VMScrollBoth'.$this->slideid.' li .VMTitle'.$this->slideid.' a:link,
#VMScrollBoth'.$this->slideid.' li .VMTitle'.$this->slideid.' a:Visited {
	display: block;
	color:'.$this->titlecolor.';
}

#VMScrollBoth'.$this->slideid.' li .VMTitle'.$this->slideid.' a:hover {
	text-decoration:none;
	color:'.$this->titlecolorhover.';
}

#VMScrollBoth'.$this->slideid.' li .VMImg'.$this->slideid.' {
	margin-bottom:5px;

}

#VMScrollBoth'.$this->slideid.' li .VMDetail{
display: block;
font-size: 11px;
padding: 2px 5px;
line-height: 1.1em;
color:#447e3e;
}

#VMScrollBoth'.$this->slideid.' li .{
display: block;
font-size: 11px;
margin-top: 3px;
line-height: 1.1em;
color:'.$this->detailcolor.';
text-align:center;
}

#VMScrollBoth'.$this->slideid.' p {
margin: 0px;
padding: 0px;
line-height: 1.0em;
}

#VMScrollBoth'.$this->slideid.' img {
margin-top: 2px;
}

.productPrice {
color:'.$this->detailcolor.';
font-weight:bold;
white-space: nowrap;
}
.product-Old-Price {
color:red;
text-decoration:line-through;
}

' );


			$this->itemWidth 						= $params->get('itemWidth');
			$this->moduleWidth 						= $params->get('moduleWidth');
			$this->moduleHeight 					= $params->get('moduleHeight');
			$this->titleHeight 						= $params->get('titleHeight');
			$this->spacer 							= $params->get('spacer');
			$this->speed 							= $params->get('speed');
			$this->delay 							= $params->get('delay');		
			$this->transition 						= $params->get('transition');
			$this->direction 						= $params->get('direction');
			$this->bgcolor 							= $params->get('bgcolor');
			$this->panelcolor 						= $params->get('panelcolor');
			$this->bordercolor 						= $params->get('bordercolor');
			$this->borderthick 						= $params->get('borderthick');
			$this->panelmargin 						= $params->get('panelmargin');
			
			$this->imagealign 						= $params->get('imageAlign');
			
			$this->detailalign 						= $params->get('detailalign');
			$this->imageSize						= $params->get('imageSize');
			$this->show_product_name                = $params->get('show_product_name', "yes");
			$this->show_addtocart                   = $params->get('show_addtocart', "yes");
			$this->show_price                       = $params->get('show_price', "yes");
			$this->show_detail                 		= $params->get('show_Detail', "yes");
			$this->category_id                      = intval($params->get('categoryId', 0 ));
			$this->NumberOfProducts 				= intval($params->get( 'NumberOfProducts', 10 ));
			$this->featured							= intval($params->get('featuredProducts', 0 ));
			$this->SortMethod						= $params->get('ScrollSortMethod');
			$this->cart_text						= $params->get('cart_text');

			if ($this->direction == 0) {
				$this->direct = 'horizontal';
			} else {
				$this->direct = 'vertical';
			}

			switch ($this->transition)
			{
				case 'linear':
					$this->effects = 'Fx.Transitions.linear';
					break;

				case 'cubic':
					$this->effects = 'Fx.Transitions.Cubic.easeIn';
					break;

				case 'expoin':
					$this->effects = 'Fx.Transitions.Expo.easeIn';
					break;

				case 'expoout':
					$this->effects = 'Fx.Transitions.Expo.easeOut';
					break;

				case 'bounce':
					$this->effects = 'Fx.Transitions.Bounce.easeOut';
					break;

				case 'back':
					$this->effects = 'Fx.Transitions.Back.easeOut';
					break;

				case 'elastic':
					$this->effects = 'Fx.Transitions.Elastic.easeOut';
					break;
			}


			
                        $db = JFactory::getDBO();                        

			$limit = 'LIMIT ' . $this->NumberOfProducts;
			
			if ( $this->category_id ) {
                                $q = "SELECT DISTINCT P.virtuemart_product_id FROM 
                                       #__virtuemart_products as P 
                                       LEFT JOIN 
                                       #__virtuemart_product_categories as PC on PC.virtuemart_product_id = P.virtuemart_product_id 	  
                                       WHERE 
                                       P.published = 1  
                                       AND PC.virtuemart_category_id = ".$this->category_id;  

                                if( VmConfig::get('check_stock') && VmConfig::get('show_out_of_stock_products') != "1") {
					$q .= " AND P.product_in_stock > 0 ";
				}
				if( $this->featured == 1 ) {
					$q .= " AND P.product_special = '1' ";
				}
			}
			else {
				
                                $q  = "SELECT  DISTINCT P.virtuemart_product_id FROM 
                                       #__virtuemart_products as P
                                       WHERE
                                       P.product_parent_id='' AND P.published = 1";  

				if( VmConfig::get('check_stock') && VmConfig::get('show_out_of_stock_products') != "1") {
					$q .= " AND P.product_in_stock > 0 ";
				}
				if( $this->featured == 1 ) {
					$q .= " AND P.product_special = '1' ";
				}
                                
			}
			
			switch( $this->SortMethod ) {
				case 'random':
					$q .= ' ORDER BY RAND() ' . $limit;	break;
				case 'newest':
					$q .= ' ORDER BY P.created_on DESC ' . $limit;	break;
				case 'oldest':
					$q .= ' ORDER BY P.created_on ASC ' . $limit; break;
				default:
					$q .= ' ORDER BY P.created_on DESC ' . $limit;	break;
			}
                       
			$db->setQuery($q);
                        $rows = $db->loadAssocList();

			$i = 0;
			while($i < count($rows)){
				$this->prodlist[$i]=$rows[$i]["virtuemart_product_id"];
				$i++;
                              
			}

			if(count($rows) == 0) {
				return;
			}
			
			$counts = count($rows);
			
			if ($counts < $this->NumberOfProducts) {
				$this->NumberOfProducts = $counts;
			}
		}

		function product_snapshot_new( $product_id, $show_product_name = true, $show_price=true, $show_detail=true, $show_addtocart=true, $cart_text2="add to cart" ) {
			global $sess, $mm_action_url;

                        $product_manager = new VirtueMartModelProduct;
                        $pr = $product_manager->getProduct($product_id); 
                      


                        if ($pr) {
                           
                               
                               $db = JFactory::getDBO();
		               $query="SELECT * FROM #__virtuemart_medias where virtuemart_media_id = '".$pr->virtuemart_media_id[0]."'";
		               $db->setQuery( $query );
		               $image = $db->loadObject();
                          
                               
                          
                          
                         	$html="";
				//$cid = $ps_product_category->get_cid( $product_id );
                                $cid = $pr->virtuemart_category_id;
                                   
				if ($pr->product_parent_id) {
					$url = "?page=shop.product_details&category_id=$cid";
					$url .= "&product_id=" . $pr->product_parent_id;
				} else {
					$url = "?page=shop.product_details&category_id=$cid";
					$url .= "&product_id=" . $pr->virtuemart_product_id;
                                     
				}

				$product_link = $pr->link;

                                if ($show_product_name) {
					$html .= '<span class="VMTitle'.$this->slideid.'" style="color: '.$this->titlecolor.'; text-align: '.$this->titlealign.';">'.'<a href="'. $product_link .'" title="'.$pr->product_name.'"><img src="'.JURI::base(true).'/'.$image->file_url_thumb.'" class="VMImg'.$this->slideid.'" style="width: '.$this->imageSize.';" alt="'.$pr->product_name.'" /><br/>'.$pr->product_name.'</a></span>';
				} else {
					$html .= '<span class="VMTitle'.$this->slideid.'">'.'<a href="'. $product_link .'" title=""><img src="'.JURI::base(true).'/'.$image->file_url_thumb.'" class="VMImg'.$this->slideid.'" style="width: '.$this->imageSize.';" alt="'.$pr->product_name.'" /></a></span>';
				}

				if ($show_price) {			
					$db->setQuery("SELECT REPLACE(REPLACE(C.currency_positive_style,'{symbol}',                                C.currency_symbol ),'{number}',cast(P.product_price as decimal(19,2))) As pricetext 
                                             FROM  
                                             #__virtuemart_product_prices as P 
                                             LEFT JOIN 
                                             #__virtuemart_currencies as C on C.virtuemart_currency_id = P.product_currency 
                                             WHERE P.virtuemart_product_id = '" . $product_id . "'  LIMIT 1 ");


                                        $price_object = $db->loadObject();
					
					
					//$price = $pr->prices['salesPrice'];
         
                                        
					$price = '<span class="productPrice">'.$price_object->pricetext.'</span>';
                                        

				}
				else $price='';

				if ($show_detail) {
					$html .= '<span class="VMDetail" style="color: '.$this->detailcolor.'; text-align: '.$this->detailalign.'" />'.$pr->product_s_desc.'</span>';
				}
				if ($show_price) {
					$html .= '<div style="border-bottom:solid '.$this->borderthick.' '.$this->bordercolor.'; line-height:36px;">'.$price;	
				}

				if(	$show_addtocart == 1){
					$html .= '<form action="'.JURI::base().'index.php?option=com_virtuemart" method="post" name="addtocart" >
				<input type="hidden" name="option" value="com_virtuemart" />
				<input type="hidden" name="page" value="shop.cart" />
				<input type="hidden" name="func" value="cartAdd" />
				<input type="hidden" name="prod_id" value="'. ($product_id).'" />
				<input type="hidden" name="product_id" value="'. ($product_id).'" />
				<input type="hidden" name="quantity" value="1" />
				<input type="hidden" name="set_price[]" value="" />
				<input type="hidden" name="adjust_price[]" value="" />
				<input type="hidden" name="master_product[]" value="" />
				<input type="submit" class="addtocart_button_module2" value="'.$cart_text2.'" title="Add to cart" />
				</form>';}
				else $html .= '</div>';

				return $html;
			}

			return '';
		}	

		function render()
		{
			$show_product_name = ( $this->show_product_name == "yes" ) ? true : false;
			$show_addtocart = ( $this->show_addtocart == "yes" ) ? true : false;
			$show_price = ( $this->show_price == "yes" ) ? true : false;
			$show_detail = ( $this->show_detail == "yes" ) ? true : false;
			$temp = "<!-- VirtueMart Scroller by http://www.photoindochina.com/ --> \n";
			$templink = '';

			for($i=0; $i<$this->NumberOfProducts; $i++) {
				$templink = modVMScrollerHelper::product_snapshot_new( $this->prodlist[$i], $show_product_name, $show_price, $show_detail, $show_addtocart,  $this->cart_text);
          		$temp .= modVMScrollerHelper::addItem($templink);
			}

			$temp = $this->addContent($temp);		
			$temp = $this->addWrapper($temp);		
			$temp = $temp.'<script language="javascript" type="text/javascript">';
			

                        $temp = $temp."
                            var VmsInit".$this->slideid." = function(){
                              
                              jQuery('"."#"."VMScrollBoth".$this->slideid."').hotvmscroller({
                               speed:".$this->speed.",
                               delay:".$this->delay.",
                               transition:'".$this->transition."',
                               direction:'".$this->direct."'
                               });
                          };  

                          if(jQuery.hjt.hotvmscroller){
                            jQuery(document).ready(VmsInit".$this->slideid.");
                          }else{
                            if (window.addEventListener) 
                            {
                              window.addEventListener('load', VmsInit".$this->slideid.", false);
                            } 
                            else if (window.attachEvent) // Microsoft
                            {
                              window.attachEvent('onload', VmsInit".$this->slideid.");
                            }
                          }
                          ";
                        

			$temp = $temp.'</script>';

                        


			echo $temp;
		}

		function addWrapper($text)
		{
			$begin = '<div id="VMScroller'.$this->slideid.'" style="width:'.$this->moduleWidth.';">'."\n".'<div id="ScrollerHorizontal'.$this->slideid.'" style="width:'.$this->moduleWidth.'; height:'.$this->moduleHeight.';">';
			$end = '</div></div>'."\n";
			return $begin.$text.$end;
		}

		function addContent($text)
		{
			$begin = '<div id="VMScrollBoth'.$this->slideid.'" style="width:'.$this->moduleWidth.'; height:'.$this->moduleHeight.'; background-color: '.$this->bgcolor.';">';
			$end   = '</div>'."\n";
			return $begin.$text.$end;
		}

		function addItem($item)
		{
			if ($this->direction == 0) {
				$begin = '<ul><li class="VMScrollBoth'.$this->slideid.'" style="height:'.$this->moduleHeight.'; width:'.$this->itemWidth.'; margin-right: '.$this->spacer.';  border:solid '.$this->borderthick.' '.$this->bordercolor.'; background-color: '.$this->panelcolor.'; text-align: '.$this->imagealign.'" >'."\n";
			}
			else {
				$begin = '<ul><li class="VMScrollBoth'.$this->slideid.'" style="height:auto; width:'.$this->itemWidth.'; margin-bottom: '.$this->spacer.'; background-color: '.$this->panelcolor.'; text-align: '.$this->imagealign.'" >'."\n";
			}

			$end = '</li></ul>'."\n";
			return $begin.$item.$end;
		}
	}
?>