<?php defined('_JEXEC') or die('Restricted access'); // no direct access

if ($showButtons==0) { $showButtonsDisplay = 'display:none;'; }else{ $showButtonsDisplay = ''; }
if ($buttonColor=="white") { $buttonColorValue = "666"; }else{ $buttonColorValue = "fff"; }

// get the document object
$doc =& JFactory::getDocument();

// add your stylesheet
$doc->addStyleSheet( 'modules/mod_hot_image_slider/tmpl/style.css' );

// style declaration
$doc->addStyleDeclaration( '

div.hotwrap {
width:'.$sliderWidth.'px;
margin:0 auto;
text-align:left;
}

div#top {
float:left;
clear:both;
width:'.$sliderWidth.'px;
height:52px;
margin:22px 0 0;
}

div#header_hotslider div.hotwrap {
height:'.$sliderHeight.'px;
background:#'.$backgroundColor.';
}

div#header_hotslider div#slide-holder {
width:'.$sliderWidth.'px;
height:'.$sliderHeight.'px;
position:absolute;
}

div#header_hotslider div#slide-holder div#slide-runner {
top:'.$borderWidth2.'px;
left:'.$borderWidth2.'px;
width:'.$sliderWidth2.'px;
height:'.$sliderHeight2.'px;
overflow:hidden;
position:absolute;
}

div#header_hotslider div#slide-holder div#slide-controls {
left:0;
top:10px;
width:'.$sliderWidth2.'px;
height:46px;
display:none;
position:absolute;
background:url(modules/mod_hot_image_slider/images/slide-bg.png) 0 0;
}

div#header_hotslider div#slide-holder div#slide-controls div#slide-nav {
float:right;
'.$showButtonsDisplay.'
}

p.textdesc {
float:left;
color:#fff;
display:inline;
font-size:10px;
line-height:16px;
margin:15px 0 0 20px;
text-transform:uppercase;
overflow:hidden;
color:#'.$descColor.';
}

div#header_hotslider div#slide-holder div#slide-controls div#slide-nav a {
background-image:url(modules/mod_hot_image_slider/images/slide-nav-'.$buttonColor.'.png);
color:#'.$buttonColorValue.';
top:11px;
position:relative;
}

' );

?>

<script type="text/javascript">var _siteRoot='index.php',_root='index.php';</script>
<?php if ($enablejQuery!=0) { ?>
<script type="text/javascript" src="<?php echo $mosConfig_live_site; ?>/modules/mod_hot_image_slider/js/jquery.min.js"></script>
<?php } ?>
<script type="text/javascript" src="<?php echo $mosConfig_live_site; ?>/modules/mod_hot_image_slider/js/scripts.js"></script>
<!--[if IE 6]>
<script type="text/javascript" src="<?php echo $mosConfig_live_site; ?>/modules/mod_hot_image_slider/js/pngfix.js"></script>
<script>
  POS_BrowserPNG.fix('div#header_hotslider div#slide-holder div#slide-controls p#slide-nav a,div#header_hotslider div#slide-holder div#slide-controls');
</script>
<![endif]-->

<div id="header_hotslider"><div class="hotwrap">
<div id="slide-holder">
<div id="slide-runner">
<?php for ($imageCounter = 1; $imageCounter <= 9; $imageCounter += 1) { ?>
	<?php if ($imageArray[$imageCounter]) { ?>
        <?php if (($showLink!=0)and($imageLinkArray[$imageCounter]!="")) { ?><a href="<?php echo $imageLinkArray[$imageCounter]; ?>"<?php if($linkNewWindow) { ?> target="_blank"<?php } ?>><?php } ?>
            <img id="slide-img-<?php echo $imageCounter; ?>" src="<?php echo $mosConfig_live_site; ?>/<?php echo $imageFolder ?>/<?php echo $imageArray[$imageCounter]; ?>" class="slide" alt="" />
        <?php if (($showLink!=0)and($imageLinkArray[$imageCounter]!="")) { ?></a><?php } ?>
    <?php } ?>
<?php } ?>
<div id="slide-controls">
     <div id="slide-nav"></div>
     <?php if ($showNames!=0) { ?><p id="slide-client" class="text"><span></span></p><?php } ?>
     <?php if ($showDesc!=0) { ?>
     <div style="clear:both"></div>
     <p id="slide-desc" class="textdesc"></p>
	 <?php } ?>
</div>
</div>
   </div>
  
   <script type="text/javascript">
    if(!window.slider) var slider={};slider.anim='<?php echo $animation; ?>';slider.fade_speed=<?php echo $fadeSpeed; ?>;slider.data=[
	<?php $imageSRC=""; ?>
	<?php for ($titleCounter = 1; $titleCounter <= 9; $titleCounter += 1) { ?>
		<?php if ($imageArray[$titleCounter]) { ?>
		<?php $imageSRC .= '{"id":"slide-img-'.$titleCounter.'","client":"'.$imageTitleArray[$titleCounter].'","desc":"'.$imageDescArray[$titleCounter].'"},'; ?>
		<?php } ?>
	<?php } ?>
	<?php $imageREDUCED = substr($imageSRC, 0, -1); echo $imageREDUCED; ?>
	];
   </script>
  </div></div>