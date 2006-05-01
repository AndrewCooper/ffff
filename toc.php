<?php
session_start();
include('functions.php');
if(!isset($_SESSION['ffff_user_data'])) {
	hk_redir_rel('index.php');
} else {
	include('head.html');
	$ud = $_SESSION['ffff_user_data'];
	$thisweek = hk_get_week();
	$_SESSION['thisweek'] = $thisweek;
	echo <<<CODE
	<div class="head5" style="text-align:right;"><a href="admin.php">[Administration]</a></div>
	<img src="images/logo.gif" /><br /><br />
CODE;
	hk_check_status();
	echo <<<CODE
	<div class="head1"><div class="box1">{$thisweek['name']}</div></div><br />
	<div class="head2">Welcome back, {$ud['full_name']}!<br />
	<span style="font-size:x-small; font-weight:normal;">If this is not you, please <a href="login.php?a=lo">logout</a>.</span></div><br />
CODE;

	get_user_status();
	get_options();
	get_standings();
	include('foot.html');
}

function get_user_status() {
	global $thisweek;
	global $ud;
	$rankbox = "";
	$statusbox = "";
	$db = hk_db_connect();
	//get user data from database
	$res = $db->query("SELECT users.*,scores.* FROM users LEFT JOIN scores ON (scores.user=users.id) WHERE users.id={$ud['id']} AND users.id!=-1");
	$user = $res->fetch_assoc();
	$res->close();
	//find ties
	$res = $db->query("SELECT firstname,lastname,id FROM users WHERE id IN (SELECT user FROM scores WHERE score={$user['score']}) AND users.id!=-1 AND users.id!={$ud['id']}");
	$ud['ties'] = array();
	$ud['ties']['num'] = $res->num_rows;
	for($i=0; $i < $ud['ties']['num']; ++$i) {
		$row = $res->fetch_assoc();
		if ($row['id'] != $user['id']) {
			$ud['ties'][$i] = $row['firstname']." ".$row['lastname'];
		}
	}
	$res->close();
	//find rank
	$res = $db->query("SELECT score FROM scores WHERE user!=-1 ORDER BY score DESC");
	$row = $res->fetch_assoc();
	$ud['place'] = 1;
	while($row['score'] > $user['score']) {
		$row = $res->fetch_assoc();
		$ud['place']+=1;
	}
	$res->close();
	//output ties/rank and score
	$ud['pl_suf'] = hk_num_suffix($ud['place']);
	if ($ud['ties']['num'] > 0) {
		$rankbox .= "You are tied with {$ud['ties']['num']} players for {$ud['place']}{$ud['pl_suf']} place with a score of {$user['score']}.<br />Those players are: ";
		for($i=0; $i <= $ud['ties']['num']-1; ++$i) {
			if ($ud['ties']['num']==1) {
				$rankbox .= $ud['ties'][$i];
			} elseif($i+1 != $ud['ties']['num']) {
				$rankbox .= $ud['ties'][$i].", ";
			} else {
				$rankbox .= " and ".$ud['ties'][$i];
			}
		}
	} else {
		$rankbox .= "You are alone in {$ud['place']}{$ud['pl_suf']} place with a score of {$user['score']}.<br />";
	}
	
	//determine if picks for this week are made
	if ($thisweek['id'] != NULL) {
		$q="SELECT games.*, picks.id AS pid, away.name AS aname, home.name AS hname FROM games LEFT JOIN picks ON (picks.game=games.id AND picks.user={$_SESSION['ffff_user_data']['id']}) LEFT JOIN teams AS away ON (away.id=games.away) LEFT JOIN teams AS home ON (home.id=games.home) WHERE games.week={$thisweek['id']} ORDER BY games.gametime";
		$res = $db->query($q);
		$earlytime=9999999999999999;
		$games=$res->num_rows;
		$pids=0;
		if ($games > 0) {
			while (($row=$res->fetch_assoc())!=NULL) {
				if ($row['pid'] == NULL) {
					$pids++;
					if ($row['gametime'] < $earlytime) {
						$earlytime = $row['gametime'];
						$gamename=$row['aname']." vs ".$row['hname'];
						$timename=date("g:ia",$earlytime)." on ".date("D, M jS",$earlytime);
					}
				}
			}
			if ($pids != 0) {
				$statusbox .= "You have $pids:$games picks to make for {$thisweek['name']}.<br /><br />";
				$statusbox .= "The soonest game is:<br /> $gamename<br /> at $timename.<br /><br />";
				$statusbox .= "<a href=\"picks.php?week={$thisweek['id']}\">Make picks now</a><hr />";
			} else {
				$statusbox .= "You have already made your picks for {$thisweek['name']}.<br /><a href=\"picks.php?week={$thisweek['id']}\">Review picks</a><hr />";
			}
		} else {
			$statusbox .= "There are no games in {$thisweek['name']}<br />";
		}
		$res->close();
	} else {
		$statusbox .= "There are no games in {$thisweek['name']}";
	}

	//determine if picks for next week are made
	if ($thisweek['next'] != NULL) {
		$q="SELECT games.*, picks.id AS pid, away.name AS aname, home.name AS hname FROM games LEFT JOIN picks ON (picks.game=games.id AND picks.user={$_SESSION['ffff_user_data']['id']}) LEFT JOIN teams AS away ON (away.id=games.away) LEFT JOIN teams AS home ON (home.id=games.home) WHERE games.week={$thisweek['next']} ORDER BY games.gametime";
		$res = $db->query($q);
		$earlytime=9999999999999999;
		$games=$res->num_rows;
		$pids=0;
		if ($games > 0) {
			while (($row=$res->fetch_assoc())!=NULL) {
				if ($row['pid'] == NULL) {
					$pids++;
					if ($row['gametime'] < $earlytime) {
						$earlytime = $row['gametime'];
						$gamename=$row['aname']." vs ".$row['hname'];
						$timename=date("g:ia",$earlytime)." on ".date("D, M jS",$earlytime);
					}
				}
			}
			if ($pids != 0) {
				$statusbox .= "You have $pids:$games picks to make for {$thisweek['nextname']}.<br /><br />";
				$statusbox .= "The soonest game is:<br /> $gamename<br /> at $timename.<br /><br />";
				$statusbox .= "<a href=\"picks.php?week={$thisweek['next']}\">Make picks now</a>";
			} else {
				$statusbox .= "You have already made your picks for {$thisweek['nextname']}.<br /><a href=\"picks.php?week={$thisweek['next']}\">Review picks</a>";
			}
		} else {
			$statusbox .= "There are no games in {$thisweek['nextname']}<br />";
		}
		$res->close();
	} else {
		$statusbox .= "There are no more weeks in the game";
	}
	echo <<<CODE
	<div class="head3">User Status</div>
	<div class="box1">
	<table border="0" cellspacing="0" cellpadding="2" style="width:100%;">
	<tr valign="top" style="text-align:center;"><td style="width:50%;">
	<div class="head4">Rank and Score</div>
	<div class="box2">
	$rankbox
	</div></td>
	<td style=\"width:50%;\"><div class=\"head4\">Pick Status for {$_SESSION['thisweek']['name']} & {$_SESSION['thisweek']['nextname']}</div>
	<div class="box2">
	
	$statusbox
	</div>
	</td></tr></table>
	</div><br />
CODE;
}

function get_options() {
	global $thisweek;
	global $ud;
	$optionform = "";
	$db = hk_db_connect();
	$res = $db->query("SELECT id,postseason FROM weeks ORDER BY id");
	while(($row = $res->fetch_assoc()) != NULL) {
		$optionform .= "<option value=\"".$row['id']."\" ";
		if ($row['id'] == $thisweek['id']) {
			$optionform .= "selected=\"selected\"";
		}
		$optionform .= ">".hk_week_name($row)."</option>\n";
	}
	echo <<<CODE
	<div class="head3">Options</div>
	<div class="box1">
	<table border="0" cellpadding="2" cellspacing="0" style="width:100%;">
	<tr style="text-align:center;" valign="top">
	<td style="width:50%;">
	<span class='head4'>Game Options</span>
	<div class="box2" style="text-align:left;">
	<ul type="disc">
	<li><a href="reports.php">Weekly Pick Reports</a></li>
	<li><a href="scoreReport.php">Weekly Score Reports</a></li>
	</ul>
	</div>
	</td>
	<td style="width:50%;">
	<span class='head4'>User Options</span>
	<div class="box2" style="text-align:left;">
	<ul>
	<li><a href='user.php'>Update my information</a></li>
	<li>
	<form action="picks.php" method="get">
	<select name="week">
	$optionform
	</select>
	<input type="submit" value="View" />
	</li>
	<li>
	<a href='login.php?a=lo'>Logout</a>
	</li>
	</ul>
	</form>
	</div>
	</td></tr>
	</table>
	</div><br />
CODE;
}	

function get_standings() {
	global $ud;
	global $thisweek;
	
	$tds = array(0=>"style=\"text-align:right;\"","style=\"text-align:center;\"","style=\"text-align:center;\"","style=\"text-align:center;\"","style=\"text-align:center;\"","style=\"text-align:center;\"");
	$tdc = array(0=>"class=\"trbl\"","class=\"trbl\"","class=\"trbl\"","class=\"trbl\"","class=\"trbl\"","class=\"trbl\"");
	$hstr = array(0=>"href=\"toc.php?scol=0&sdesc=0#standings\"","href=\"toc.php?scol=1&sdesc=1#standings\"","href=\"toc.php?scol=2&sdesc=1#standings\"","href=\"toc.php?scol=3&sdesc=1#standings\"","href=\"toc.php?scol=4&sdesc=1#standings\"","href=\"toc.php?scol=5&sdesc=0#standings\"");
	$hstr[$_GET['scol']] = "href=\"toc.php?scol={$_GET['scol']}&sdesc=".(!$_GET['sdesc'])."#standings\"";
	$tblstr = "";
	$tdc[$_GET['scol']]="class=\"trbls\"";
	switch($_GET['scol']) {
	case '0':
		$order="ORDER BY users.lastname ";
		break;
	case '1':
		$order="ORDER BY wins ";
		break;
	case '2':
		$order="ORDER BY sevens ";
		break;
	case '3':
		$order="ORDER BY closests ";
		break;
	case '4':
		$order="ORDER BY perfects ";
		break;
	case '5':
		$order="ORDER BY score ";
		break;
	default:
		$order="ORDER BY scores.score DESC,users.lastname ";
		$tdc[5] = "class=\"trbls\"";
	}
	if ($_GET['sdesc']) {
		$order .= "DESC";
	}
	if ($_GET['scol'] != 0) {
		$order .= ", users.lastname, users.firstname";
	} else {
		$order .= ", users.firstname";
	}
	$db = hk_db_connect();
	$res = $db->query("SELECT CONCAT(users.firstname,' ',users.lastname) AS name, scores.* FROM users LEFT JOIN scores ON (scores.user=users.id) WHERE users.id != -1 $order");
	echo $db->error;
	while(($row = $res->fetch_assoc()) != NULL) {
		$tblstr.="<tr><td {$tds[0]} {$tdc[0]}>{$row['name']}</td>";
		$tblstr.="<td {$tds[1]} {$tdc[1]}>{$row['wins']}</td>";
		$tblstr.="<td {$tds[2]} {$tdc[2]}>{$row['sevens']}</td>";
		$tblstr.="<td {$tds[3]} {$tdc[3]}>{$row['closests']}</td>";
		$tblstr.="<td {$tds[4]} {$tdc[4]}>{$row['perfects']}</td>";
		$tblstr.="<td {$tds[5]} {$tdc[5]}>{$row['score']}</td></tr>\n";
	}
	echo<<<CODE
	<div class="head3"><a name="standings">Current Standings</div>
	<div class="box1">
	<table border="0" cellpadding="2" cellspacing="0" style="width:100%;"
	<tr style="white-space:nowrap; text-align:center;">
	<td><a {$hstr[0]}>Players</a></td>
	<td><a {$hstr[1]}>Wins Picked</a></td>
	<td><a {$hstr[2]}>7 of 7's</a></td>
	<td><a {$hstr[3]}>Closest Picked Games</a></td>
	<td><a {$hstr[4]}>Perfect Picked Games</a></td>
	<td><a {$hstr[5]}>Score</a></td>
	</tr>
	$tblstr
	</table>
	</div>
CODE;
}
?>
