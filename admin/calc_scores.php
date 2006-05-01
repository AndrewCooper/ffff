<?php
session_start();
include('../functions.php');
calculate_scores();

function calculate_scores() {
	$db=hk_db_connect();
	$res=$db->query("SELECT MIN(week) AS minweek, MAX(week) AS maxweek FROM games");
	$row=$res->fetch_assoc();
	$res->close();
	$maxweek=hk_get_week($row['maxweek']);
	$minweek=hk_get_week($row['minweek']);
	$total_wins = array();
	$total_sevens = array();
	$total_exacts = array();
	$total_closest = array();
	$total_total = array();
	ob_start();
	echo <<<CODE
		<h1><a name="top">Score Report for {$minweek['name']} to {$maxweek['name']}</a></h1>
		<a href="toc.php">Back to Table of Contents</a><br /><br />
		<h3>Scores are calculated using the formula*:</h3>
		<table style="font-family:monospace; border:1px black solid; text-align:center; margin:0px auto;">
		<tr><td rowspan="3" style="vertical-align:top; text-align:right;">SCORE =</td>
		<td style="text-align:left;">&nbsp;&nbsp;|picked home score - real home score|</td>
		<tr><td style="text-align:left;">+ |picked away score - real away score|</td></tr>
		<tr><td style="text-align:left;">+ |picked point spread - real point spread|</td></tr>
		</table>
		*Scores are applicable only if the correct winner was picked.<br />
		A <span style="font-weight:bold; color:red">red</span> score indicates the lowest score for a game.<br /><br />

		<form name="selweek" action="" method="get" onsubmit="chweek(); return false;">
			View: <select name="week" onchange="chweek()">
CODE;
		$wres=$db->query("SELECT id,postseason FROM weeks ORDER BY id");
		while ($wrow = $wres->fetch_assoc()) {
			echo "<option value=\"{$wrow['id']}\">".hk_week_name($wrow)."</option>\n";
		}

		echo <<<CODE
			</select>
			<input type="submit" value="View" />
		</form>
CODE;

	for ($iweek = $minweek['id']; $iweek <= $maxweek['id']; $iweek++) {
		$week_data=hk_get_week($iweek);
		$score_array = array(); //user x game

		$wins = array();
		$sevens = array();
		$exacts = array();
		$closest = array();
		$total = array();
		$game_closest = array();
		
		$ures = $db->query("SELECT * FROM users WHERE id != -1 ORDER BY lastname,firstname");
		while ($user_row = $ures->fetch_assoc()) {
			$uid = $user_row['id'];
			if (!isset($wins[$uid])) {
				$wins[$uid] = 0;
				$exacts[$uid] = 0;
				$bonus[$uid] = 0;
			}
			$gres = $db->query("SELECT id,home_score,away_score FROM games WHERE week=\"$iweek\" ORDER BY gametime");
			while ($grow = $gres->fetch_assoc()) {
				$q="SELECT id,away,home FROM picks WHERE game=\"{$grow['id']}\" AND user=\"$uid\"";
				$pres = $db->query($q);
				if ($pres->num_rows > 0) {
					$prow = $pres->fetch_assoc();
					$hp = $prow['home'];
					$ap = $prow['away'];
					$ha = $grow['home_score'];
					$aa = $grow['away_score'];
					if (($hp>$ap&&$ha>$aa) || ($hp<$ap&&$ha<$aa) || ($hp==$ap&&$ha==$aa)) {
						$wins[$uid] += 1;
						$score = abs($hp-$ha)+abs($ap-$aa)+abs(abs($hp-$ap)-abs($ha-$aa));
						if ($score == 0) {
							$exacts[$uid] += 1;
						}
					} else {
						$score = -1;
					}
				} else {
					$score = -1;
				}
				//echo $uid." x ".$grow['id']."<br />";
				$score_array[$uid][$grow['id']] = $score;
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
		echo "<a name=\"week$iweek\"><h2 style=\"text-align:left;\">".$week_data['name']."</a><br />\n";
		echo "<span style=\"font-size:10px; font-weight:normal;\">[<a href=\"#top\">Back to top</a>]</span>\n</h2>\n";
		echo "<table border=\"1\" cellspacing=\"0\" cellpadding=\"2\">\n";
		echo "<tr style=\"text-align:center;\"><td></td><td colspan=\"".count($score_array[$sa1])."\">Score Formula Result for Games</td>";
		echo "<td colspan=\"5\">Week Scores</td><td colspan=\"5\">Total Scores</td></tr>\n";
		echo "<tr><td>Player Name</td>";
		foreach ($score_array[$sa1] as $game => $gamev) {
			$gres = $db->query("SELECT home.name AS hname, away.name AS aname FROM games LEFT JOIN teams AS home ON (home.id=games.home) LEFT JOIN teams AS away ON (away.id=games.away) WHERE games.id=\"$game\"");
			$grow = $gres->fetch_assoc();
			echo "<td>{$grow['aname']}<br />@<br />{$grow['hname']}</td>";
		}

		echo "\n<td>Wins</td><td>7&nbsp;of&nbsp;7's</td><td>Perfects</td><td>Closests</td><td>Score</td>\n";
		echo "<td>Wins</td><td>7&nbsp;of&nbsp;7's</td><td>Perfects</td><td>Closests</td><td>Score</td></tr>\n";
		foreach ($score_array as $user => $user_ary) {
			$ures = $db->query("SELECT firstname,lastname FROM users WHERE id=\"$user\"");
			$urow = $ures->fetch_assoc();
			echo "<tr style=\"text-align:right;\"><td>{$urow['firstname']}<br />{$urow['lastname']}</td>";
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
	include("head.html");
	$file = fopen("../scores.php","w");
	if (fwrite($file,$output)) {
		echo "scores.php successfully written to disk";
	} else {
		echo "There were problems writing scores.php to disk";
	}
	fclose($file);
	foreach ($wins as $user => $user_ary) {
		$update = "UPDATE scores SET score=\"{$total_total[$user]}\",wins=\"{$total_wins[$user]}\",closests=\"{$total_closest[$user]}\",perfects=\"{$total_exacts[$user]}\",sevens=\"{$total_sevens[$user]}\" WHERE user=\"$user\"";
		echo $update."<br />\n";
		$db->query($update);
		echo $db->error;
	}
	include("foot.html");
}
?>
