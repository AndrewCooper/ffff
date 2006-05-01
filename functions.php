<?php
function hk_current_week() {
	global $db_link;
	$today = time();
	$week_query = "SELECT id FROM weeks WHERE UNIX_TIMESTAMP(start)<=$today && UNIX_TIMESTAMP(end)>=$today";
	$week_res = hk_db_query($week_query,$db_link);
	if (mysql_num_rows($week_res) != 0) {
		$week_row = mysql_fetch_assoc($week_res);
		return $week_row['id'];
	} else {
		return null;
	}
}

function hk_week_list($asc=true) {
	echo "<select name=\"week\">\n";
	$weekr = hk_db_query("SELECT id,postseason FROM weeks ORDER BY id".($asc?"":" DESC"),$db_link);
	while ($week_row = mysql_fetch_assoc($weekr)) {
		echo "\t<option value=\"{$week_row['id']}\">".hk_week_name($week_row['id'])."</option>\n";
	}
	echo "</select>\n";
}

function hk_week_name($week_id) {
	global $db_link;
	$week_query = "SELECT id,postseason FROM weeks WHERE id = \"$week_id\"";
	$week_row = mysql_fetch_assoc(hk_db_query($week_query,$db_link));
	if ($week_row['postseason'] == "0") {
		$week_name = "Week {$week_row['id']}";
		$next_week = "Week $nweek_id";
	} else {
		$ps_wid = $week_row['postseason'];
		$week_name = "Bowl Season";
		$ps_wid++;
		$next_week = "End of Game";
		unset($ps_wid);
	}
	return $week_name;
}

function hk_db_query($query,$link) {
	if ($result = mysql_query($query,$link)) {
		return $result;
	} else {
		echo "<div>".$query."<br />".mysql_error($link)."</div>";
		return $result;
	}
}

function hk_debug($str) {
	echo "<div>****DEBUG**** ".$str."</div>\n";
}

function log_out() {
	if (!setcookie("ffff_auto_login","",time()-604800)) {
		echo "Error deleting cookie";
	} else {
		unset($_SESSION['login_id']);
		unset($_SESSION['is_admin']);
		unset($_SESSION['full_name']);
		unset($_SESSION['week_id']);
		header("Location: http://".$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF'])."/index.php");
	}
}

function log_in() {
	if (isset($_COOKIE['ffff_auto_login']) && !isset($_SESSION['login_id'])) {
		if ($_COOKIE['ffff_auto_login'] != "deleted") {
			$db_link = mysql_connect("localhost","hkc","hkc");
			mysql_select_db("ffff");
			$cookievars = explode(":",$_COOKIE['ffff_auto_login']);
			$_SESSION['login_id'] = $cookievars[0];
			$_SESSION['is_admin'] = $cookievars[1];
			$_SESSION['full_name'] = $cookievars[2];
			$today = time();
			$week_query = "SELECT id FROM weeks WHERE UNIX_TIMESTAMP(start)<=$today && UNIX_TIMESTAMP(end)>=$today";
			$week_result = mysql_query($week_query,$db_link);
			$week_row = mysql_fetch_assoc($week_result);
			$_SESSION['week_id'] = $week_row['id'];
			if ($_SERVER['PHP_SELF'] == dirname($_SERVER['PHP_SELF'])."/index.php") {
				$file = dirname($_SERVER['PHP_SELF'])."/toc.php";
				header("Location: http://".$_SERVER['HTTP_HOST'].$file);
			}
		}
	}
}

function num_suffix($num) {
	if ($place == 11 || $place == 12 || $place == 13) {
		$suf = "th";
	} else if ($place % 10 == 1) {
		$suf = "st";
	} else if ($place % 10 == 2) {
		$suf = "nd";
	} else if ($place % 10 == 3) {
		$suf = "rd";
	} else {
		$suf = "th";
	}
	return $suf;
}

function print_table($data,$table="<table border=\"1\" cellspacing=\"0\">",$params=null) {
	echo $table."\n";
	foreach($data as $rowid => $row) {
		echo "<tr>\n";
		foreach($row as $colid => $col) {
			echo "<td";
			if (isset($params)) {
				echo " ".$params[$rowid][$colid];
			}
			echo ">".$data[$rowid][$colid]."</td>\n";
		}
		echo "</tr>\n";	
	}
	echo "</table>\n";
}
?>
