<?php
session_start();
include('../functions.php');
if ($_GET['a']=='show') {
	include('head.html');
	hk_check_status();
	get_bowl_show();
	include('foot.html');
} elseif ($_GET['a']=='edit') {
	include('head.html');
	get_bowl_edit();
	include('foot.html');
} else {
	get_bowl_frameset();
}

function get_bowl_frameset() {
echo <<<CODE
<DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN"
		"http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
	<title>ffff2 administration</title>
</head>
<frameset rows="*,175">
	<frame name="bowl_show" id="bowl_show" src="bowls.php?a=show" frameborder="1" marginheight="0" marginwidth="0" />
	<frame name="bowl_edit" id="bowl_edit" src="bowls.php?a=edit" frameborder="1" marginheight="0" marginwidth="0" />
</frameset>
</html>
CODE;
}

function get_bowl_show() {
	$db=hk_db_connect();
	echo <<<CODE
	<div class="head5" style="text-align:right;"><a href="admin.php">[Administration]</a></div>
	<img src="../images/logo.gif" /><br /><br />
	<div style="font-size:smaller;"><a href="../toc.php">[Back to Table of Contents]</a></div><br />
	<div class="box2"><a href="bowls.php?a=edit&week={$_GET['week']}" target="bowl_edit">Create New Bowl</a></div><br />
	<div class="head2" style="text-align:left;">Bowl Games</div>
	<table border='1' cellspacing='0' cellpadding='2' style="text-align:center;margin:auto;">
	<tr><td>Name</td><td>Description</td><td>Location</td><td>Game</td><td>Multiplier</td><td></td></tr>
CODE;
	$res=$db->query("SELECT bowls.*, away.name AS aname, home.name AS hname FROM bowls LEFT JOIN games ON (games.id=bowls.game) LEFT JOIN teams AS away ON (away.id=games.away) LEFT JOIN teams AS home ON (home.id=games.home) ORDER BY games.gametime");
	echo $db->error;
	while ($row=$res->fetch_assoc()) {
		echo <<<CODE
		<tr>
		<td>{$row['name']}</td>
		<td>{$row['description']}</td>
		<td>{$row['location']}</td>
		<td>{$row['aname']} @ {$row['hname']}</td>
		<td>{$row['multiplier']}</td>
		<td><a href="bowls.php?a=edit&id={$row['id']}" target="bowl_edit">Edit</a></td></tr>
CODE;
	}
	echo "</table>";
}

function get_bowl_edit() {
	$db=hk_db_connect();
	if ($_POST['submitter']=="Create") {
		$q="INSERT INTO bowls (name,description,location,game,multiplier) VALUES ('{$_POST['name']}', '{$_POST['description']}', '{$_POST['location']}', '{$_POST['game']}', '{$_POST['multiplier']}')";
		$db->query($q);
		$_SESSION['message'].="Bowl creation status: <br />".$q;
		$_SESSION['error'].=$db->error;
		echo "<script language=\"JavaScript\">\nparent.bowl_show.location.reload();\n</script>";
	} elseif ($_POST['submitter']=="Update") {
		$q="UPDATE bowls SET name='{$_POST['name']}', description='{$_POST['description']}', location='{$_POST['location']}', game='{$_POST['game']}', multiplier='{$_POST['multiplier']}' WHERE id={$_POST['uid']}";
		$db->query($q);
		$_SESSION['message'].="Bowl update status: <br />".$q;
		$_SESSION['error'].=$db->error;
		echo "<script language=\"JavaScript\">\nparent.bowl_show.location.reload();\n</script>";
	} elseif ($_POST['submitter']=="Delete") {
		$res=$db->query("SELECT name FROM bowls WHERE bowls.id={$_POST['uid']}");
		echo $db->error;
		$t=$res->fetch_assoc();
		echo <<<CODE
		<div class="head2" style="color:red;">WARNING</div>
		<div class="box1">You are about to delete all data for the {$t['name']}. This action is NOT reversable.</div>
		<div class="head3">Confirm Deletion?<br />
		<form action="bowls.php?a=edit" method="post" target="_self">
		<input type="hidden" name="uid" value="{$_POST['uid']}" />
		<input type="submit" name="submitter" value="Confirm" />
		</form>
		</div><hr />
CODE;
	} elseif ($_POST['submitter']=="Confirm") {
		$q="DELETE FROM bowls WHERE id={$_POST['uid']}";
		$db->query($q);
		$_SESSION['message']="Bowl Successfully Deleted.";
		unset($_GET['id']);
		echo "<script language=\"JavaScript\">\nparent.bowl_show.location.reload();\n</script>";
	}
	if (isset($_GET['id'])) {
		$res=$db->query("SELECT * FROM bowls WHERE id={$_GET['id']}");
		$bowl=$res->fetch_assoc();
		$submit="<td style=\"width:50%;\"><input type=\"submit\" name=\"submitter\" value=\"Update\" /></td><td style=\"width:50%;\"><input type=\"submit\" name=\"submitter\" value=\"Delete\" /></td>";
		$head="Update bowl";
		$getstr="&id=".$_GET['id'];
		$gsel=get_game_combobox($bowl['game']);
	} else {
		$head="Create bowl";
		$submit="<td colspan=\"2\"><input type=\"submit\" name=\"submitter\" value=\"Create\" /></td>";
		$gsel=get_game_combobox();
	}
	echo <<<CODE
	<div class="head3">$head</div>	
	<form action="bowls.php?a=edit$getstr" method="post" target="_self">
	<input type="hidden" name="uid" value="{$_GET['id']}" />
	<table border='0' cellspacing='0' cellpadding='2' style="text-align:center;width:100%;">
	<tr>
	<td style="width:30%;">Name</td>
	<td style="width:30%;">Description</td>
	<td style="width:30%;">Location</td>
	<td style="width:10%;">Multiplier</td>
	</tr>
	<tr>
	<td><input name="name" type="text" value="{$bowl['name']}" style="width:100%;" /></td>
	<td><input name="description" type="text" value="{$bowl['description']}" style="width:100%;" /></td>
	<td><input name="location" type="text" value="{$bowl['location']}" style="width:100%;" /></td>
	<td><input name="multiplier" type="text" value="{$bowl['multiplier']}" style="width:100%;" /></td>
	</table><hr />
	<table border='0' cellspacing='0' cellpadding='2' style="text-align:center;width:100%;">
	<tr><td colspan="2">Game: $gsel</td></tr>
	<tr>$submit</tr>
	</table>
	</form>
CODE;
}
?>
