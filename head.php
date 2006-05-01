<?php
	session_start();
	$db_link = mysql_connect("192.168.0.101","hkc","hkc");
	//$db_link = mysql_connect("10.249.0.223","hkc","hkc");
	mysql_select_db("ffff");
	include("functions.php");
	if ($_GET['action'] == "logout") {
		log_out();
	}
	log_in();
	if (!isset($_SESSION['login_id']) && $_SERVER['PHP_SELF'] != "/ffff/index.php") {
		header("Location: http://".$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF'])."/index.php");
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
	<title>hkc's fall football fanatic frenzy</title>
	<meta name="generator" content="BBEdit 7.0.3" />
	<link rel="Stylesheet" href="style.css" type="text/css" />
	<script src="functions.js">
	</script>
</head>
<body>
<table class="layouttable" border="0" cellspacing="0" cellpadding="0" frame="void" align="center" >
	<tr>
		<td class="tlc"><div class="spacer"></div></td>
		<td class="tb"><div class="spacer"></div></td>
		<td class="trc"><div class="spacer"></div></td>
	</tr>
	<tr>
		<td class="lb"><div class="spacer"></div></td>
		<td class="mainContent" align="center" valign="top">
<?php
	if (isset($_SESSION['login_id']) && !isset($_GET['nologout'])) {
	echo <<<CODE
		<div style="text-align:left; vertical-align:top;">
		<a name="top">Logged in as:</a><br />
		&nbsp;&nbsp;&nbsp;&nbsp;{$_SESSION['full_name']}
		&nbsp;[<a href="toc.php?action=logout">Log Out</a>]</div>
CODE;
	}
?>
<!-- Begin Main Content -->
