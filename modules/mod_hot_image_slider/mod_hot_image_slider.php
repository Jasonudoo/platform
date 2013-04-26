<?php
//no direct access
defined('_JEXEC') or die('Direct Access to this location is not allowed.');

// Path assignments
$mosConfig_absolute_path = JPATH_SITE;
$mosConfig_live_site = JURI :: base();
if(substr($mosConfig_live_site, -1)=="/") { $mosConfig_live_site = substr($mosConfig_live_site, 0, -1); }
 
// get parameters from the module's configuration
$enablejQuery = $params->get('enablejQuery','1');
$sliderWidth = $params->get('sliderWidth','300');
$borderWidth = $params->get('borderWidth','20');
$sliderWidth2 = $sliderWidth - $borderWidth;
$borderWidth2 = $borderWidth/2;
$sliderHeight = $params->get('sliderHeight','150');
$sliderHeight2 = $sliderHeight - $borderWidth;
$backgroundColor = $params->get('backgroundColor','000000');
$descColor = $params->get('descColor','000000');
$showButtons = $params->get('showButtons','1');
$showNames = $params->get('showNames','1');
$showDesc = $params->get('showDesc','1');
$showLink = $params->get('showLink','1');
$linkNewWindow = $params->get('linkNewWindow','0');
$buttonColor = $params->get('buttonColor','green');
$imageFolder = $params->get('imageFolder','images/stories');
$animation   = $params->get('animation','slide');
$fadeSpeed   = $params->get('fadeSpeed','600');

for ($loop = 1; $loop <= 9; $loop += 1) {
$imageArray[$loop] = $params->get('image'.$loop,'');
}

for ($loop = 1; $loop <= 9; $loop += 1) {
$imageTitleArray[$loop] = $params->get('image'.$loop.'title','');
}

for ($loop = 1; $loop <= 9; $loop += 1) {
$imageDescArray[$loop] = $params->get('image'.$loop.'desc','');
}

for ($loop = 1; $loop <= 9; $loop += 1) {
$imageLinkArray[$loop] = $params->get('image'.$loop.'link','');
}

require(JModuleHelper::getLayoutPath('mod_hot_image_slider'));
