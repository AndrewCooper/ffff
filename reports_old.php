<?php
	include("head.php");

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

	$user_query = "SELECT id,firstname,lastname FROM participants ORDER BY firstname";
	for ($iWeek = 1; $iWeek <= $hkWeek; $iWeek++) {
		echo "<hr />";
		echo "<table border=\"0\" cellspacing=\"0\">\n";
		echo "<tr><td>";
		echo "<div style=\"font-size:16px;font-weight:bolder\"><a name=\"week$iWeek\">".hk_week_name($iWeek)."</a><br />\n";
		echo "<span style=\"font-size:10px; font-weight:normal;\">[<a href=\"#top\">Back to top</a>]</span>\n</div>\n";
		echo "</td>\n";
		// Column Headers
		$games = array();
		$game_result = hk_db_query("SELECT * FROM games WHERE week_id = $iWeek ORDER BY id",$db_link);
		while ($game_row = mysql_fetch_assoc($game_result)) {
			$games[] = $game_row['id'];
			$homer = mysql_fetch_assoc(hk_db_query("SELECT * FROM teams WHERE id=\"{$game_row['home']}\"",$db_link));
			$homen = $homer['name'];
			$awayr = mysql_fetch_assoc(hk_db_query("SELECT * FROM teams WHERE id=\"{$game_row['away']}\"",$db_link));
			$awayn = $awayr['name'];
			echo "<td class=\"center_tbl\">$awayn</td><td class=\"center_tb\">@</td><td class=\"center_trb\">$homen</td>";
		}
		echo "</tr>\n\n";

		// Final Score Line
		$game_query = "SELECT away_score,home_score FROM games WHERE week_id = $iWeek ORDER BY id";
		$game_result = mysql_query($game_query,$db_link);
		echo "<tr style=\"font-weight:bold;\"><td class=\"right_trbl\">Final Score</td>";
		while ($game_row = mysql_fetch_assoc($game_result)) {
			echo "<td class=\"center_tbl\">{$game_row['away_score']}</td><td class=\"center_tb\">@</td><td class=\"center_trb\">{$game_row['home_score']}</td>";
		}
		echo "</tr>\n\n";

		// User pick lines
		$user_result = mysql_query($user_query,$db_link);
		while ($user_row = mysql_fetch_assoc($user_result)) {
			echo "<tr><td class=\"right_trbl\">{$user_row['firstname']}&nbsp;{$user_row['lastname']}</td>";
			foreach($games as $gid) {
				$pick_r = mysql_fetch_assoc(hk_db_query("SELECT home_score,away_score FROM picks WHERE user_id={$user_row['id']} && game_id=$gid",$db_link));
				echo "<td class=\"center_tbl\">{$pick_r['away_score']}</td><td class=\"center_tb\">@</td><td class=\"center_trb\">{$pick_r['home_score']}</td>";
			}
			echo "</tr>\n\n";
		}
		echo "</table>\n";
		unset($games);
	}

	include("foot.php");
?>
