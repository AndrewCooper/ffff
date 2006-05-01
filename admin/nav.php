<?php
session_start();
include('../functions.php');
include('head.html');
$ud = $_SESSION['ffff_user_data'];
$thisweek = hk_get_week();
$_SESSION['thisweek'] = $thisweek;

echo <<<CODE
<div style="margin:0px auto; text-align:left;">
<img src="../images/hkc_logo.gif" /><br />
<div style="font-size:smaller;text-align:center;"><a href="../toc.php" target="_top">[Remove Administration Frame]</a></div><br />
<div style="font-size:smaller;text-align:center;"><a href="../toc.php" target="main">[Show Table of Contents]</a></div><br />
<span style="font-size:larger;">Player Administration:</span>
<ul>
<li><a href="user.php">Create New User</a></li>
<li><a href="user.php?a=e">Edit Current User</a></li>
<li>Change user to: \n<form action="../login.php?a=su" method="post" target="_top">\n<select name="newid">
CODE;
$db = hk_db_connect();
$ures= $db->query("SELECT firstname,lastname,id FROM users ORDER BY lastname,firstname");
while($urow=$ures->fetch_assoc()) {
echo "<option value=\"{$urow['id']}\" ";
if ($_SESSION['ffff_user_data']['id'] == $urow['id']) {
	echo "selected=\"selected\"";
}
echo ">{$urow['firstname']}&nbsp;{$urow['lastname']}</option>\n";
}
echo <<<CODE
</select>\n<br /><input type="submit" name="submitter" value="Change" /></li>
</form>
<li><a href="user.php?a=del">Delete a User</a></li>
</ul>

<span style="font-size:larger;">Game Administration:</span>
<ul>
<li><a href="teams.php">Edit Teams</a></li>
<li><a href="games.php">Edit Games</a></li>
<li><a href="bowls.php">Edit Bowls</a></li>
<li><a href="picks.php">Batch Edit Picks</a></li>
<li><a href="calc_scores.php">Calculate Scores</a></li>
<li><a href="rankings.php">Update Rankings</a></li>
</ul>
CODE;
include('foot.html');
?>
