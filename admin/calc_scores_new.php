<?php
ob_start();
echo <<<CODE
<h1><a name="top">Results from Week {$_GET['start']} to Week {$_GET['end']}</a></h1>
<a href="toc.php">Back to Table of Contents</a><br /><br />
<h3>Scores are calculated using the formula*:</h3>
<table style="font-family:monospace; border:1px black solid; text-align:center; margin:0px auto;">
<tr><td rowspan="3" style="vertical-align:top; text-align:right;">SCORE =</td>
<td style="text-align:left;">&nbsp;&nbsp;|picked home score - real home score|</td>
<tr><td style="text-align:left;">+ |picked away score - real away score|</td></tr>
<tr><td style="text-align:left;">+ |picked point spread - real point spread|</td></tr>
</table>
*Scores are applicable only if the correct winner was picked.<br />
A <span style="font-weight:bold; color:red">red</span> score indicates the lowest score for a game.
CODE;

echo "<br /><br />";

echo <<<CODE
<form name="selweek" action="" method="get" onsubmit="chweek(); return false;">
	View: <select name="week" onchange="chweek()">
CODE;
$weekr = hk_db_query("SELECT id,postseason FROM weeks ORDER BY id",$db_link);
while ($week_row = mysql_fetch_assoc($weekr)) {
	if ($week_row['id'] >= $_POST['start'] && $week_row['id'] <= $_POST['end']) {
		echo "\t\t\t<option value=\"{$week_row['id']}\">".hk_week_name($week_row['id'])."</option>\n";
	}
}

echo <<<CODE
	</select>
	<input type="submit" value="View" />
</form>
CODE;

$win_pts = 1;
$cls_pts = 2;
$ex_pts = 7;
$sev_pts = 7;

$total_wins = array();
$total_sevens = array();
$total_exacts = array();
$total_closest = array();
$total_total = array();

for ($iWeek = $_GET['start']; $iWeek <= $_GET['end']; $iWeek++) {
//for ($iWeek = 1; $iWeek <= 17; $iWeek++) {
	echo "<hr />\n";
	$game_r = hk_db_query("SELECT games.*,t1.name AS 'homename',t2.name AS 'awayname',UNIX_TIMESTAMP(games.gametime) AS 'gametimestamp' FROM games LEFT JOIN teams AS t1 ON t1.id=games.home LEFT JOIN teams AS t2 ON t2.id=games.away WHERE games.week_id=$iWeek ORDER BY games.week_id,games.gametime",$db_link);
	$w_row = mysql_fetch_assoc(hk_db_query("SELECT postseason FROM weeks WHERE id=$iWeek",$db_link));

	$num_games = mysql_num_rows($game_r);
	$game_cnt = 0;
	$game_set = 1;
	$max_games = 7;
	
	while($num_games != 0) {
		$score_array = array(); //user x game
		$data = array();
		$params = array();
	
		$wins = array();
		$sevens = array();
		$exacts = array();
		$closest = array();
		$total = array();
		$game_closest = array();
	
		$row0 = 4;
		$gamerow = 1;
		$col0 = 1;
		$namecol = 0;
		$rescol = 8;
	
		$data[0][0] = "<div style=\"font-size:16px;font-weight:bolder\"><a name=\"week$iWeek".($game_set!=1?"-".$game_set:"")."\">".hk_week_name($iWeek).($game_set!=1?"-".$game_set:"")."</a><br />\n";
		$data[0][0] = $data[0][0]."<span style=\"font-size:10px; font-weight:normal;\">[<a href=\"#top\">Back to top</a>]</span>\n</div>\n";
		$params[0][0] = "style=\"text-align:center;\"";
	
		$data[0][1] = "Score Formula Results";	$params[0][1] = "colspan=\"7\" class=\"center_trbl\"";
		$data[1][0] = "Player Name"; 			$params[1][0] = "rowspan=\"3\" class=\"center_trbl\"";
		$data[0][$rescol+0] = "";				$params[0][$rescol+0] = "";
		$data[1][$rescol+0] = "";				$params[1][$rescol+0] = "";
		$data[0][$rescol+1] = "Week results";	$params[0][$rescol+1] = "colspan=\"5\" class=\"center_trbl\"";
		$data[1][$rescol+1] = "Wins";			$params[1][$rescol+1]="rowspan=\"3\" class=\"center_trbl\"";
		$data[1][$rescol+2] = "Sevens";			$params[1][$rescol+2]="rowspan=\"3\" class=\"center_trbl\"";
		$data[1][$rescol+3] = "Perfects";		$params[1][$rescol+3]="rowspan=\"3\" class=\"center_trbl\"";
		$data[1][$rescol+4] = "Closests";		$params[1][$rescol+4]="rowspan=\"3\" class=\"center_trbl\"";
		$data[1][$rescol+5] = "Score";			$params[1][$rescol+5]="rowspan=\"3\" class=\"center_trbl\"";
		$data[0][$rescol+6] = "";				$params[0][$rescol+6] = "";
		$data[1][$rescol+6] = "";				$params[1][$rescol+6] = "";
		$data[0][$rescol+7] = "Total results";	$params[0][$rescol+7] = "colspan=\"5\" class=\"center_trbl\"";
		$data[1][$rescol+7] = "Wins";			$params[1][$rescol+7]="rowspan=\"3\" class=\"center_trbl\"";
		$data[1][$rescol+8] = "Sevens";			$params[1][$rescol+8]="rowspan=\"3\" class=\"center_trbl\"";
		$data[1][$rescol+9] = "Perfects";		$params[1][$rescol+9]="rowspan=\"3\" class=\"center_trbl\"";
		$data[1][$rescol+10] = "Closests";		$params[1][$rescol+10]="rowspan=\"3\" class=\"center_trbl\"";
		$data[1][$rescol+11] = "Score";			$params[1][$rescol+11]="rowspan=\"3\" class=\"center_trbl\"";
		$row = $row0;
		$col = $col0;
		while ($game_cnt == 0 || $game_cnt != $max_games) {
			$g_row = mysql_fetch_assoc($game_r);
			if($w_row['postseason'] != 0) {
				$b_row = mysql_fetch_assoc(hk_db_query("SELECT multiplier FROM bowls WHERE game_id={$g_row['id']}",$db_link));
				$mult = $b_row['multiplier'];
			} else {
				$mult = 1;
			}
			$best_score = -1;
			
			$pick_r = hk_db_query("SELECT picks.*,CONCAT(u1.firstname,'<br />',u1.lastname) AS 'fullname',u1.* FROM picks LEFT JOIN participants AS u1 ON u1.id=picks.user_id WHERE game_id={$g_row['id']} ORDER BY u1.firstname",$db_link);
			while ($p_row = mysql_fetch_assoc($pick_r)) {
				$uid = $p_row['user_id'];
				if (!isset($wins[$uid])) {
					$wins[$uid] = 0;
					$exacts[$uid] = 0;
					$bonus[$uid] = 0;
				}
				$hp = $p_row['home_score'];
				$ap = $p_row['away_score'];
				$ha = $g_row['home_score'];
				$aa = $g_row['away_score'];
				if (($hp>$ap&&$ha>$aa) || ($hp<$ap&&$ha<$aa) || ($hp==$ap&&$ha==$aa)) {
					$wins[$uid] += 1;
					$total[$uid] += $mult*$win_pts;
					$score = abs($hp-$ha)+abs($ap-$aa)+abs(abs($hp-$ap)-abs($ha-$aa));
					if ($score == 0) {
						$exacts[$uid] += 1;
						$total[$uid] += $mult*$ex_pts;
					}
				} else {
					$score = -1;
				}
				if ($wins[$uid] == 7) {
					$sevens[$uid] = 1;
					$total[$uid] += $mult*$sev_pts;
				} else {
					$sevens[$uid] = 0;
				}

				$score_array[$uid][$g_row['id']] = $score ;
				
				$data[$gamerow+0][$col] = $g_row['awayname'];	$params[$gamerow+0][$col] = "class=\"center_trl\"";
				$data[$gamerow+1][$col] = "@";					$params[$gamerow+1][$col] = "class=\"center_rl\"";
				$data[$gamerow+2][$col] = $g_row['homename'];	$params[$gamerow+2][$col] = "class=\"center_rbl\"";
				
				$data[$row][$namecol] = $p_row['fullname'];		$params[$row][$namecol]="class=\"right_trbl\"";
				$data[$row][$col] = $score != -1? $score : "N/A";$params[$row][$col]="class=\"center_trbl\"";
				
				if($best_score == -1 || ($score < $best_score && $score != -1)) {
					$best_score = $score;
				}

				$row++;
			}
			foreach($data as $rowid => $row) {
				if ($data[$rowid][$col] === $best_score) {
					$params[$rowid][$col] .= " style=\"color:red; font-weight:bold;\"";
				}
			}
			foreach($score_array as $uid => $uscores) {
				if($score_array[$uid][$g_row['id']] == $best_score && $best_score != -1) {
					$closest[$uid] += 1;
					$total[$uid] += $mult*$cls_pts;
				} else {
					$closest[$uid] += 0;
				}
			}
			
			$row = $row0;
			$col += 1;
			$game_cnt++;
		}
		$game_cnt = 0;
		$game_set += 1;
		$num_games -= $max_games;

		$rescol = 8;
		$row = $row0;
		$user_r = hk_db_query("SELECT * FROM participants ORDER BY firstname",$db_link);
		while($u_row = mysql_fetch_assoc($user_r)) {
			$uid = $u_row['id'];
			$total_wins[$uid] += $wins[$uid];
			$total_sevens[$uid] += $sevens[$uid];
			$total_exacts[$uid] += $exacts[$uid];
			$total_closest[$uid] += $closest[$uid];
			$total_total[$uid] += $total[$uid];

			$data[$row][$rescol+0] = "";
			$data[$row][$rescol+1] = $wins[$uid]+0;			$params[$row][$rescol+1]="class=\"center_trbl\"";
			$data[$row][$rescol+2] = $sevens[$uid]+0;		$params[$row][$rescol+2]="class=\"center_trbl\"";
			$data[$row][$rescol+3] = $exacts[$uid]+0;		$params[$row][$rescol+3]="class=\"center_trbl\"";
			$data[$row][$rescol+4] = $closest[$uid]+0;		$params[$row][$rescol+4]="class=\"center_trbl\"";
			$data[$row][$rescol+5] = $total[$uid]+0;		$params[$row][$rescol+5]="class=\"center_trbl\"";
			$data[$row][$rescol+6] = "";
			$data[$row][$rescol+7] = $total_wins[$uid]+0;	$params[$row][$rescol+7]="class=\"center_trbl\"";
			$data[$row][$rescol+8] = $total_sevens[$uid]+0;	$params[$row][$rescol+8]="class=\"center_trbl\"";
			$data[$row][$rescol+9] = $total_exacts[$uid]+0;	$params[$row][$rescol+9]="class=\"center_trbl\"";
			$data[$row][$rescol+10] = $total_closest[$uid]+0;	$params[$row][$rescol+10]="class=\"center_trbl\"";
			$data[$row][$rescol+11] = $total_total[$uid]+0;	$params[$row][$rescol+11]="class=\"center_trbl\"";
			$row++;
		}
		ksort($data,SORT_NUMERIC);
		foreach($data as $rowid => $row) {
			ksort($data[$rowid],SORT_NUMERIC);
		}
		print_table($data,"<table border=\"0\" cellspacing=\"0\">",$params);
		echo "<br />\n";
	}
}
$output = ob_get_contents();
ob_end_clean();
$file = fopen("scores.php","w");
if (fwrite($file,$output)) {
	echo "scores.php successfully written to disk";
} else {
	echo "There were problems writing scores.php to disk";
}
fclose($file);
echo $output;
foreach ($wins as $user => $user_ary) {
	$update = "UPDATE participants SET score=\"".$total_total[$user]."\",wins=\"".$total_wins[$user]."\",closest=\"".$total_closest[$user]."\",exact=\"".$total_exacts[$user]."\",sevens=\"".$total_sevens[$user]."\" WHERE id=\"$user\"";
	hk_db_query($update,$db_link);
}

?>