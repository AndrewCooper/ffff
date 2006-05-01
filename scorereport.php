<?php
session_start();
include('functions.php');
if(!isset($_SESSION['ffff_user_data'])) {
	hk_redir_rel('index.php');
} else {
	include('head.html');
	echo <<<CODE
	<script language="JavaScript">
	function chweek() {
		var weeknum;
		weeknum = document.forms["selweek"].week.options[document.forms["selweek"].week.selectedIndex].value
		chloc("#week"+weeknum);
	}
	</script>
	<div class="head5" style="text-align:right;"><a href="admin.php">[Administration]</a></div>
	<img src="images/logo.gif" /><br />
	<div style="font-size:smaller;"><a href="toc.php">[Back to Table of Contents]</a></div><br />
CODE;
	hk_check_status();
	include('scores.php');
	//include('admin/calc_scores.php');
	include('foot.html');
}
?>
