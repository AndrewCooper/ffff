<?php
session_start();
include('../functions.php');
if ($_GET['a']=='show') {
	include('head.html');
	get_game_show();
	include('foot.html');
} elseif ($_GET['a']=='edit') {
	include('head.html');
	get_game_edit();
	include('foot.html');
} else {
	get_game_frameset();
}

function get_game_frameset() {
echo <<<CODE
<DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN"
		"http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
	<title>ffff2 administration</title>
</head>
<frameset rows="*,200">
	<frame name="game_show" id="game_show" src="games.php?a=show" frameborder="1" marginheight="0" marginwidth="0" />
	<frame name="game_edit" id="game_edit" src="games.php?a=edit" frameborder="1" marginheight="0" marginwidth="0" />
</frameset>
</html>
CODE;
}

function get_game_show() {
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
	<div class="box2"><a href="games.php?a=edit&week={$_GET['week']}" target="game_edit">Create New Game</a></div><br />
	<div class="head2" style="text-align:left;"><form action="games.php" method="get" target="_self"><input type="hidden" name="a" value="show" />Games for $wsel<input type="submit" value="Change" /></form></div>
	<table border='0' cellspacing='0' cellpadding='2' style="text-align:center;margin:auto;">
	<thead><tr><td class="trbl">Date</td><td class="trbl">Time</td><td class="tbl">Away Team</td><td class="trb">Score</td><td class="tbl">Home Team</td><td class="trb">Score</td><td></td></tr></thead>
CODE;
	$res=$db->query("SELECT games.*, away.name AS aname, home.name AS hname FROM games LEFT JOIN teams AS away ON (away.id=games.away) LEFT JOIN teams AS home ON (home.id=games.home) WHERE week={$thisweek['id']} ORDER BY gametime");
	while ($row=$res->fetch_assoc()) {
		$date=date("D, M jS, Y",$row['gametime']);
		$time=date("g:ia",$row['gametime']);
		echo <<<CODE
		<tr><td class="trbl">$date</td><td class="trbl">$time</td><td class="tbl">{$row['aname']}</td><td class="trb">{$row['away_score']}</td>
		<td class="tbl">{$row['hname']}</td><td class="trb">{$row['home_score']}</td>
		<td><a href="games.php?a=edit&id={$row['id']}" target="game_edit">Edit</a></td></tr>
CODE;
	}
	echo "</table>";
}

function get_game_edit() {
	$db=hk_db_connect();
	if ($_POST['submitter']=="Create") {
		$datestr=$_POST['date'].' '.$_POST['time'];
		$tmstmp=strtotime($datestr);
		$date=date("m/d/Y",$tmstmp);
		$time=date("H:i",$tmstmp);
		$q="INSERT INTO games (week, gametime, away, away_score, home, home_score) VALUES ('{$_POST['week']}', $tmstmp, '{$_POST['away']}', '{$_POST['away_score']}', '{$_POST['home']}', '{$_POST['home_score']}')";
		$db->query($q);
		$_SESSION['message'].="Game creation status: <br />".$q;
		$_SESSION['error'].=$db->error;
		$_SESSION['message'].=$datestr.' -> '.$date.' '.$time;
		echo "<script language=\"JavaScript\">\nparent.game_show.location.href='games.php?a=show&week={$_POST['week']}';\n</script>";
	} elseif ($_POST['submitter']=="Update") {
		$datestr=$_POST['date'].' '.$_POST['time'];
		$tmstmp=strtotime($datestr);
		$date=date("m/d/Y",$tmstmp);
		$time=date("H:i",$tmstmp);
		$q="UPDATE games SET week='{$_POST['week']}', gametime=$tmstmp, away='{$_POST['away']}', away_score='{$_POST['away_score']}', home='{$_POST['home']}', home_score='{$_POST['home_score']}' WHERE id={$_POST['uid']}";
		$db->query($q);
		$_SESSION['message'].="Game update status: <br />".$q;
		$_SESSION['error'].=$db->error;
		echo "<script language=\"JavaScript\">\nparent.game_show.location.href='games.php?a=show&week={$_POST['week']}';\n</script>";
	} elseif ($_POST['submitter']=="Delete") {
		$res=$db->query("SELECT games.id,away.name AS aname,home.name AS hname FROM games LEFT JOIN teams AS away ON (away.id=games.away) LEFT JOIN teams AS home ON (home.id=games.home) WHERE games.id={$_POST['uid']}");
		echo $db->error;
		$t=$res->fetch_assoc();
		echo <<<CODE
		<div class="head2" style="color:red;">WARNING</div>
		<div class="box1">You are about to delete all data for {$t['aname']} vs. {$t['hname']}. This action is NOT reversable.</div>
		<div class="head3">Confirm Deletion?<br />
		<form action="games.php?a=edit" method="post" target="_self">
		<input type="hidden" name="uid" value="{$_POST['uid']}" />
		<input type="submit" name="submitter" value="Confirm" />
		</form>
		</div><hr />
CODE;
	} elseif ($_POST['submitter']=="Confirm") {
		$q="DELETE FROM games WHERE id={$_POST['uid']}";
		$db->query($q);
		$_SESSION['error'].=$db->error;
		$_SESSION['message']="Game Successfully Deleted.";
		unset($_GET['id']);
		echo "<script language=\"JavaScript\">\nparent.game_show.location.reload();\n</script>";
	}
	if (isset($_GET['id'])) {
		$res=$db->query("SELECT * FROM games WHERE id={$_GET['id']}");
		$game=$res->fetch_assoc();
		$submit="<td colspan=\"2\"><input type=\"submit\" name=\"submitter\" value=\"Update\" /></td><td colspan=\"2\"><input type=\"submit\" name=\"submitter\" value=\"Delete\" /></td>";
		$head="Update Game";
		$getstr="&id=".$_GET['id'];
		$wsel=get_week_combobox($game['week']);
		$asel=get_team_combobox("away",$game['away']);
		$hsel=get_team_combobox("home",$game['home']);
		$date=date("m/d/Y",$game['gametime']);
		$time=date("H:i",$game['gametime']);
	} else {
		$head="Create Game";
		if (isset($_GET['week'])) {
			$weekid=$_GET['week'];
		} else {
			$weekid=$_SESSION['thisweek']['nextweek'];
		}
		$submit="<td colspan=\"4\"><input type=\"submit\" name=\"submitter\" value=\"Create\" /></td>";
		$wsel=get_week_combobox($weekid);
		$asel=get_team_combobox("away");
		$hsel=get_team_combobox("home");
	}
	echo <<<CODE
	<div class="head3">$head</div>	
	<form action="games.php?a=edit$getstr" method="post" target="_self">
	<input type="hidden" name="uid" value="{$_GET['id']}" />
	<table border="0" cellspacing="0" cellpadding="1" style="text-align:center; width:100%;">
	<tr><td class="tbl">Week</td>
	<td class="tbl">Date <a href="http://www.gnu.org/software/tar/manual/html_chapter/tar_7.html" target="_new">help</a></td>
	<td class="trbl">Time <a href="http://www.gnu.org/software/tar/manual/html_chapter/tar_7.html" target="_new">help</a></td></tr>
	<tr>
	<td style="width:20%;">$wsel</td>
	<td style="width:40%;"><input type="text" name="date" maxlength="255" value="$date" style="width:100%;" /></td>
	<td style="width:40%;"><input type="text" name="time" maxlength="255" value="$time" style="width:100%;" /></td>
	</tr>
	</table><br />
	<table border="0" cellspacing="0" cellpadding="1" style="text-align:center;">
	<tr><td class="tbl">Away Team</td><td class="tb">Score</td>
	<td class="tbl">Home Team</td><td class="trb">Score</td><td></td></tr>
	<tr>
	<td style="width:40%;">$asel</td>
	<td style="width:10%;"><input type="text" name="away_score" maxlength="3" value="{$game['away_score']}" style="width:100%;" /></td>
	<td style="width:40%;">$hsel</td>
	<td style="width:10%;"><input type="text" name="home_score" maxlength="3" value="{$game['home_score']}" style="width:100%;" /></td>
	</tr>
	<tr>$submit</tr>
	</table>
	</form>
CODE;
}
?>
