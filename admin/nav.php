<?php
	echo <<<CODE
	<div style="margin:0px auto; text-align:left;">
	<img src="../images/hkc_logo.gif" /><br />
	<span style="font-size:larger;">Player Administration:</span>
		<ul>
		<li><a href="admin.php?action=edit_user">Create New Player</a></li>
		<li><a href="admin.php?action=edit_user">Edit Player</a></li>
		<li>Change user to: \n<form action="admin.php?action=change_user" method="post">\n<select name="new_user">
CODE;
		$user_query = "SELECT firstname,lastname,id FROM participants";
		$user_result = mysql_query($user_query,$db_link);
		while($user_row = mysql_fetch_assoc($user_result)) {
			echo "<option value=\"{$user_row['id']}\" ";
			if ($_SESSION['login_id'] == $user_row['id']) {
				echo "selected=\"selected\"";
			}
			echo ">{$user_row['firstname']}&nbsp;{$user_row['lastname']}</option>\n";
		}
		echo <<<CODE
		</select>\n<br /><input type="submit" name="submitter" value="Change" />\n</form>\n</li>
		</ul>

	<span style="font-size:larger;">Game Administration:</span>
		<ul>
		<li><a href="admin.php?page=teams" target="main">Edit Teams</a></li>
		<li><a href="admin.php?page=games" target="main">Edit Games</a></li>
		<li><a href="admin.php?page=bowls" target="main">Edit Bowls</a></li>
		<li><a href="admin.php?page=picks" target="main">Batch Edit Picks</a></li>
		<li style="white-space:nowrap;">Calculate Scores
			<form action="admin.php?page=calc_scores" method="post" target="main">
			From Week:<input type="text" name="start" size="3" value="1" />\n<br />To Week:<input type="text" name="end" size="3" value="{$_SESSION['week_id']}" />\n<br /><input type="submit" name="submitter" value="Calculate" />
			</form>
		</li>
		</ul>
CODE;
?>