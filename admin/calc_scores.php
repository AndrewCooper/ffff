<?php
	$total_wins = array();
	$total_sevens = array();
	$total_exacts = array();
	$total_closest = array();
	$total_total = array();
	ob_start();
	echo <<<CODE
		<h1><a name="top">Results from Week {$_POST['start']} to Week {$_POST['end']}</a></h1>
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

	for ($iweek = $_POST['start']; $iweek <= $_POST['end']; $iweek++) {
		
		$score_array = array(); //user x game

		$wins = array();
		$sevens = array();
		$exacts = array();
		$closest = array();
		$total = array();
		$game_closest = array();
		
		$user_result = hk_db_query("SELECT * FROM participants ORDER BY firstname",$db_link);
		while ($user_row = mysql_fetch_assoc($user_result)) {
			$game_result = hk_db_query("SELECT id,home_score,away_score FROM games WHERE week_id=\"$iweek\"",$db_link);
			$uid = $user_row['id'];
			if (!isset($wins[$uid])) {
				$wins[$uid] = 0;
				$exacts[$uid] = 0;
				$bonus[$uid] = 0;
			}
			while ($game_row = mysql_fetch_assoc($game_result)) {
				$pick = mysql_fetch_assoc(hk_db_query("SELECT id,away_score,home_score FROM picks WHERE game_id=\"{$game_row['id']}\" && user_id=\"{$user_row['id']}\"",$db_link));
				$hp = $pick['home_score'];
				$ap = $pick['away_score'];
				$ha = $game_row['home_score'];
				$aa = $game_row['away_score'];
				if (($hp>$ap&&$ha>$aa) || ($hp<$ap&&$ha<$aa) || ($hp==$ap&&$ha==$aa)) {
					$wins[$uid] += 1;
					$score = abs($hp-$ha)+abs($ap-$aa)+abs(abs($hp-$ap)-abs($ha-$aa));
					if ($score == 0) {
						$exacts[$uid] += 1;
					}
				} else {
					$score = -1;
				}
				//echo $uid." x ".$game_row['id']."<br />";
				$score_array[$uid][$game_row['id']] = $score;
			}
			if ($wins[$uid] == 7) {
				$sevens[$uid] = 1;
			} else {
				$sevens[$uid] = 0;
			}
		}

		$sak = array_keys($score_array);
		$sa1 = $sak[0];
		foreach ($score_array[$sa1] as $game => $gamev) {
			$c = 1;
			foreach ($score_array as $user => $user_ary) {
				if ($score_array[$user][$game] >= 0 && ($score_array[$user][$game] < $score_array[$c][$game] || $score_array[$c][$game] < 0)) {
					$c = $user;
				}
			}
			$game_closest[$game] = $c;
			foreach ($score_array as $user => $user_ary) {
				if ($score_array[$user][$game] == $score_array[$c][$game] && $score_array[$user][$game] != -1) {
					$closest[$user] += 1;
				} else {
					$closest[$user] += 0;
				}
			}
		}

		foreach ($wins as $user => $user_ary) {
			$total[$user] = 2*$closest[$user] + $wins[$user] + 7*$exacts[$user] + 7*$sevens[$user];

			$total_wins[$user] += $wins[$user];
			$total_sevens[$user] += $sevens[$user];
			$total_exacts[$user] += $exacts[$user];
			$total_closest[$user] += $closest[$user];
			$total_total[$user] = 2*$total_closest[$user] + $total_wins[$user] + 7*$total_exacts[$user] + 7*$total_sevens[$user];
		}
	
		//Results table for a week
		echo "<hr />";
		echo "<a name=\"week$iweek\"><h2 style=\"text-align:left;\">".hk_week_name($iweek)."</a><br />\n";
		echo "<span style=\"font-size:10px; font-weight:normal;\">[<a href=\"#top\">Back to top</a>]</span>\n</h2>\n";
		echo "<table border=\"1\" cellspacing=\"0\" cellpadding=\"2\">\n";
		echo "<tr style=\"text-align:center;\"><td></td><td colspan=\"".count($score_array[$sa1])."\">Score Formula Result for Games</td>";
		echo "<td colspan=\"5\">Week Scores</td><td colspan=\"5\">Total Scores</td></tr>\n";
		echo "<tr><td>Player Name</td>";
		foreach ($score_array[$sa1] as $game => $gamev) {
			$gamer = mysql_fetch_assoc(hk_db_query("SELECT home,away FROM games WHERE id=\"$game\"",$db_link));
			$homen = mysql_fetch_assoc(hk_db_query("SELECT name FROM teams WHERE id=\"{$gamer['home']}\"",$db_link));
			$awayn = mysql_fetch_assoc(hk_db_query("SELECT name FROM teams WHERE id=\"{$gamer['away']}\"",$db_link));
			 echo "<td>{$awayn['name']}&nbsp;@<br />{$homen['name']}</td>";
		}

		echo "\n<td>Wins</td><td>7&nbsp;of&nbsp;7's</td><td>Perfects</td><td>Closests</td><td>Score</td>\n";
		echo "<td>Wins</td><td>7&nbsp;of&nbsp;7's</td><td>Perfects</td><td>Closests</td><td>Score</td></tr>\n";
		foreach ($score_array as $user => $user_ary) {
			$usern = mysql_fetch_assoc(hk_db_query("SELECT firstname,lastname FROM participants WHERE id=\"$user\"",$db_link));
			echo "<tr style=\"text-align:right;\"><td>{$usern['firstname']}<br />{$usern['lastname']}</td>";
			foreach ($user_ary as $game => $gamev) {
				if ($gamev == -1) {
					echo "<td>N/A</td>";
				} else {
					if ($gamev == $score_array[$game_closest[$game]][$game]) {
						echo "<td><span style=\"font-weight:bold; color:red;\">$gamev</span></td>";
					} else {
						echo "<td>$gamev</td>";
					}
				}
			}
			echo "<td>{$wins[$user]}</td><td>{$sevens[$user]}</td><td>{$exacts[$user]}</td><td>{$closest[$user]}</td><td>{$total[$user]}</td>";
			echo "<td>{$total_wins[$user]}</td><td>{$total_sevens[$user]}</td><td>{$total_exacts[$user]}</td><td>{$total_closest[$user]}</td><td>{$total_total[$user]}</td></tr>\n";
		}
		echo "</table>\n";
		
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
	foreach ($wins as $user => $user_ary) {
		$update = "UPDATE participants SET score=\"".$total_total[$user]."\",wins=\"".$total_wins[$user]."\",closest=\"".$total_closest[$user]."\",exact=\"".$total_exacts[$user]."\",sevens=\"".$total_sevens[$user]."\" WHERE id=\"$user\"";
		//echo "<div class=\"databox\">$update</div>\n";
		mysql_query($update,$db_link);
		//echo mysql_error($db_link);
	}

?>
