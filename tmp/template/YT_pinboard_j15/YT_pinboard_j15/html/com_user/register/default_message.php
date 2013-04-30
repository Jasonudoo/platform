<?php
/**
* @package   yoo_pinboard
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   YOOtheme Proprietary Use License (http://www.yootheme.com/license)
*/

// no direct access
defined('_JEXEC') or die('Restricted access');
?>

<h2>
	<?php echo $this->escape($this->message->title); ?>
</h2>

<p>
	<?php echo $this->escape($this->message->text); ?>
</p>
