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
			if(!isset($this->cart)) $this->cart = hikashop_get('helper.cart');
			if(!empty($this->row->has_options)){
				echo $this->cart->displayButton(JText::_('CHOOSE_OPTIONS'),'choose_options',$this->params,hikashop_completeLink('product&task=show&product_id='.$this->row->product_id.'&name='.$this->row->alias.$this->itemid.$this->category_pathway),'window.location = \''.str_replace("'","\'",hikashop_completeLink('product&task=show&product_id='.$this->row->product_id.'&name='.$this->row->alias.$this->itemid.$this->category_pathway)).'\';return false;','');
			}else{
				$config =& hikashop_config();
				$this->cart = hikashop_get('helper.cart');
				$url = '';
				$module_id = $this->params->get('from_module',0);
				if(empty($this->ajax)){
					$this->ajax = 'return hikashopModifyQuantity(\''.$this->row->product_id.'\',field,1,0,\'cart\','.$module_id.')';
				}
				if(@$this->row->product_sale_start || empty($this->element->main)){
					$start_date = @$this->row->product_sale_start;
				}else{
					$start_date = $this->element->main->product_sale_start;
				}
				if(@$this->row->product_sale_end || empty($this->element->main)){
					$end_date = @$this->row->product_sale_end;
				}else{
					$end_date = $this->element->main->product_sale_end;
				}

				if($end_date && $end_date<time()){
					?>
					<span class="hikashop_product_sale_end">
						<?php echo JText::_('ITEM_NOT_SOLD_ANYMORE'); ?>
					</span>
					<?php
				}elseif($start_date && $start_date>time()){
					?>
					<span class="hikashop_product_sale_start">
						<?php
						echo JText::sprintf('ITEM_SOLD_ON_DATE',hikashop_getDate($start_date,$this->params->get('date_format','%d %B %Y')));
						?>
					</span>
					<?php
				}elseif(!$this->params->get('catalogue') && ($this->config->get('display_add_to_cart_for_free_products') || !empty($this->row->prices))){
					if(@$this->row->product_min_per_order || empty($this->element->main)){
						$min = @$this->row->product_min_per_order;
					}else{
						$min = @$this->element->main->product_min_per_order;
					}
					if(@$this->row->product_max_per_order || empty($this->element->main)){
						$max = @$this->row->product_max_per_order;
					}else{
						$max = @$this->element->main->product_max_per_order;
					}
					if($min<=0){
						$min=1;
					}
					$formName = ',0';

					if (!$this->config->get('ajax_add_to_cart', 0) || ($this->config->get('show_quantity_field')>=2 && !@$this->element->product_id)) {
						if(empty($this->formName)) {
							if(@$this->row->product_id)
								$formName = ',\'hikashop_product_form_'.$this->row->product_id.'_'.$this->params->get('main_div_name').'\'';
							else
								$formName = ',\'hikashop_product_form_'.$this->params->get('main_div_name').'\'';
						} else {
							$formName = $this->formName;
						}
					}
					$wishlistAjax =	'if(hikashopCheckChangeForm(\'item\''.$formName.')){ return hikashopModifyQuantity(\'' . (int)@$this->row->product_id . '\',field,1' . $formName . ',\'wishlist\','.$module_id.'); } else { return false; }';

					if($this->row->product_quantity == -1 && !empty($this->element->main) && $this->element->main->product_quantity != -1){
						$this->row->product_quantity = $this->element->main->product_quantity;
					}
					$btnType = 'add';
					if($this->row->product_quantity==-1){
					?>
					<div class="hikashop_product_stock">
					<?php
						if($this->params->get('add_to_cart',1)){
							echo $this->cart->displayButton(JText::_('ADD_TO_CART'),'add',$this->params,$url,$this->ajax,'',$max,$min);
							$btnType = 'wish';
						}
						if($this->params->get('add_to_wishlist') && $config->get('enable_wishlist', 1) && (($config->get('hide_wishlist_guest', 1) && hikashop_loadUser() != null) || !$config->get('hide_wishlist_guest', 1))){
							echo '<div id="hikashop_add_wishlist">';
								echo $this->cart->displayButton(JText::_('ADD_TO_WISHLIST'),$btnType,$this->params,$url,$wishlistAjax,'',$max,$min,'',false);
							echo '</div>';
						}
					}elseif($this->row->product_quantity>0){
					?>
					<div class="hikashop_product_stock">
					<?php
						echo '<span class="hikashop_product_stock_count">'.JText::sprintf('X_ITEMS_IN_STOCK',$this->row->product_quantity).'</span><br/>';
						if($config->get('button_style','normal')=='css'){
							echo '<br />';
						}
						if($max<=0 || $max>$this->row->product_quantity) $max = $this->row->product_quantity;
						if($this->params->get('add_to_cart',1)){
							echo $this->cart->displayButton(JText::_('ADD_TO_CART'),'add',$this->params,$url,$this->ajax,'',$max,$min);
							$btnType = 'wish';
						}
						if($this->params->get('add_to_wishlist')  && $config->get('enable_wishlist', 1) && (($config->get('hide_wishlist_guest', 1) && hikashop_loadUser() != null) || !$config->get('hide_wishlist_guest', 1))){
							echo '<div id="hikashop_add_wishlist">';
								echo $this->cart->displayButton(JText::_('ADD_TO_WISHLIST'),$btnType,$this->params,$url,$wishlistAjax,'',$max,$min,'',false);
							echo '</div>';
						}
					}else{
						?>
					<div class="hikashop_product_no_stock">
					<?php
						echo JText::_('NO_STOCK').'<br/>';
						$waitlist = $this->config->get('product_waitlist',0);
						if(hikashop_level(1) && ($waitlist==2 || ($waitlist==1 && (!empty($this->element->main->product_waitlist) || !empty($this->element->product_waitlist))))){ ?>
							</div><div id="hikashop_product_waitlist_main" class="hikashop_product_waitlist_main">
							<?php
							$empty='';
							jimport('joomla.html.parameter');
							$params = new HikaParameter($empty);
							echo $this->cart->displayButton(JText::_('ADD_ME_WAITLIST'),'add_waitlist',$params,hikashop_completeLink('product&task=waitlist&cid='.$this->row->product_id),'window.location=\''.str_replace("'","\'",hikashop_completeLink('product&task=waitlist&cid='.$this->row->product_id)).'\';return false;');


						}
						if($this->params->get('add_to_wishlist')  && $config->get('enable_wishlist', 1)  && (($config->get('hide_wishlist_guest', 1) && hikashop_loadUser() != null) || !$config->get('hide_wishlist_guest', 1))){
							echo '<div id="hikashop_add_wishlist">';
								echo $this->cart->displayButton(JText::_('ADD_TO_WISHLIST'),'add',$this->params,$url,$wishlistAjax,'',@$this->row->product_max_per_order,1,'',false);
							echo '</div>';
						}
					}?>
					</div>
				<?php
				}
			}
				?>
