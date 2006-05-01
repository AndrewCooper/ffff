<?php
	echo "\n<!-- BEGIN EDIT PICKS CODE -->\n";
	global $db_link;
	if ($_POST['submitter'] == "Edit Picks") {
//		print_r($_POST);
		$ids = explode(",",$_POST['ids']);

		foreach($ids as $id) {
			$aways = $_POST["away_".$id];
			$homes = $_POST["home_".$id];
			$edit_query = "UPDATE picks SET away_score=\"$aways\",home_score=\"$homes\" WHERE id=\"$id\"";
			hk_db_query($edit_query,$db_link);
		}
		if ($_POST['zero'] == "on") {
			$user_r = hk_db_query("SELECT id FROM participants ORDER BY id",$db_link);
			while($user_row = mysql_fetch_assoc($user_r)) {
				$game_r = hk_db_query("SELECT id FROM games WHERE week_id = {$_POST['pickweek']} ORDER BY id",$db_link);
				while($game_row = mysql_fetch_assoc($game_r)) {
					$pick_r = hk_db_query("SELECT * FROM picks WHERE user_id = {$user_row['id']} && game_id = {$game_row['id']}",$db_link);
					if(mysql_num_rows($pick_r) == 0) {
						$pick_q = "INSERT INTO picks (user_id,game_id,home_score,away_score) VALUES ({$user_row['id']},{$game_row['id']},0,0)";
//						echo $pick_q."<br />\n";
						$pick_a = hk_db_query($pick_q,$db_link);
					}
				}
			}
		}
	}
	if (isset($_GET['week'])) {
		$iweek = $_GET['week'];
	} else {
		$iweek = $_SESSION['week_id']+1;
	}
	
echo <<<CODE
	<div style="text-align:left;">
	<form action="admin.php" method="get">
		Edit Picks for: 
		<input type="hidden" name="page" value="picks" />
		<select name="week">
CODE;

	for($i = $_SESSION['week_id']+1; $i >= 1; $i--) {
		echo "\t\t<option value=\"$i\" ";
		if ($iweek == $i) 
			echo "selected=\"selected\" ";
		echo ">".hk_week_name($i)."</option>\n";
	}

echo <<<CODE
		</select>
		<input type="submit" value="View" />
	</form>
	</div>
CODE;
	echo <<<CODE
		<a name="week$iweek"><h2 style="text-align:left;">Week $iweek</h2></a>
		<form action="admin.php?action=edit_picks&week=$iweek" method="post">
		<table border="0" cellspacing="0">
		<thead><tr><td></td>
CODE;

	// Column Headers
	echo "\n<!-- column headers -->\n";
	$game_ids = array();
	$game_result = hk_db_query("SELECT * FROM games WHERE week_id = $iweek ORDER BY id",$db_link);
	while ($game_row = mysql_fetch_assoc($game_result)) {
		$game_ids[] = $game_row['id'];
		$homer = mysql_fetch_assoc(hk_db_query("SELECT * FROM teams WHERE id=\"{$game_row['home']}\"",$db_link));
		$homen = $homer['name'];
		$awayr = mysql_fetch_assoc(hk_db_query("SELECT * FROM teams WHERE id=\"{$game_row['away']}\"",$db_link));
		$awayn = $awayr['name'];
		echo "<td class=\"center_tbl\">$awayn</td><td class=\"center_trb\">$homen</td>\n";
	}
	echo "</tr></thead>\n";

	// Final Score Line
	echo "\n<!-- final score line -->\n";
	$game_query = "SELECT away_score,home_score FROM games WHERE week_id = $iweek ORDER BY id";
	$game_result = mysql_query($game_query,$db_link);
	echo "<tr style=\"font-weight:bold;\"><td class=\"right_trbl\">Final Score</td>\n";
	while ($game_row = mysql_fetch_assoc($game_result)) {
		echo "<td class=\"center_tbl\">{$game_row['away_score']}</td><td class=\"center_trb\">{$game_row['home_score']}</td>\n";
	}
	echo "<td></td></tr>\n";

	// User pick lines
	echo "\n<!-- user pick lines -->\n";
	$user_result = hk_db_query("SELECT id,firstname,lastname FROM participants ORDER BY firstname",$db_link);
	$pick_ids = array();
	while ($user_row = mysql_fetch_assoc($user_result)) {
		echo <<<CODE
		<tr style="vertical-align:top;">
		<td class="right_trbl">{$user_row['firstname']}&nbsp;{$user_row['lastname']}</td>\n
CODE;
		foreach ($game_ids as $key => $gid) {
			$pick_result = hk_db_query("SELECT * FROM picks WHERE user_id=\"{$user_row['id']}\" && game_id=\"$gid\"",$db_link);
			$pick_row = mysql_fetch_assoc($pick_result);
			if (mysql_num_rows($pick_result) > 0) {
				$id = $pick_row['id'];
				$pick_ids[] = $id;
				$away_input = "<input type=\"text\" name=\"away_$id\" value=\"{$pick_row['away_score']}\" size=\"3\" />";
				$home_input = "<input type=\"text\" name=\"home_$id\" value=\"{$pick_row['home_score']}\" size=\"3\" />";
			} else {
				$away_input = "";
				$home_input = "";
			}
			echo "<td class=\"center_tbl\">$away_input</td>\n<td class=\"center_trb\">$home_input</td>\n";
		}
		echo "</tr>\n";
	}
	$idstr = implode(",",$pick_ids);
	echo <<<CODE
		</table><br />
		<input type="checkbox" name="zero" />Zero empty picks<br />
		<input type="hidden" name="pickweek" value="$iweek" />
		<input type="hidden" name="ids" value="$idstr" />
		<input type="submit" name="submitter" value="Edit Picks" size="5" maxlength="5" />
		</form>
CODE;
?>
