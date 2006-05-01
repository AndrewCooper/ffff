<?php
	if ($_GET['page'] == "change_user") {
		CHANGE_USER();
	}
	if (isset($_GET['page'])) {
		include("head.php");
		if ($_GET['page'] == 'nav') {
			include("admin/nav.php");
		} else if ($_GET['page'] == 'picks') {
			include("admin/picks.php");
		} else if ($_GET['page'] == 'user') {
			include("admin/user.php");
		} else if ($_GET['page'] == 'teams') {
			include("admin/teams.php");
		} else if ($_GET['page'] == 'games') {
			include("admin/games.php");
		} else if ($_GET['page'] == 'bowls') {
			include("admin/bowls.php");
		} else if ($_GET['page'] == 'calc_scores') {
			include("admin/calc_scores.php");
		} else if ($_GET['page'] == 'calc_scores_new') {
			include("admin/calc_scores_new.php");
		}
		include("foot.php");
	} else {
		include("adminfset.php");
	}		
	
function CHANGE_USER() {
	session_start();
	$_SESSION['login_id'] = $_POST['new_user'];
	$db_link = mysql_connect("localhost","hkc","hkc");
	mysql_select_db("ffff");
	$user_query = "SELECT firstname,lastname FROM participants WHERE id=\"{$_SESSION['login_id']}\"";
	$user_result = mysql_query($user_query,$db_link);
	$user_row = mysql_fetch_assoc($user_result);
	$_SESSION['full_name'] = $user_row['firstname']." ".$user_row['lastname'];
	header("Location: http://".$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF'])."/toc.php?".strip_tags(SID));
}
?>
