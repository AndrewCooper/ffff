<?php
session_start();
include('functions.php');
if(!isset($_SESSION['ffff_user_data'])) {
	hk_redir_rel('index.php');
} elseif($_POST['submitter']=="Submit Picks") {
	if($_SESSION['ffff_user_data']['id']!=-1) {
		$user=$_SESSION['ffff_user_data']['id'];
		$db = hk_db_connect();
		$res = $db->query("SELECT games.id AS gid, picks.id AS pid FROM games LEFT JOIN picks ON (picks.game=games.id AND picks.user=$user) WHERE games.week={$_POST['week']}");
		while(($row = $res->fetch_assoc())!=NULL) {
			$game=$row['gid'];
			$home=$_POST['home'][$game];
			$away=$_POST['away'][$game];
			if($home!=NULL && $away!=NULL) {
				if($row['pid']==NULL) {
					$db->query("INSERT INTO picks (user,game,home,away) VALUES ($user,$game,$home,$away)");
				} else {
					$db->query("UPDATE picks SET user=$user,game=$game,home=$home,away=$away WHERE id={$row['pid']}");
				}
			}
		}
		$_SESSION['message']="Picks submitted successfully.";
		hk_redir_rel('picks.php?week='.$_POST['week']);
	} else {
		$_SESSION['error']="I'm sorry, the guest account may not make picks.";
		hk_redir_rel('picks.php?week='.$_POST['week']);
	}
} else {
	include('head.html');
	
	if (isset($_GET['week'])) {
		$thisweek = hk_get_week($_GET['week']);
	} else {
		$thisweek = $_SESSION['thisweek'];
	}
	
echo <<<CODE
<div class="head5" style="text-align:right;"><a href="admin.php">[Administration]</a></div>
<img src="images/logo.gif" /><br />
<div style="font-size:smaller;"><a href="toc.php">[Back to Table of Contents]</a></div><br />
<div class="head1">Edit {$thisweek['name']} Picks</div>
CODE;
	hk_check_status();
	get_games_table();
	include('foot.html');
}

//helper functions
function get_games_table() {
global $thisweek;
echo<<<CODE
<form action='picks.php?week={$thisweek['id']}' method='post'>
<input type="hidden" name="week" value="{$thisweek['id']}" />
<div class="box1">
<table border='0' cellpadding='2' cellspacing='0' style='width:100%;'>
<!--
<thead style='font-size:large; text-align:center'><tr><td colspan='7'>{$thisweek['name']} Picks</td></tr></thead>
-->
<tfoot style='text-align:center;'><tr><td colspan='7'><input type='submit' name='submitter' value='Submit Picks' /></td></tr></tfoot>
<tbody>
CODE;
game_row_query($thisweek['id'],$_SESSION['ffff_user_data']['id']);
echo<<<CODE
</tbody>
</table>
</div>
</form>
CODE;
}

function game_row_query($week,$user) {
	$q = "SELECT ";
	$q .= "g.id AS gid, g.home_score AS ghs, g.away_score AS gas, g.gametime AS gtime, ";
	$q .= "h.name AS hn, h.image AS hi, h.location AS hl, h.conference AS hc, h.rank AS hr, h.record AS hrec, ";
	$q .= "a.name AS an, a.image AS ai, a.location AS al, a.conference AS ac, a.rank AS ar, a.record AS arec, ";
	$q .= "p.id AS pid, p.home AS phs, p.away AS pas, ";
	$q .= "b.name AS bn, b.description AS bd, b.location AS bl, b.multiplier AS bm, w.postseason AS bowlgame ";
	$q .= "FROM games AS g LEFT JOIN teams AS h ON (h.id=g.home) LEFT JOIN teams AS a ON (a.id=g.away) LEFT JOIN picks AS p ON (p.game=g.id AND p.user=$user) LEFT JOIN bowls AS b ON (b.game=g.id) LEFT JOIN weeks AS w ON (w.id=g.week) ";
	$q .= "WHERE g.week=$week ORDER BY g.gametime";
	$db = hk_db_connect();
	$res = $db->query($q);
	echo $db->error;
	while (($row = $res->fetch_assoc()) != NULL) {
	$now=time();
	if (($now > $row['gtime']) && (!$_SESSION['ffff_user_data']['is_admin'])) {
		$row['ro']="readonly=\"readonly\" ";
	}
	if ($row['ar']=="-1") {
		$row['ar']="N/A";
	}
	if ($row['hr']=="-1") {
		$row['hr']="N/A";
	}
	$gtime="@<br /><div style=\"font-size:smaller;\">".date("g:ia",$row['gtime'])."<br />".date("D n/j/y",$row['gtime'])."</div>";

	if ($row['bowlgame'] > 0) {
echo <<<CODE
<tr><td colspan="7" class="bwl_name">{$row['bn']}</td></tr>
<tr><td colspan="7" class="bwl_desc">{$row['bd']}</td></tr>
<tr><td colspan="7" class="bwl_loc">{$row['bl']}</td></tr>\n
CODE;
	}

echo <<<CODE
<tr>
<td class='tbl' style='text-align:right;width:25%'>
<div style='font-weight:bold;'>{$row['an']}</div>
<div style='font-style:italic;font-size:smaller;'>{$row['ac']}<br />{$row['al']}<br />Rank: {$row['ar']} ({$row['arec']})</div></td>
<td class='tb' style='text-align:center;width:15%'><img src='logos/{$row['ai']}' style='width:50px; height:50px;' /></td>
<td class='trb' style='text-align:center;width:5%'><input type='text' name='away[{$row['gid']}]' size='4' value='{$row['pas']}' {$row['ro']}/></td>
<td style='width:10%; text-align:center;'>$gtime</td>
<td class='tbl' style='text-align:center;width:5%'><input type='text' name='home[{$row['gid']}]' size='4' value='{$row['phs']}' {$row['ro']}/></td>
<td class='tb' style='text-align:center;width:15%'><img src='logos/{$row['hi']}' style='width:50px; height:50px;' /></td>
<td class='trb' style='text-align:left;width:25%'>
<div style='font-weight:bold;'>{$row['hn']}</div>
<div style='font-style:italic;font-size:smaller;'>{$row['hc']}<br />{$row['hl']}<br />Rank: {$row['hr']} ({$row['hrec']})</div></td>
</tr>
CODE;
	if ($row['bowlgame'] > 0) {
echo <<<CODE
<tr><td>&nbsp;</td></tr>
CODE;
	}
	}
}
?>
