<?php
include('head.php');
global $db_link;

$week_r = hk_db_query("SELECT id FROM weeks ORDER BY id DESC LIMIT 1",$db_link);
$week_row = mysql_fetch_assoc($week_r);
$hkWeek = $week_row['id'];

echo <<<CODE
	<h1><a name="top">Weekly Game Reports</a></h1>
	<a href="toc.php">Back to Table of Contents</a><br />
	<br />
	<div style="text-align:center;">

	<form name="selweek" action="" method="get" onsubmit="chweek(); return false;">
		View: <select name="week" onchange="chweek()">
CODE;
	$weekr = hk_db_query("SELECT id FROM weeks ORDER BY id",$db_link);
	while ($week_row = mysql_fetch_assoc($weekr)) {
		echo "\t\t\t<option value=\"{$week_row['id']}\">".hk_week_name($week_row['id'])."</option>\n";
	}

echo <<<CODE
		</select>
		<input type="submit" value="View" />
	</form>
CODE;
// ****************BEGIN CONTENT **********************
for ($iWeek = 1; $iWeek <= $hkWeek; $iWeek++) {
	echo "<hr /><br />\n";
	$game_r = hk_db_query("SELECT games.*,t1.name AS 'homename',t2.name AS 'awayname',UNIX_TIMESTAMP(games.gametime) AS 'gametimestamp' FROM games LEFT JOIN teams AS t1 ON t1.id=games.home LEFT JOIN teams AS t2 ON t2.id=games.away WHERE games.week_id=$iWeek ORDER BY games.week_id,games.gametime",$db_link);
	$num_games = mysql_num_rows($game_r);
	$game_cnt = 0;
	$game_set = 1;
	$max_games = 7;
	while($num_games != 0) {
		$data = array();
		$params = array();
		$data[0][0] = "<div style=\"font-size:16px;font-weight:bolder\"><a name=\"week$iWeek".($game_set!=1?"-".$game_set:"")."\">".hk_week_name($iWeek).($game_set!=1?"-".$game_set:"")."</a><br />\n";
		$data[0][0] = $data[0][0]."<span style=\"font-size:10px; font-weight:normal;\">[<a href=\"#top\">Back to top</a>]</span>\n</div>\n";
		$params[0][0] = " style=\"text-align:center;\"";
		
		$data[1][0] = "Final Score"; $params[1][0] = " style=\"font-weight:bold;\" class=\"right_trbl\"";
		$row = 2;
		$col = 1;
		while ($game_cnt == 0 || $game_cnt != $max_games) {
			$g_row = mysql_fetch_assoc($game_r);
			$data[0][$col+0] = $g_row['awayname']; 		$params[0][$col+0] = " class=\"center_tbl\"";
			$data[0][$col+1] = "@";						$params[0][$col+1] = " class=\"center_tb\"";
			$data[0][$col+2] = $g_row['homename'];		$params[0][$col+2] = " class=\"center_trb\"";
			$data[1][$col+0] = $g_row['away_score'];	$params[1][$col+0] = " style=\"font-weight:bold;\" class=\"center_tbl\"";
			$data[1][$col+1] = "@";						$params[1][$col+1] = " style=\"font-weight:bold;\" class=\"center_tb\"";
			$data[1][$col+2] = $g_row['home_score'];	$params[1][$col+2] = " style=\"font-weight:bold;\" class=\"center_trb\"";
			$pick_r = hk_db_query("SELECT picks.*,CONCAT(u1.firstname,' ',u1.lastname) AS 'name' FROM picks LEFT JOIN participants AS u1 ON u1.id=picks.user_id WHERE game_id={$g_row['id']} ORDER BY u1.firstname",$db_link);
			while ($p_row = mysql_fetch_assoc($pick_r)) {
				$data[$row][0] = $p_row['name']; 			$params[$row][0] = " class=\"right_trbl\"";
				$data[$row][$col] = $p_row['away_score'];	$params[$row][$col+0] = " class=\"center_tbl\"";
				$data[$row][$col+1] = "@";					$params[$row][$col+1] = " class=\"center_tb\"";
				$data[$row][$col+2] = $p_row['home_score'];	$params[$row][$col+2] = " class=\"center_trb\"";
				$row++;
			}
			$row = 2;
			$col += 3;
			$game_cnt++;
		}
		print_table($data,"<table border=\"0\" cellspacing=\"0\">",$params);
		echo "<br />\n";
		$game_cnt = 0;
		$game_set += 1;
		$num_games -= $max_games;
	}
}

include("foot.php");
?>
