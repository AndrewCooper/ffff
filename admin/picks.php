<?php
session_start();
include('../functions.php');
if ($_GET['a']=='show') {
	include('head.html');
	get_picks_show();
	include('foot.html');
} elseif ($_GET['a']=='edit') {
	include('head.html');
	get_picks_edit();
	include('foot.html');
} else {
	get_picks_frameset();
}

function get_picks_frameset() {
echo <<<CODE
<DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN"
		"http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
	<title>ffff2 administration</title>
</head>
<frameset rows="*,120">
	<frame name="picks_show" id="picks_show" src="picks.php?a=show" frameborder="1" marginheight="0" marginwidth="0" />
	<frame name="picks_edit" id="picks_edit" src="picks.php?a=edit" frameborder="1" marginheight="0" marginwidth="0" />
</frameset>
</html>
CODE;
}
function get_picks_show() {
	if (!isset($_GET['week']) ) {
		if ($_SESSION['thisweek']['id']==NULL) {
			$thisweek=hk_get_week(1);
		} else {
			$thisweek=$_SESSION['thisweek'];
		}
	} else {
		$thisweek=hk_get_week($_GET['week']);
	}
	$db=hk_db_connect();
	$wsel=get_week_combobox($thisweek['id']);
	echo <<<CODE
	<div class="head5" style="text-align:right;"><a href="admin.php">[Administration]</a></div>
	<img src="../images/logo.gif" /><br /><br />
CODE;
	hk_check_status();
	echo <<<CODE
	<div style="font-size:smaller;"><a href="../toc.php">[Back to Table of Contents]</a></div><br />
	<div class="head2" style="text-align:left;">
	<form action="picks.php" method="get" target="_self">
	<input type="hidden" name="a" value="show" />Picks for $wsel<input type="submit" value="Change" /></form></div>
	<table border='0' cellspacing='0' cellpadding='2' style="text-align:center;margin:auto;">
	<thead><tr><td class="trbl">User</td>
CODE;
	$gres=$db->query("SELECT games.id, away.name AS aname, home.name AS hname FROM games LEFT JOIN teams AS away ON (away.id=games.away) LEFT JOIN teams AS home ON (home.id=games.home) WHERE week={$thisweek['id']} ORDER BY gametime");
	while ($grow=$gres->fetch_assoc()) {
		echo <<<CODE
		<td class="tbl">{$grow['aname']}</td>
		<td class="tb">@</td>
		<td class="trb">{$grow['hname']}</td>
CODE;
	}
	echo "<td></td><tr></thead>\n";
	$ures=$db->query("SELECT id,CONCAT(firstname,' ',lastname) AS fullname FROM users WHERE id!=-1 ORDER BY lastname,firstname");
	while ($urow=$ures->fetch_assoc()) {
		echo <<<CODE
		<tr><td class="trbl">{$urow['fullname']}</td>
CODE;
		$pres=$db->query("SELECT games.id,picks.* FROM games LEFT JOIN picks ON (picks.game=games.id AND picks.user={$urow['id']}) WHERE games.week={$thisweek['id']} ORDER BY gametime");
		while ($prow=$pres->fetch_assoc()) {
			echo <<<CODE
			<td class="tbl">{$prow['away']}</td>
			<td class="tb">@</td>
			<td class="trb">{$prow['home']}</td>
CODE;
		}
		echo <<<CODE
		<td><a href="picks.php?a=edit&uid={$urow['id']}&week=${thisweek['id']}" target="picks_edit">Edit</a></td></tr>\n
CODE;
	}
	echo "</table>";
}

function get_picks_edit() {
$db=hk_db_connect();
if ($_POST['submitter']=="Update") {
	$away=$_POST['away'];
	$home=$_POST['home'];
	foreach($away as $pid => $val) {
		$q="UPDATE picks SET home={$home[$pid]}, away={$away[$pid]} WHERE id=$pid";
		$db->query($q);
		$_SESSION['message'].=$q."<br />\n";
		$_SESSION['error'].=$db->error;
		
	}
	$_SESSION['message'].="Picks updated successfully. <br />";
	echo "<script language=\"JavaScript\">\nparent.picks_show.location.reload();\n</script>";
}
if (isset($_GET['week']) && isset($_GET['uid'])) {
	$uid=$_GET['uid'];
	$week=$_GET['week'];
	$getstr="&week=$week&uid=$uid";
	echo <<<CODE
	<div class="head3">$head</div>	
	<form action="picks.php?a=edit$getstr" method="post" target="_self">
	<input type="hidden" name="uid" value="{$_GET['uid']}" />
	<table border="0" cellspacing="0" cellpadding="1" style="text-align:center; width:100%;">
	<thead><tr>
CODE;
	$gres=$db->query("SELECT games.id, away.name AS aname, home.name AS hname FROM games LEFT JOIN teams AS away ON (away.id=games.away) LEFT JOIN teams AS home ON (home.id=games.home) WHERE week=$week ORDER BY gametime");
	$width=100/($gres->num_rows*2);
	while ($grow=$gres->fetch_assoc()) {
		echo <<<CODE
		<td class="tbl" style="width:$width;">{$grow['aname']}</td>
		<td class="tb">@</td>
		<td class="trb" style="width:$width;">{$grow['hname']}</td>
CODE;
	}
	echo "<tr></thead>\n";
	$pres=$db->query("SELECT games.id AS gid,picks.* FROM games LEFT JOIN picks ON (picks.game=games.id AND picks.user=$uid) WHERE games.week=$week ORDER BY gametime");
	while ($prow=$pres->fetch_assoc()) {
		echo <<<CODE
		<td class="tbl"><input type="text" maxlength="3" name="away[{$prow['id']}]" value="{$prow['away']}" style="width:100%;" /></td>
		<td class="tb">@</td>
		<td class="trb"><input type="text" maxlength="3" name="home[{$prow['id']}]" value="{$prow['home']}" style="width:100%;" /></td>
CODE;
	}
	echo <<<CODE
	</table>
	<input type="submit" name="submitter" value="Update" style="text-align:center;" />
	</form>
CODE;
}
}
?>
