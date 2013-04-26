<?php
/**
* @Copyright Copyright (C) 2009 - 2010 ... Oh-Taek Im
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/
defined('_JEXEC') or die ('Restricted access.');

require_once(dirname(__FILE__).DS.'helper.php');

if (!defined('PIG2JS')) {
   
   
    
    

    define('PIG2JS',1);
}

$vmscroller = new modVMScrollerHelper($params);
$vmscroller->render();

?>

