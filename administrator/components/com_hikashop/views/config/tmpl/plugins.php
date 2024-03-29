<?php
/**
 * @package	HikaShop for Joomla!
 * @version	2.1.2
 * @author	hikashop.com
 * @copyright	(C) 2010-2013 HIKARI SOFTWARE. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><div id="config_plugins">
	<fieldset class="adminform">
		<legend><?php echo JText::_('PLUGINS') ?></legend>
		<table class="adminlist table table-striped table-hover" cellpadding="1">
			<thead>
				<tr>
					<th class="title titlenum">
						<?php echo JText::_( 'HIKA_NUM' );?>
					</th>
					<th class="title">
						<?php echo JText::_('HIKA_NAME'); ?>
					</th>
					<th class="title titletoggle">
						<?php echo JText::_('HIKA_ENABLED'); ?>
					</th>
					<th class="title titleid">
						<?php echo JText::_( 'ID' ); ?>
					</th>
				</tr>
			</thead>
			<tbody>
				<?php
					$k = 0;
					for($i = 0,$a = count($this->plugins);$i<$a;$i++){
						$row =& $this->plugins[$i];
						if(version_compare(JVERSION,'1.6','<')){
							$publishedid = 'published-'.$row->id;
							$url = 'index.php?option=com_plugins&amp;view=plugin&amp;client=site&amp;task=edit&amp;cid[]='.$row->id;
						}else{
							$publishedid = 'enabled-'.$row->id;
							$url = 'index.php?option=com_plugins&amp;task=plugin.edit&amp;extension_id='.$row->id;
						}
				?>
					<tr class="<?php echo "row$k"; ?>">
						<td align="center">
						<?php echo $i+1 ?>
						</td>
						<td>
							<a target="_blank" href="<?php echo $url; ?>"><?php echo $row->name; ?></a>
						</td>
						<td align="center">
							<?php if($this->manage){ ?>
								<span id="<?php echo $publishedid ?>" class="loading"><?php echo $this->toggleClass->toggle($publishedid,$row->published,'plugins') ?></span>
							<?php }else{ echo $this->toggleClass->display('activate',$row->published); } ?>
						</td>
						<td align="center">
							<?php echo $row->id; ?>
						</td>
					</tr>
				<?php
						$k = 1-$k;
					}
				?>
			</tbody>
		</table>
	</fieldset>
</div>
