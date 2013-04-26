<?php
/**
 * @version		$Id: modules.php 14276 2010-01-18 14:20:28Z louis $
 * @package		Joomla.Site
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

/**
 * This is a file to add template specific chrome to module rendering.  To use it you would
 * set the style attribute for the given module(s) include in your template to use the style
 * for each given modChrome function.
 *
 * eg.  To render a module mod_test in the sliders style, you would use the following include:
 * <jdoc:include type="module" name="test" style="slider" />
 *
 * This gives template designers ultimate control over how modules are rendered.
 *
 * NOTICE: All chrome wrapping methods should be named: modChrome_{STYLE} and take the same
 * two arguments.
 */

/*
 * Module chrome for rendering the module in a slider
 */
function modChrome_slider($module, &$params, &$attribs)
{
	echo JHtml::_('sliders.panel', JText::_($module->title), 'module' . $module->id);
	echo $module->content;
}




function modChrome_hide($module, &$params, &$attribs)
{
	$headerLevel = isset($attribs['headerLevel']) ? (int) $attribs['headerLevel'] : 3;
	$state=isset($attribs['state']) ? (int) $attribs['state'] :0;

	if (!empty ($module->content)) { ?>

<div
	class="moduletable_js <?php echo htmlspecialchars($params->get('moduleclass_sfx'));?>"><?php if ($module->showtitle) : ?>
<h<?php echo $headerLevel; ?> class="js_heading"><span class="backh"> <span
	class="backh1"><?php echo $module->title; ?> <a href="#"
	title="<?php echo JText::_('TPL_FLY_CLICK'); ?>"
	onclick="auf('module_<?php echo $module->id; ?>'); return false"
	class="opencloselink" id="link_<?php echo $module->id?>"> <span
	class="no"><img src="templates/fly/images/plus.png"
	alt="<?php if ($state == 1) { echo JText::_('TPL_FLY_ALTOPEN');} else {echo JText::_('TPL_FLY_ALTCLOSE');} ?>" />
</span></a></span></span></h<?php echo $headerLevel; ?>> <?php endif; ?>
<div class="module_content <?php if ($state==1){echo "open";} ?>"
	id="module_<?php echo $module->id; ?>" tabindex="-1"><?php echo $module->content; ?></div>
</div>
	<?php }
}

/**
 * tabs chrome.
 *
 * @since	1.6
 */
function modChrome_tabs($module, $params, $attribs)
{
	$area = isset($attribs['id']) ? (int) $attribs['id'] :'1';
	$area = 'area-'.$area;

	static $modulecount;
	static $modules;

	if ($modulecount < 1) {
		$modulecount = count(JModuleHelper::getModules($attribs['name']));
		$modules = array();
	}

	if ($modulecount == 1) {
		$temp = new stdClass();
		$temp->content = $module->content;
		$temp->title = $module->title;
		$temp->params = $module->params;
		$temp->id=$module->id;
		$modules[] = $temp;
		// list of moduletitles
		// list of moduletitles
		echo '<div id="'. $area.'" class="tabouter"><ul class="tabs">';

		foreach($modules as $rendermodule) {
			echo '<li class="tab"><a href="#" id="link_'.$rendermodule->id.'" class="linkopen" onclick="tabshow(\'module_'. $rendermodule->id.'\');return false">'.$rendermodule->title.'</a></li>';
		}
		echo '</ul>';
		$counter=0;
		// modulecontent
		foreach($modules as $rendermodule) {
			$counter ++;

			echo '<div tabindex="-1" class="tabcontent tabopen" id="module_'.$rendermodule->id.'">';
			echo $rendermodule->content;
			if ($counter!= count($modules))
			{
			echo '<a href="#" class="unseen" onclick="nexttab(\'module_'. $rendermodule->id.'\');return false;" id="next_'.$rendermodule->id.'">'.JText::_('TPL_FLY_NEXTTAB').'</a>';
			}
			echo '</div>';
		}
		$modulecount--;
		echo '</div>';
	} else {
		$temp = new stdClass();
		$temp->content = $module->content;
		$temp->params = $module->params;
		$temp->title = $module->title;
		$temp->id = $module->id;
		$modules[] = $temp;
		$modulecount--;
	}
}
function modChrome_container($module, &$params, &$attribs)
{
	if (!empty ($module->content)) : ?>
		<div class="container">
			<?php echo $module->content; ?>
		</div>
	<?php endif;
}
function modChrome_bottommodule($module, &$params, &$attribs)
{
	if (!empty ($module->content)) : ?>
		<?php if ($module->showtitle) : ?>
			<h6><?php echo $module->title; ?></h6>
		<?php endif; ?>
		<?php echo $module->content; ?>
	<?php endif;
}
function modChrome_side($module, &$params, &$attribs)
{
	if (!empty ($module->content)) : ?>
	<div class="module module<?php echo $params->get('moduleclass_sfx'); ?> module<?php echo $module->id ?>">
		<div class="inner">
			<?php if ($module->showtitle) : ?>
			<div class="heading">
				<div class="title">
					<?php echo $module->title; ?>
				</div>
			</div>
			<?php endif; ?>
			<div class="content">
				<?php echo $module->content; ?>
			</div>
		</div>
	</div>
	<?php endif;
}
function modChrome_top($module, &$params, &$attribs)
{
	if (!empty ($module->content)) : ?>
	<div class="module_top module_top<?php echo $params->get('moduleclass_sfx'); ?> module<?php echo $module->id ?>">
		<div class="inner">
			<?php if ($module->showtitle) : ?>
			<div class="heading">
				<?php echo $module->title; ?>
			</div>
			<?php endif; ?>
			<div class="content">
				<?php echo $module->content; ?>
			</div>
		</div>
	</div>
	<?php endif;
}
function modChrome_footer ($module, &$params, &$attribs)
{
	?>
	<div class="footer_module module<?php echo $module->id ; ?>">
		<?php if ( $module->showtitle ) { ?>
			<span class="heading"><?php echo $module->title ?></span>
		<?php } ?>
		<span class="content"><?php echo $module->content ?></span>
	</div>
	<?php 
}
function modChrome_content($module, &$params, &$attribs)
{
	if (!empty ($module->content)) : ?>
	<div class="mod_cont<?php echo $params->get('moduleclass_sfx'); ?> module<?php echo $module->id ?>">
			<?php if ($module->showtitle) : ?>
			<div class="heading">
				<h1><?php echo $module->title; ?></h1>
			</div>
			<?php endif; ?>
			<div class="content">
				<?php echo $module->content; ?>
			</div>
	</div>
	<?php endif;
}
function modChrome_bottom($module, &$params, &$attribs)
{
	if (!empty ($module->content)) : ?>
	<div class="module_bot module_bot<?php echo $params->get('moduleclass_sfx'); ?> module<?php echo $module->id ?>">
		<div class="inner">
			<?php if ($module->showtitle) : ?>
			<div class="heading">
				<div class="left">
					<div class="right">
						<div class="title">
							<?php echo $module->title; ?>
						</div>
					</div>
				</div>
			</div>
			<?php endif; ?>
			<div class="content">
				<?php echo $module->content; ?>
			</div>
		</div>
	</div>
	<?php endif;
}
?>
