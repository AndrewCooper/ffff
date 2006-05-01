<?php
session_start();
include('../functions.php');
if ($_GET['a']=='show') {
	include('head.html');
	hk_check_status();
	get_team_show();
	include('foot.html');
} elseif ($_GET['a']=='edit') {
	include('head.html');
	get_team_edit();
	include('foot.html');
} else {
	get_team_frameset();
}

function get_team_frameset() {
echo <<<CODE
<DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN"
		"http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
	<title>ffff2 administration</title>
</head>
<frameset rows="*,125">
	<frame name="team_show" id="team_show" src="teams.php?a=show" frameborder="1" marginheight="0" marginwidth="0" />
	<frame name="team_edit" id="team_edit" src="teams.php?a=edit" frameborder="1" marginheight="0" marginwidth="0" />
</frameset>
</html>
CODE;
}

function get_team_show() {
	$db=hk_db_connect();
	echo <<<CODE
	<div class="head5" style="text-align:right;"><a href="admin.php">[Administration]</a></div>
	<img src="../images/logo.gif" /><br /><br />
	<div style="font-size:smaller;"><a href="../toc.php">[Back to Table of Contents]</a></div><br />
	<table border='1' cellspacing='0' cellpadding='2' style="text-align:center;margin:auto;">
	<thead><tr><td></td><td>Name</td><td>Conference</td><td>Location</td><td>Rank</td><td>Record</td><td></td></tr></thead>
CODE;
	$res=$db->query("SELECT * FROM teams ORDER BY name");
	while ($row=$res->fetch_assoc()) {
		echo <<<CODE
		<tr><td><a name="{$row['id']}"><img src="../logos/{$row['image']}" style="width:50px;"></a></td>
		<td>{$row['name']}</td><td>{$row['conference']}</td><td>{$row['location']}</td>
		<td>{$row['rank']}</td><td>{$row['record']}</td><td><a href="teams.php?a=edit&id={$row['id']}" target="team_edit">Edit</a></td></tr>
CODE;
	}
	echo "</table>";
}
function get_team_edit() {
	$db=hk_db_connect();
	if ($_POST['submitter']=="Create") {
		$q="INSERT INTO teams (name,image,conference,location,rank,record) VALUES ('{$_POST['name']}','{$_POST['image']}','{$_POST['conference']}','{$_POST['location']}','{$_POST['rank']}','{$_POST['record']}')";
		$db->query($q);
		$_SESSION['error'].=$db->error;
		$_SESSION['message'].="Team created successfully. <br />".$q;
		echo "<script language=\"JavaScript\">\nparent.team_show.location.reload();\n</script>";
	} elseif ($_POST['submitter']=="Update") {
		$q="UPDATE teams SET name='{$_POST['name']}', image='{$_POST['image']}', conference='{$_POST['conference']}', location='{$_POST['location']}', rank='{$_POST['rank']}', record='{$_POST['record']}' WHERE id={$_POST['uid']}";
		$db->query($q);
		$_SESSION['error'].=$db->error;
		$_SESSION['message'].="Team updated successfully. <br />".$q;
		echo "<script language=\"JavaScript\">\nparent.team_show.location.reload();\n</script>";
	} elseif ($_POST['submitter']=="Delete") {
		$res=$db->query("SELECT name FROM teams WHERE id={$_POST['uid']}");
		$t=$res->fetch_assoc();
		echo <<<CODE
		<div class="head2" style="color:red;">WARNING</div>
		<div class="box1">You are about to delete all data for {$t['name']}. This action is NOT reversable.</div>
		<div class="head3">Confirm Deletion?<br /><form action="teams.php?a=edit" method="post" target="_self"><input type="hidden" name="uid" value="{$_POST['uid']}" /><input type="submit" name="submitter" value="Confirm" /></form></div><hr />
CODE;
	} elseif ($_POST['submitter']=="Confirm") {
		$q="DELETE FROM teams WHERE id={$_POST['uid']}";
		$db->query($q);
		$_SESSION['message']="Team Successfully Deleted.";
		unset($_GET['id']);
		echo "<script language=\"JavaScript\">\nparent.team_show.location.reload();\n</script>";
	}
	if (isset($_GET['id'])) {
		$res=$db->query("SELECT * FROM teams WHERE id={$_GET['id']}");
		$team=$res->fetch_assoc();
		$submit="<td></td><td colspan=\"3\"><input type=\"submit\" name=\"submitter\" value=\"Update\" /></td><td colspan=\"2\"><input type=\"submit\" name=\"submitter\" value=\"Delete\" /></td>";
		$head="Update Team";
		$getstr="&id=".$_GET['id'];
	} else {
		$head="Create Team";
		$submit="<td colspan=\"6\"><input type=\"submit\" name=\"submitter\" value=\"Create\" /></td>";
	}
	echo <<<CODE
	<div class="head3">$head</div>	
	<form action="teams.php?a=edit$getstr" method="post" target="_self">
	<input type="hidden" name="uid" value="{$_GET['id']}" />
	<table border="0" cellspacing="0" cellpadding="1" style="text-align:center;">
	<tr><td>Name</td><td>Image</td><td>Conference</td><td>Location</td><td>Rank</td><td>Record</td></tr>
	<tr><td style="width:20%;"><input type="text" name="name" maxlength="255" value="{$team['name']}" style="width:100%;" /></td>
	<td style="width:20%;"><input type="text" name="image" maxlength="255" value="{$team['image']}" style="width:100%;" /></td>
	<td style="width:20%;"><input type="text" name="conference" maxlength="255" value="{$team['conference']}" style="width:100%;" /></td>
	<td style="width:20%;"><input type="text" name="location" maxlength="255" value="{$team['location']}" style="width:100%;" /></td>
	<td style="width:7%;"><input type="text" name="rank" maxlength="3" value="{$team['rank']}" style="width:100%;" /></td>
	<td style="width:13%;"><input type="text" name="record" maxlength="10" value="{$team['record']}" style="width:100%;" /></td>
	<tr>$submit</tr>
	</table>
	</form>
CODE;
}
?>
