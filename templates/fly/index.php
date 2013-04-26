<?php
defined('_JEXEC') or die;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->language; ?>" lang="<?php echo $this->language; ?>" >
<head>
<!--
author: viva http://websitetemplates.bz
-->
<jdoc:include type="head" />
<link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/system/css/general.css" type="text/css" />
<link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/system/css/system.css" type="text/css" />
<link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/fly/css/template.css" type="text/css" />
<script language="javascript" type="text/javascript" src="<?php echo $this->baseurl ?>/templates/fly/js/ie.js"></script>
<!--[if lt IE 7]>
<!--[if IE 6]>
<![if gte IE 5.5]>
<link href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/css/iefix.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="<?php echo $this->baseurl ?>/templates/fly/fixpng.js"></script>
<![endif]>
<![endif]>
<![endif]-->
</head>
<body id="body">
	<div class="clear"></div>
	<div id="topmenu">
		<div class="outer">
			<div id="topmenu_pos">
				<jdoc:include type="modules" name="topmenu" />
			</div>
			<script type="text/javascript">
				var z_menu = document . getElementById ( 'topmenu' ) ;
				z_menu = z_menu . getElementsByTagName ( 'a' ) ;
				z_menu [ 0 ] . className = 'first_item' ;
			</script>
			<div class="clear"></div>
		</div>
	</div>
	<div class="toprow">
		<div class="outer">
			<jdoc:include type="modules" name="header" />
			<div class="clear"></div>
		</div>
	</div>
	<div id="header">
		<div class="outer">
			<div>
				<jdoc:include type="modules" name="header1" />
			</div>
			<div>
				<jdoc:include type="modules" name="header2" />
			</div>
			<div>
				<jdoc:include type="modules" name="header3" />
			</div>
			<div class="clear"></div>
		</div>
	</div>
	<!--------------------- Content ------------------------------------->
	<div class="outer">
		<div id="content">
				<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0"  id="center">
					<tr>
						<td valign="top">
							<div id="leftcolumn">
								<jdoc:include type="modules" name="search"/>
								<div class="mod_bor">
									<jdoc:include type="modules" name="left" style="side"/>
									<jdoc:include type="modules" name="user1" style="side"/>
								</div>
								<jdoc:include type="modules" name="banner1" />
							</div>
						</td>
						<td id="centercolumn"> 
								<div>
									<div>
										<jdoc:include type="modules" name="top" style="side" />
									</div>
									<div>
										<jdoc:include type="modules" name="content" style="content" />
									</div>
									<?php if ($this->getBuffer('message')) : ?>
										<jdoc:include type="message" />
									<?php endif; ?>
									<jdoc:include type="component" />
								</div>
						</td>
						<td valign="top" >
							<div id="rightcolumn">
								<div class="mod_bor">
									<jdoc:include type="modules" name="right" style="side"/>
									<jdoc:include type="modules" name="user2" style="side"/>
									<jdoc:include type="modules" name="user3" style="side"/>
								</div>
								<jdoc:include type="modules" name="banner" />
								<jdoc:include type="modules" name="banner2" />
							</div>
						</td>
					</tr>
				</table> 
			</div>
		<div class="clear"></div>
	</div>
<!------------------------------------- footer ------------------------------------->
	<div id="footer" >
		<div class="outer">
			<div class="inner" >
				<div id="footer1">
					<jdoc:include type="modules" name="footer1"/>
				</div>
				<div id="footer2">
					<div id="footer2_t">
						<div style="float:right;color:white;">&copy; All rights reserved. <?php echo  Date( 'Y' ) ;?>.</div>
						<div class="clear"></div>
						<div style="text-align:right;color:white;" id="power">designed by <a href="http://www.websitetemplates.bz/" title="Website Templates">Website Templates.bz</a> - professional <a href="http://www.websitetemplates.bz/virtuemart-templates.html" title="Virtuemart Template">Virtuemart Templates</a>
						</div>
					</div>
					<div id="footer2_b" class="sep">
						<jdoc:include type="modules" name="footer2"/>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="clear"></div>
	<jdoc:include type="modules" name="debug" />
</body>
</html>
