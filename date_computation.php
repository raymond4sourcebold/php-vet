<?php
 
if ($_POST) {

$seconds_one_month	= 2629743.83;
$seconds_one_year	= 31556926;
$seconds_one_day	= 86400;

if ($_POST['frmSecondDate']) {
	$minuenDate	= strtotime($_POST['frmSecondDate']);
} else {
	$minuenDate	= time();
}

	$nSeconds	= $minuenDate - strtotime($_POST['frmPastDate']);
	$nDayOnly	= $nSeconds / $seconds_one_day;
	
	$nYear		= $nSeconds / $seconds_one_year;
	
	$nSeconds	= $nSeconds - ($seconds_one_year * floor($nYear));
	
	$nMonth		= $nSeconds / $seconds_one_month;
	
	$nSeconds	= $nSeconds - ($seconds_one_month * floor($nMonth));

	$nDay		= $nSeconds / $seconds_one_day;

	echo '<p>' . date('Y-m-d h:i:s') . '</p>';
	echo '<p>' . $_POST['frmPastDate'] . '</p>';
	echo "<p>Sec: {$nSeconds}<br />Year: <b>" . floor($nYear) . "</b>, Month: <b>" . floor($nMonth) . "</b>, Day: <b>{$nDay}</b></p>";
	echo "<p>Day only: " . $nDayOnly . "</p>";
}

?>
<html>
<head></head>
<body>
<p>Enter a date in the past:</p>
<form method='post'>
<p>YYYY-MM-DD [hh:mm:ss] <input type='text' id='frmPastDate' name='frmPastDate' /></p>
<p>YYYY-MM-DD [hh:mm:ss] <input type='text' id='frmSecondDate' name='frmSecondDate' /></p>
<input type='submit' value='compute' />
</form>
<script type='text/javascript'>
	document.getElementById('frmPastDate').focus();
</script>
</body>
</html>
