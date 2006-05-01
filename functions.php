<?php
require('config.inc.php');
require('func_get_week.php');

function hk_db_connect() {
	global $hk_config;
	return new mysqli($hk_config['mysql_server'],$hk_config['mysql_user'],$hk_config['mysql_pass'],$hk_config['database']);
}
function hk_db_connect_admin() {
	global $hk_config;
	return new mysqli($hk_config['mysql_server'],$hk_config['mysql_admin_user'],$hk_config['mysql_admin_pass'],$hk_config['database']);
}

function hk_redir_rel($page) {
	$dir=dirname($_SERVER['PHP_SELF']);
	if ($dir=="/") {
		$dir="";
	}
	header("Location: http://".$_SERVER['HTTP_HOST'].$dir."/".$page);
}

function hk_setcookie($name,$value) {
	if(!setcookie($name,$value,time()+60*60*24*7,'/')) {
		echo "setting cookie failed";
	}
}
function hk_delcookie($name) {
	setcookie($name,"",time()-3600,'/');
}
function hk_check_status() {
	if (isset($_SESSION['error'])) {
		echo "<div style=\"color:red; text-algin:center;\">\n{$_SESSION['error']}\n</div><br />\n";
		unset($_SESSION['error']);
	}
	if (isset($_SESSION['message'])) {
		echo "<div style=\"color:green; text-algin:center;\">\n{$_SESSION['message']}\n</div><br />\n";
		unset($_SESSION['message']);
	}
}
function hk_week_name($week_data) {
	//requires $week_data to be an array with at least id and postseason columns
	if ($week_data['postseason'] != 0) {
		$name = "Bowl Week ".$week_data['postseason'];
	} else {
		$name = "Week ".$week_data['id'];
	}
	return $name;
}
function hk_num_suffix($number) {
	$driver = substr("$number", -1);
	switch($driver) {
	case '1':
		$suffix = "st";
		break;
	case '2':
		$suffix = "nd";
		break;
	case '3':
		$suffix = "rd";
		break;
	default:
		$suffix = "th";
		break;
	}
	return $suffix;
}
function hk_print_r($var) {
	echo "<pre style=\"font-size:normal; text-align:left;\">\n";
	print_r($var);
	echo "</pre>\n";
}
function echoln ($str) {
	echo $str."<br />\n";
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
function get_week_combobox($weekid=1,$name="week") {
	$result="<select name=\"$name\">\n";
	$db=hk_db_connect();
	$res=$db->query("SELECT id,postseason FROM weeks ORDER BY id");
	while ($row=$res->fetch_assoc()) {
		$wname=hk_week_name($row);
		if ($weekid==$row['id']) {
			$selected="selected=\"selected\"";
		} else {
			$selected="";
		}
		$result.="<option value=\"{$row['id']}\" $selected >$wname</option>\n";
	}
	$result.="</select>\n";
	return $result;
}
function get_team_combobox($name="team",$def=NULL) {
	$result="<select name=\"$name\">\n";
	$db=hk_db_connect();
	$res=$db->query("SELECT id,name FROM teams ORDER BY name");
	while ($row=$res->fetch_assoc()) {
		if ($def==NULL) {
			$def=$row['id'];
		}
		if ($def==$row['id']) {
			$selected="selected=\"selected\"";
		} else {
			$selected="";
		}
		$result.="<option value=\"{$row['id']}\" $selected >{$row['name']}</option>\n";
	}
	$result.="</select>\n";
	return $result;
}
function get_game_combobox($def=NULL,$name="game") {
	$result="<select name=\"$name\">\n";
	if ($def==NULL) {
		$selected="selected=\"selected\"";
	}
	$result.="<option value=\"-1\" $selected >No Game.</option>\n";
	$db=hk_db_connect();
	$res=$db->query("SELECT games.id, away.name AS aname, home.name AS hname FROM games LEFT JOIN teams AS away ON (away.id=games.away) LEFT JOIN teams AS home ON (home.id=games.home) WHERE games.week= ANY (SELECT id FROM weeks WHERE postseason!=0) ORDER BY games.gametime");
	echo $db->error;
	while ($row=$res->fetch_assoc()) {
		if ($def==NULL) {
			$def=$row['id'];
		}
		if ($def==$row['id']) {
			$selected="selected=\"selected\"";
		} else {
			$selected="";
		}
		$result.="<option value=\"{$row['id']}\" $selected >{$row['aname']} @ {$row['hname']}</option>\n";
	}
	$result.="</select>\n";
	return $result;
}
?>
