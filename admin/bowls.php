<?php
	echo "\n<!-- BEGIN EDIT BOWL GAMES CODE -->\n";
	global $db_link;
	echo "<h1>Edit Bowl Games</h1>";
	$hk_week = $_SESSION['week_id']+1;
	if ($_POST['submitter'] == "Edit Bowls") {
		$bowl_ids = explode(",",$_POST['ids']);
		foreach($bowl_ids as $id) {
			$name = $_POST["name_".$id];
			$location = $_POST["location_".$id];
			$description = $_POST["description_".$id];
			$game_id = $_POST["game_".$id];
			$multiplier = $_POST["multiplier_".$id];
			$edit_query = "UPDATE bowls SET name=\"$name\",location=\"$location\",description=\"$description\",game_id=\"$game_id\" WHERE id=\"$id\"";
			hk_db_query($edit_query,$db_link);
			//echo "<div>$edit_query</div><br />\n";
		}
	} else if ($_POST['submitter'] == "Create Bowl") {
		$bowl_query = "INSERT INTO bowls (name,location,description,game_id,multiplier) VALUES (\"{$_POST['name']}\",\"{$_POST['location']}\",\"{$_POST['description']}\",\"{$_POST['game']}\",\"{$_POST['multiplier']}\")";
		hk_db_query($bowl_query,$db_link);
		echo "<div>$bowl_query</div><br />\n";
	}
	
	// New Bowl Creation
	echo <<<CODE
	<form action="admin.php?page=bowls" method="post">
	<table border="1" cellspacing="0" cellpadding="3">
	<thead><h3>Add A Bowl Game:</h3></thead>
	<tr><td>Bowl Name</td><td>Bowl Location</td><td>Bowl Description</td><td>Game</td><td>Score Multiplier</td></tr>
	<tr>
	<td><textarea name="name" rows="4" cols="16"></textarea></td>
	<td><textarea name="location" rows="4" cols="16"></textarea></td>
	<td><textarea name="description" rows="4" cols="16"></textarea></td>
	<td><select name="game">\n
CODE;
	$week_result = hk_db_query("SELECT id,postseason FROM weeks WHERE postseason != 0 ORDER BY postseason DESC",$db_link);
	while ($week_r = mysql_fetch_assoc($week_result)) {
		echo "\t\t<optgroup label=\"Bowl Week {$week_r['postseason']}\">\n";
		$game_result = hk_db_query("SELECT id,week_id,home,away FROM games WHERE week_id = \"{$week_r['id']}\" ORDER BY id",$db_link);
		while ($game_r = mysql_fetch_assoc($game_result)) {
			$home_r = mysql_fetch_assoc(hk_db_query("SELECT name FROM teams WHERE id=\"{$game_r['home']}\"",$db_link));
			$away_r = mysql_fetch_assoc(hk_db_query("SELECT name FROM teams WHERE id=\"{$game_r['away']}\"",$db_link));
			echo "\t\t\t<option value=\"{$game_r['id']}\">{$away_r['name']} vs. {$home_r['name']}</option>\n";
		}
		echo "\t\t</optgroup>\n";
	}

	echo <<<CODE
	</select></td>
	<td><input type="textbox" name="multiplier" size="5" value="2" /></td>
	</tr>
	</table>
	<input type="submit" name="submitter" value="Create Bowl" />
	</form>
CODE;

	// Edit Bowls
	if (isset($_GET['week'])) {
		$iweek = $_GET['week'];
		$weekStr = "week=".$_GET['week'];
	} else {
		$iweek = $hk_week;
		$weekStr = "";
	}
	echo "<h3>Edit A Bowl Game:</h3>";
	echo <<<CODE
	<form action="admin.php?action=edit_bowls" method="post">
	<table border="1" cellspacing="0" cellpadding="3">
	<tr><td>Bowl Name</td><td>Bowl Location</td><td>Bowl Description</td><td>Game</td><td>Score Multiplier</td></tr>
CODE;
	$bowls_result = hk_db_query("SELECT * FROM bowls ORDER BY id",$db_link);
	$bowl_ids = array();
	while($bowl_row = mysql_fetch_assoc($bowls_result)) {
		$id = $bowl_row['id'];
		$bowl_ids[] = $id;
		echo <<<CODE
		<tr>
		<td><textarea name="name_$id" rows="4" cols="16">{$bowl_row['name']}</textarea></td>
		<td><textarea name="location_$id" rows="4" cols="16">{$bowl_row['location']}</textarea></td>
		<td><textarea name="description_$id" rows="4" cols="16">{$bowl_row['description']}</textarea></td>
		<td><select name="game_$id" >\n
CODE;
		$week_result = hk_db_query("SELECT id,postseason FROM weeks WHERE postseason != 0 ORDER BY postseason DESC",$db_link);
		while ($week_r = mysql_fetch_assoc($week_result)) {
			echo "\t\t<optgroup label=\"Bowl Week {$week_r['postseason']}\">\n";
			$game_result = hk_db_query("SELECT id,week_id,home,away FROM games WHERE week_id = \"{$week_r['id']}\" ORDER BY id",$db_link);
			while ($game_r = mysql_fetch_assoc($game_result)) {
				$home_r = mysql_fetch_assoc(hk_db_query("SELECT name FROM teams WHERE id=\"{$game_r['home']}\"",$db_link));
				$away_r = mysql_fetch_assoc(hk_db_query("SELECT name FROM teams WHERE id=\"{$game_r['away']}\"",$db_link));
				if ($bowl_row['game_id'] == $game_r['id']) {
					$sel = "selected=\"selected\"";
				} else {
					$sel = "\"\"";
				}
				echo "\t\t\t<option value=\"{$game_r['id']}\" $sel>{$away_r['name']} vs. {$home_r['name']}</option>\n";
			}
			echo "\t\t</optgroup>\n";
		}
		echo <<<CODE
		</select></td>
		<td><input name="multiplier_$id" type="textbox" size="5" value="{$bowl_row['multiplier']}" /></td></tr>
CODE;
	}
	$idstr = implode(",",$bowl_ids);
	echo <<<CODE
	</table>
	<input type="hidden" name="ids" value="$idstr" />
	<input type="submit" name="submitter" value="Edit Bowls" />
	</form>
CODE;

?>
