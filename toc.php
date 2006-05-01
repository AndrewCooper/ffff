<?php
	include("head.php");

	$week_id = hk_current_week();
	$nweek_id = $week_id+1;
	$_SESSION['week_id'] = $week_id;
	
	echo "<img src=\"images/logo.gif\" />\n";
	echo "<h2>".hk_week_name($week_id)."</h2>\n";
	echo "<h3>Welcome, {$_SESSION['full_name']}</h3>\n";
	
	$userr = mysql_fetch_assoc(hk_db_query("SELECT score FROM participants WHERE id=\"{$_SESSION['login_id']}\"",$db_link));
	$users = $userr['score'];
	
	$userr = hk_db_query("SELECT score FROM participants WHERE score=\"$users\"",$db_link);
	if (mysql_num_rows($userr) > 1) {
		$tie = "tied with ".(mysql_num_rows($userr)-1)." players for ";
	} else {
		$tie = "alone in ";
	}
	
	$userr = hk_db_query("SELECT score FROM participants ORDER BY score DESC",$db_link);
	$i = 1;
	while ($user_row = mysql_fetch_assoc($userr)) {
		if ($user_row['score'] == $users) {
			$place = $i;
			break;
		}
		$i++;
	}
	$suf = num_suffix($place);
	
	echo "You are ".$tie.$place.$suf." place, with a score of $users.<br />\n";
	echo "Today is ".date("l, F j, Y")."<br />\n";

	$next_week = hk_week_name($nweek_id);
echo <<<CODE
	<h3>User Options</h3>
	<div style="text-align:left; margin:0px auto 0px 30%;">
	<ul>
		<li><a href="picks.php?week=$nweek_id">Make/Edit Picks for $next_week</a></li>
		<li>View other weeks:<br /><div style="margin-right:10px;">
		<form action="picks.php?action=change_user" method="get">
hk_week_list(false);
			<input type="submit" value="View" />
		</form>
		</div>
		</li>
		<li><a href="user.php">Edit User Preferences</a></li>
CODE;
	if ($_SESSION['is_admin'] == 1) {
		echo "<li><a href=\"admin.php\">Administration</a></li>";
	}
	echo "</ul></div>";
	echo "<a href=\"reports.php\"><h3>Weekly Game Reports</h3></a>";
	echo "<a href=\"scoreReport.php\"><h3>Cumulative Score Reports</h3></a>";
	global $db_link;
	echo <<<CODE
		<h3>Current Standings</h3>
		<table align="center" border="0" cellspacing="0" cellpadding="2">
		<tr style="white-space:nowrap;"><td></td><td class="center_trbl">Wins&nbsp;Picked</td><td class="center_trbl">7&nbsp;of&nbsp;7's</td><td class="center_trbl">Closest&nbsp;Picked&nbsp;Games</td><td class="center_trbl">Perfect&nbsp;Pick&nbsp;Games</td><td class="center_trbl">Total&nbsp;Score</td></tr>\n
CODE;
	$score_query = "SELECT * FROM participants ORDER BY score DESC";
	$score_result = mysql_query($score_query,$db_link);
	while($score_row = mysql_fetch_assoc($score_result)) {
		echo "<tr><td class=\"right_trbl\">".$score_row['firstname']." ".$score_row['lastname']."</td>";
		echo "<td class=\"center_trbl\">".$score_row['wins']."</td>";
		echo "<td class=\"center_trbl\">".$score_row['sevens']."</td>";
		echo "<td class=\"center_trbl\">".$score_row['closest']."</td>";
		echo "<td class=\"center_trbl\">".$score_row['exact']."</td>";
		echo "<td class=\"center_trbl\">".$score_row['score']."</td></tr>\n";
	}
	echo "</table>\n";
	include("foot.php");
	
?>
