<?php
/**
 * Login template file.
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
	Login page
-->
<div id="logo"></div>
<div id="blocconnexion"></div>
<div id="blochaut"></div>
<div id="bloccentre">
<?php
if (isset($error)) {
	echo '<div id="erreurauth">'.$error.'</div>';
}
?>
	<div id="boiteconnexion">
		<form method="post" action="/login" onsubmit="return loginValidate();">
			<label for='login'><?php echo lang_user; ?></label><input class='txtIn' id="login" name="login" type="text" /><br />
			<label for='password'><?php echo lang_password; ?></label><input class='txtIn' id="password" name="password" type="password" /><br />
			<input style='margin-top: 5px;' type="submit" value="<?php echo lang_connect; ?>" />
		</form>
	</div>
	<div style='clear: both;'></div>
	<noscript>JavaScript must be activated<br />in your browser options</noscript>
</div>
<div id="blocbas"></div>