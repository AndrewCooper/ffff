<?php
	include("head.php");
	
	echo "<h1>Edit Picks</h1>";
	echo "<a href=\"toc.php\">Back to Table of Contents</a><br />";
	echo "<br />\n";
	if ($_POST['submitter'] == "Save Picks") {
		$game_result = hk_db_query("SELECT * FROM games WHERE week_id=\"{$_POST['week']}\" ORDER BY gametime",$db_link);
		while ($game_row = mysql_fetch_assoc($game_result)) {
			$aways = $_POST["away".$game_row['id']];
			$homes = $_POST["home".$game_row['id']];
			$pickr = hk_db_query("SELECT * FROM picks where user_id=\"{$_SESSION['login_id']}\" && game_id=\"{$game_row['id']}\"",$db_link);
			$pickn = mysql_fetch_assoc($pickr);
			if (mysql_num_rows($pickr) > 0) {
				$edit_result = hk_db_query("UPDATE picks SET away_score=\"$aways\",home_score=\"$homes\" WHERE id=\"{$pickn['id']}\"",$db_link);
			} else {
				$new_result = hk_db_query("INSERT INTO picks (user_id,game_id,away_score,home_score) VALUES (\"{$_SESSION['login_id']}\",\"{$game_row['id']}\",\"$aways\",\"$homes\")",$db_link);
			}
		}
	}
	if (isset($_GET['week'])) {
		$hkweek = $_GET['week'];
		$weekStr = "week=".$_GET['week'];
	} else {
		$hkweek = $_SESSION['week_id']+1;
		$weekStr = "";
	}

	$week_r = mysql_fetch_assoc(hk_db_query("SELECT postseason FROM weeks WHERE id=$hkweek",$db_link));
	echo "<h2>".hk_week_name($hkweek)."</h2>";
	if (!$_SESSION['is_admin'] == 1) {
		if ($hkweek != $_SESSION['week_id']+1) {
			echo "<h4>You cannot edit these picks.<br />If you need to submit, you must email <a href=\"mailto:kruckeb@okstate.edu\">Nic at kruckeb@okstate.edu</a>.</h4>";
		}
	}
	echo <<<CODE
		<style>
			td.ti {
				border-style:solid;
				border-width:0px 0px 1px 1px;
				border-color:#808080;
			}
			td.lg {
				border-style:solid;
				border-width:0px 0px 1px 0px;
				border-color:#808080;
			}
			td.sc {
				border-style:solid;
				border-width:0px 1px 1px 0px;
				border-color:#808080;
			}
			td.hdti {
				border-style:solid;
				border-width:1px 0px 1px 1px;
				border-color:#808080;
			}
			td.hdlg {
				border-style:solid;
				border-width:1px 0px 1px 0px;
				border-color:#808080;
			}
			td.hdsc {
				border-style:solid;
				border-width:1px 1px 1px 0px;
				border-color:#808080;
			}
			td.bwl_name {
				border-style:solid;
				border-width:1px 1px 0px 1px;
				border-color:#808080;
				font-size: large;
				font-weight: bolder;
				font-style: normal;
				text-align:center;
			}
			td.bwl_desc {
				border-style:solid;
				border-width:0px 1px 0px 1px;
				border-color:#808080;
				font-size: smaller;
				font-weight: normal;
				font-style: oblique;
				text-align:center;
			}
			td.bwl_date {
				border-style:solid;
				border-width:1px 1px 0px 1px;
				border-color:#808080;
				font-size: small;
				font-weight: bold;
				font-style: normal;
				text-align:center;
			}
			td.bwl_loc {
				border-style:solid;
				border-width:1px 1px 0px 1px;
				border-color:#808080;
				font-size: small;
				font-weight: bold;
				font-style: normal;
				text-align:center;
			}
		</style>
		<form action="picks.php?$weekStr" method="post">
		<input type="hidden" name="week" value="$hkweek" />
CODE;
	$week_row = mysql_fetch_assoc(hk_db_query("SELECT postseason FROM weeks WHERE id = \"$hkweek\"",$db_link));
	if ($week_row['postseason'] == "0") {
		// REGULAR SEASON GAME
		echo "<table border=\"0\" width=\"100%\" cellspacing=\"0\" cellpadding=\"3\">";
		echo_col_headers();
		$game_r = hk_db_query("SELECT * FROM games WHERE week_id=\"$hkweek\" ORDER BY id",$db_link);
		while ($game_row = mysql_fetch_assoc($game_r)) {
			echo_game_row($game_row);
		}
		echo "</table>";
	} else {
		// POST SEASON GAME
		$game_r = hk_db_query("SELECT * FROM games WHERE week_id = \"$hkweek\" ORDER BY id",$db_link);
		while ($game_row = mysql_fetch_assoc($game_r)) {
			echo "<table border=\"0\" width=\"100%\" cellspacing=\"0\" cellpadding=\"3\">\n";
			echo_bowl_info($game_row['id'],$game_row['gametime']);
			echo_col_headers();
			echo_game_row($game_row);
			echo "</table><br />\n";
		}
	}
	echo "<input type=\"submit\" name=\"submitter\" value=\"Save Picks\" />\n</form>\n";

	include("foot.php");

// ******* HELPER FUNCTIONS ********
function echo_col_headers() {
	echo <<<CODE
		<tr align="center">
		<td colspan="2" class="hdti">Away Team</td>
		<td class="hdsc">Score</td>
		<td class="hdlg">@</td>
		<td colspan="2" class="hdti">Home Team</td>
		<td class="hdsc">Score</td></tr>
CODE;
}	
function echo_bowl_info($game_id,$game_date) {
	global $db_link;
	$gdate = date("l, M jS, Y",strtotime($game_date));
	$bowl_row = mysql_fetch_assoc(hk_db_query("SELECT * FROM bowls WHERE game_id = \"$game_id\"",$db_link));
	echo <<<CODE
	<tr><td colspan="7" class="bwl_name">{$bowl_row['name']}</td></tr>
	<tr><td colspan="7" class="bwl_desc">{$bowl_row['description']}</td></tr>
	<tr><td colspan="7" class="bwl_date">$gdate</td></tr>
	<tr><td colspan="7" class="bwl_loc">{$bowl_row['location']}</td></tr>
CODE;
}
function echo_game_row($game_row) {
	global $db_link;
	$awayr = mysql_fetch_assoc(hk_db_query("SELECT * FROM teams WHERE id=\"{$game_row['away']}\"",$db_link));
	$homer = mysql_fetch_assoc(hk_db_query("SELECT * FROM teams WHERE id=\"{$game_row['home']}\"",$db_link));
	$pickq = "SELECT * FROM picks WHERE user_id=\"{$_SESSION['login_id']}\" && game_id=\"{$game_row['id']}\"";
	$pickr = mysql_fetch_assoc(hk_db_query($pickq,$db_link));
	if (!$_SESSION['is_admin'] == 1) {
		if ($hkweek != $_SESSION['week_id']+1) {
			$reads = "readonly=\"readonly\"";
		} else {
			$reads = "";
		}
	} else {
		$reads = "";
	}
	$ar = $awayr['rank'];
	if ($ar == 0) {
		$ar = "N/A";
	}
	$hr = $homer['rank'];
	if ($hr == 0) {
		$hr = "N/A";
	}
	
	echo <<<CODE
	
		<tr><td class="ti">{$awayr['name']}<br />Rank: $ar ({$awayr['record']})<br />{$awayr['location']}<br />{$awayr['conference']}</td>
		<td class="lg"><img src="logos/{$awayr['image']}" /></td>
		<td class="sc"><input type="text" name="away{$game_row['id']}" value="{$pickr['away_score']}" size="5" $reads /></td>
		<td class="lg">@</td>
		<td class="ti">{$homer['name']}<br />Rank: $hr ({$homer['record']})<br />{$homer['location']}<br />{$homer['conference']}</td>
		<td class="lg"><img src="logos/{$homer['image']}" /></td>
		<td class="sc"><input type="text" name="home{$game_row['id']}" value="{$pickr['home_score']}" size="5" $reads /></td>				
CODE;
}
?>
