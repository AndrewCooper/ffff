<?php
	echo "\n<!-- BEGIN EDIT TEAMS CODE -->\n";
	global $db_link;
	if ($_POST['submitter'] == "Add Team") {
		$tq = "INSERT INTO teams (name,image,location,conference,rank,record) VALUES (\"{$_POST['team_name']}\",\"{$_POST['team_image']}\",\"{$_POST['team_location']}\",\"{$_POST['team_conference']}\",\"{$_POST['team_rank']}\",\"{$_POST['team_record']}\")";
		hk_db_query($tq,$db_link);
	} else if ($_POST['submitter'] == "Edit Teams") {
		$ids = explode(",",$_POST['ids']);
		foreach($ids as $id) {
			$name = $_POST['name_'.$id];
			$image = $_POST['image_'.$id];
			$location = $_POST['location_'.$id];
			$conference = $_POST['conference_'.$id];
			$rank = $_POST['rank_'.$id];
			$record = $_POST['record_'.$id];
			$edit_query = "UPDATE teams SET name=\"$name\",image=\"$image\",location=\"$location\",conference=\"$conference\",rank=\"$rank\",record=\"$record\" WHERE id=\"$id\"";
			hk_db_query($edit_query,$db_link);
			//echo $edit_query."<br />\n";
		}
	}
	
	//CREATE NEW TEAM
	echo <<<CODE
		<h3>Add New Team</h3>
		<form action="admin.php?action=edit_teams" method="post">
		<div class="databox" style="white-space:nowrap;">
		Name:<input type="text" name="team_name" size="12" maxlength="255" />&nbsp;
		Image:<input type="text" name="team_image" size="12" maxlength="255" />&nbsp;
		Location:<input type="text" name="team_location" size="12" maxlength="255" />
		Conference:<input type="text" name="team_conference" size="12" maxlength="255" />
		Rank:<input type="text" name="team_rank" size="3" maxlength="3" />
		Record:<input type="text" name="team_record" size="5" maxlength="10" />
		<br /><input type="submit" name="submitter" value="Add Team" />
		</div>
		</form>
CODE;

	//EDIT TEAMS
	$team_query = "SELECT * FROM teams ORDER BY name";
	$team_result = hk_db_query($team_query,$db_link);
	echo <<<CODE
		<h3>Edit All Teams</h3>
		<form action="admin.php?action=edit_teams" method="post">
		<table border="1" cellspacing="0" cellpadding="5" align="center" style="width:100%">
		<thead><tr><td>Name</td><td>Image</td><td>Location</td><td>Conference</td><td>Rank</td><td>Record</td></tr></thead>
CODE;
	$team_ids = array();
	while ($team_row = mysql_fetch_assoc($team_result)) {
		$id = $team_row['id'];
		$team_ids[] = $id;
		echo <<<CODE
			<tr style="text-align:center; vertical-align:top;">
			<td><input type="text" name="name_$id" value="{$team_row['name']}" size="12" maxlength="255" /></td>
			<td><input type="text" name="image_$id" value="{$team_row['image']}" size="12" maxlength="255" /><br /><img src="logos/{$team_row['image']}" /></td>
			<td><input type="text" name="location_$id" value="{$team_row['location']}" size="12" maxlength="255" /></td>
			<td><input type="text" name="conference_$id" value="{$team_row['conference']}" size="12" maxlength="255" /></td>
			<td><input type="text" name="rank_$id" value="{$team_row['rank']}" size="3" maxlength="3" /></td>
			<td><input type="text" name="record_$id" value="{$team_row['record']}" size="5" maxlength="10" /></td>
			</tr>
CODE;
		if (count($team_ids)%5 == 0) {
			echo "<tr><td colspan=\"4\" align=\"right\"><input type=\"submit\" name=\"submitter\" value=\"Edit Teams\" /></td></tr>";
		}
	}
	$idstr = implode(",",$team_ids);
	echo <<<CODE
	</table>
	<input type="hidden" name="ids" value="$idstr" />
	<input type="submit" name="submitter" value="Edit Teams" />
	</form>
CODE;
?>
