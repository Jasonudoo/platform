<?php // no direct access
defined('_JEXEC') or die('Restricted access');
$col= 1 ;
?>


	<!-- Currency Selector Module -->
<?php echo $text_before ?>
<form action="<?php echo 'index.php?option=com_virtuemart&view=manufacturer' ?>" method="post">
	<font type="verdana" color="#000000"><b>Manufacturers: </b></font>
	<?php echo JHTML::_('select.genericlist', $manufacturers, 'virtuemart_manufacturer_id', 'class="inputbox" onchange="this.form.submit()"','virtuemart_manufacturer_id',  'mf_name' ) ; ?>

</form>