<?php
	echo "\n<!-- BEGIN EDIT GAMES CODE -->\n";
	global $db_link;
	echo "<h1>Edit Games</h1>";
	$hk_week = $_SESSION['week_id']+1;
	$game_day = date("Y-m-d");
	if ($_POST['submitter'] == "Edit Game") {
		$game_ids = explode(",",$_POST['ids']);
		foreach($game_ids as $id) {
			$home = $_POST["home_".$id];
			$away = $_POST["away_".$id];
			$home_score = $_POST["home_score_".$id];
			$away_score = $_POST["away_score_".$id];
			$gametime = $_POST["gametime_".$id];
			$week_id = $_POST["week_id_".$id];
			$edit_query = "UPDATE games SET home=\"$home\",away=\"$away\",home_score=\"$home_score\",away_score=\"$away_score\",gametime=\"$gametime\",week_id=\"$week_id\" WHERE id=\"$id\"";
			hk_db_query($edit_query,$db_link);
			//echo $edit_query."<br />\n";
		}
	} else if ($_POST['submitter'] == "Create Game") {
		echo "create game";
		$game_query = "INSERT INTO games (week_id,gametime,away,away_score,home,home_score) VALUES (\"".$_POST['week_id']."\",\"".$_POST['gametime']."\",\"".$_POST['away']."\",\"".$_POST['away_score']."\",\"".$_POST['home']."\",\"".$_POST['home_score']."\")";
		hk_db_query($game_query,$db_link);
		$game_day = $_POST['gametime'];
	}
	
	// New Game Creation
	echo "<table border=\"1\" cellspacing=\"0\" cellpadding=\"3\">";
	echo "<thead><h3>Add A Game:</h3></thead>";
	echo "<tr><td>Week</td><td>Date</td><td>Away Team</td><td>Score</td><td>@</td><td>Home Team</td><td>Score</td><td>Submit</td></tr>";
	echo <<<CODE
	<tr>
	<form action="admin.php?page=games" method="post">
	<td><input type="text" name="week_id" value="$hk_week" size="5" /></td>
	<td><input type="text" name="gametime" value="$game_day" size="10" /></td>
	<td><select name="away">
CODE;
	$team_query = "SELECT id,name FROM teams ORDER BY name";
	$team_result = mysql_query($team_query,$db_link);
	echo mysql_error($db_link);
	while($team_row = mysql_fetch_assoc($team_result)) {
		echo "<option value=\"{$team_row['id']}\" ";
		echo ">{$team_row['name']}</option>";
	}
	echo <<<CODE
	</select></td>
	<td><input type="text" name="away_score" value="0" size="5" /></td>
	<td>@</td>
	<td><select name="home">
CODE;
	$team_query = "SELECT id,name FROM teams ORDER BY name";
	$team_result = mysql_query($team_query,$db_link);
	echo mysql_error($db_link);
	while($team_row = mysql_fetch_assoc($team_result)) {
		echo "<option value=\"{$team_row['id']}\">{$team_row['name']}</option>\n";
	}
	echo <<<CODE
	</select></td>
	<td><input type="text" name="home_score" value="0" size="5" /></td>
	<td><input type="submit" name="submitter" value="Create Game" />
	</form>
	</tr>
CODE;
	echo "</table>";

	// Edit Games
	if (isset($_GET['week'])) {
		$iweek = $_GET['week'];
		$weekStr = "week=".$_GET['week'];
	} else {
		$iweek = $hk_week;
		$weekStr = "";
	}
	echo "<h3>Edit A Game:</h3>";
	echo <<<CODE
		<div style="text-align:left;">
		<form action="admin.php?$weekStr" method="get">
			Edit Games for: 
			<input type="hidden" name="page" value="games" />
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
		<form action="admin.php?page=games&$weekStr" method="post">
		<table border="1" cellspacing="0" cellpadding="3" style="width:100%;">
		<thead><tr><td>Week</td><td>Date</td><td>Away Team</td><td>Score</td><td>@</td><td>Home Team</td><td>Score</td></tr></thead>
CODE;
	$games_query = "SELECT * FROM games WHERE week_id=\"$iweek\" ORDER BY id";
	$games_result = mysql_query($games_query,$db_link);
	$game_ids = array();
	while($games_row = mysql_fetch_assoc($games_result)) {
		$id = $games_row[id];
		$game_ids[] = $id;
		echo <<<CODE
			<tr>
			<td><input type="text" name="week_id_$id" value="{$games_row['week_id']}" size="5" /></td>
			<td><input type="text" name="gametime_$id" value="{$games_row['gametime']}" size="10" /></td>
			<td><select name="away_$id">
CODE;
			$team_query = "SELECT id,name FROM teams ORDER BY name";
			$team_result = mysql_query($team_query,$db_link);
			echo mysql_error($db_link);
			while($team_row = mysql_fetch_assoc($team_result)) {
				echo "<option value=\"{$team_row['id']}\" ";
				if ($team_row['id'] == $games_row['away']) {
					echo "selected=\"selected\"";
				}
				echo ">{$team_row['name']}</option>\n";
			}
			echo <<<CODE
			</select></td>
			<td><input type="text" name="away_score_$id" value="{$games_row['away_score']}" size="5" /></td>
			<td>@</td>
			<td><select name="home_$id">
CODE;
			$team_query = "SELECT id,name FROM teams ORDER BY name";
			$team_result = mysql_query($team_query,$db_link);
			echo mysql_error($db_link);
			while($team_row = mysql_fetch_assoc($team_result)) {
				echo "<option value=\"{$team_row['id']}\" ";
				if ($team_row['id'] == $games_row['home']) {
					echo "selected=\"selected\"";
				}
				echo ">{$team_row['name']}</option>\n";
			}
			echo <<<CODE
			</select></td>
			<td><input type="text" name="home_score_$id" value="{$games_row['home_score']}" size="5" /></td>
			</tr>
CODE;
	}
	$idstr = implode(",",$game_ids);
	echo <<<CODE
		</table>
		<input type="hidden" name="ids" value="$idstr" />
		<input type="submit" name="submitter" value="Edit Game" />
		</form>
CODE;
?>
