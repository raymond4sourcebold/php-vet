<?php
/**
 * Error template file.
 *
 * LICENSE: All rights reserved.
 *
 * @copyright  Copyright (c) 2008, ContactMaster, Inc.
 * @license    Proprietary Software
 * @version    2.0.1
 * @link       http://dev.contactmaster.biz
 * @since      File available since Release 2.0.1
 * @package    html_template
 */
?>

<!--
	Error page
-->
<div class="r_container">
<h1>dev.contactmaster.biz</h1>
<div class="r_error">
<code style="font-size: smaller;">
	<div>Error:
		
<?php 
if (isset($error)) {
	if (is_array($error)) {
		foreach ($error as $errLine) {
			echo "
		<div class=\"r_errline\">$errLine</div>";
		}
	} else {
		echo "
		<div class=\"r_errline\">$error</div>";
	}
} else {
	echo "
		<div class=\"r_errline\">No error to display.</div>";
}
?>
	</div>
</code>
</div>
<div style="font-size: smaller; font-style:italic;">raymond@philippinedev.com</div>
</div>
<div style="height: 5px; background-color: #BD9400;">&nbsp;</div>