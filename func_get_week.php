<?php
//determines the current week and its info
//returns an array containing that info
// -- can also get data for a specific week if passed a week value.
// -- otherwise the current week's data is found

function hk_get_week($num=NULL) {
	$now = time();
	$db = hk_db_connect();
	if ($num != NULL) {
		$weeks = $db->query("SELECT * FROM weeks WHERE id=$num");
	} else {	
		$weeks = $db->query("SELECT * FROM weeks WHERE start<$now AND end>$now");
	}
	
	if (($thisweek = $weeks->fetch_assoc()) == NULL) {
		$weeks->close();
		$weeks = $db->query("SELECT * FROM weeks WHERE id=1");
		$tmp = $weeks->fetch_assoc();
		if ($tmp['start'] > $now) {
			$thisweek = array('id'=>NULL,'name' => "Preseason", 'next'=>'1', 'nextname'=>'Week 1', 'start'=>NULL, 'end'=>$tmp['start']-1);
		} else {
			$thisweek = array('id'=>NULL,'name' => "Postseason", 'next'=>NULL, 'nextname'=>NULL, 'start'=>NULL, 'end'=>NULL);
		}
	} else {
		$weeks->close();

		//figure out the name of the week
		$thisweek['name'] = hk_week_name($thisweek);

		//figure out next week's id, or NULL if last week
		$weeks=$db->query("SELECT * FROM weeks ORDER BY id DESC LIMIT 1");
		$tmp = $weeks->fetch_assoc();
		if ($thisweek['id'] == $tmp['id']) {
			$thisweek['next'] = NULL;
		} else {
			$thisweek['next'] = $thisweek['id'] + 1;
		}
		$weeks->close();

		//figure out next week's name
		if ($thisweek['next'] != NULL) {
			$weeks = $db->query("SELECT * FROM weeks WHERE id=".$thisweek['next']);
			$tmp = $weeks->fetch_assoc();
			$thisweek['nextname'] = hk_week_name($tmp);
			$weeks->close();
		} else {
			$thisweek['nextname'] = "Postseason";
		}
	}
	if ($thisweek['start'] != NULL) {
		$thisweek['startstr'] = date("h:i:sa",$thisweek['start'])." on ".date("D, M jS, Y",$thisweek['start']);
	}
	if ($thisweek['end'] != NULL) {
		$thisweek['endstr'] = date("h:i:sa",$thisweek['end'])." on ".date("D, M jS, Y",$thisweek['end']);
	}
	return $thisweek;
}
?>
