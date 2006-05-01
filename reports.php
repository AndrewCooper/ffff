<?php
session_start();
include('functions.php');
if(!isset($_SESSION['ffff_user_data'])) {
	hk_redir_rel('index.php');
} else {
	include('head.html');
	$db=hk_db_connect();
	$thisweek=hk_get_week($_GET['week']);
	if ($_GET['show']=='all') {
		$sweek=hk_get_week(1);
		$res=$db->query("SELECT * FROM weeks ORDER BY id DESC LIMIT 1");
		$row=$res->fetch_assoc();
		$eweek=hk_get_week($row['id']);
		$res->close();
		$vchk['all']="checked=\"checked\" ";
		$vchk['title']=$sweek['name']." to ".$eweek['name'];
	} elseif ($_GET['show']=='range') {
		$eweek=hk_get_week($_GET['rew']);
		$sweek=hk_get_week($_GET['rsw']);
		$vchk['range']="checked=\"checked\" ";
		$vchk['title']=$sweek['name']." to ".$eweek['name'];
	} elseif ($_GET['show']=='single') {
		$eweek=$sweek=hk_get_week($_GET['ssw']);
		$vchk['single']="checked=\"checked\" ";
		$vchk['title']=$sweek['name'];
	} else {
		$eweek=$sweek=$_SESSION['thisweek'];
		if($eweek['id']==NULL) {
			$eweek=$sweek=hk_get_week(1);
		}
	}
	
	$res=$db->query("SELECT id,postseason FROM weeks ORDER BY id");
	$pulldown="";
	while (($row=$res->fetch_assoc())!=NULL) {
		if ($row['id']==$sweek['id']) {
			$s=" selected=\"selected\""; 
		} else {
			$s="";
		}
		$pulldown.="<option value=\"{$row['id']}\" $s>".hk_week_name($row)."</option>";
	}
	echo <<<CODE
	<script language="JavaScript">
	function select_radio(i) {
		document.viewForm.show[i].checked=true;
	}
	</script>
	<div class="head5" style="text-align:right;"><a href="admin.php">[Administration]</a></div>
	<img src="images/logo.gif" /><br />
	<div style="font-size:smaller;"><a href="toc.php">[Back to Table of Contents]</a></div><br />
	<div class="head1"> Pick Reports<br /><span class="head2">{$vchk['title']}</span></div>
	<form action="reports.php" method="get" id="viewForm" name="viewForm">
	<table border='0' cellspacing='0' cellpadding='2' style="margin:auto;">
	<tr><td rowspan="3">View:</td>
	<td style="text-align:right;" class="tbl">All:<input type="radio" name="show" value="all" {$vchk['all']}/></td>
	<td class="trb"></td>
	<td rowspan="3" class="trb"><input type="submit" value="Change View" /></td></tr>
	<tr><td style="text-align:right;" class="l">Range:<input type="radio" name="show" value="range" {$vchk['range']}/></td>
	<td class="r"><select name="rsw" onChange="JavaScript:select_radio(1)">$pulldown</select> to 
	<select name="rew" onChange="JavaScript:select_radio(1);">$pulldown</select></td></tr>
	<tr><td style="text-align:right;" class="tbl">Single<input type="radio" name="show" value="single" {$vchk['single']}/></td>
	<td class="trb"><select name="ssw" onChange="JavaScript:select_radio(2);return true;">$pulldown</select></td></tr>
	</table>
	</form>
CODE;
	$db->close();
	hk_check_status();
	get_report();
	include('foot.html');
}
function get_report() {
	global $eweek,$sweek,$vchk;
	
	$db=hk_db_connect();

	for ($i = $sweek['id']; $i <= $eweek['id']; ++$i) {
		echo "<hr /><br />\n";
		$gres = $db->query("SELECT games.*,t1.name AS hname,t2.name AS aname FROM games LEFT JOIN teams AS t1 ON (t1.id=games.home) LEFT JOIN teams AS t2 ON (t2.id=games.away) WHERE games.week=$i ORDER BY games.week,games.gametime");
		if($gres->num_rows != 0) {
			$data = array();
			$params = array();
			$week=hk_get_week($i);
			$data[0][0] = "<div class=\"head2\"><a name=\"week$i\">".$week['name']."</a><br />\n";
			$data[0][0] = $data[0][0]."<span class=\"head4\">[<a href=\"#top\">Back to top</a>]</span>\n</div>\n";
			$params[0][0] = " style=\"text-align:center;\"";
			
			$data[1][0] = "Final Score"; $params[1][0] = " style=\"font-weight:bold;\" class=\"right_trbl\"";
			$row = 2;
			$col = 1;
			while ($grow = $gres->fetch_assoc()) {
				$data[0][$col+0] = $grow['aname']; 			$params[0][$col+0] = " style=\"text-align:center;\" class=\"tbl\"";
				$data[0][$col+1] = "@";						$params[0][$col+1] = " style=\"text-align:center;\" class=\"tb\"";
				$data[0][$col+2] = $grow['hname'];			$params[0][$col+2] = " style=\"text-align:center;\" class=\"trb\"";
				$data[1][$col+0] = $grow['away_score'];		$params[1][$col+0] = " style=\"text-align:center;font-weight:bold;\" class=\"tbl\"";
				$data[1][$col+1] = "@";						$params[1][$col+1] = " style=\"text-align:center;font-weight:bold;\" class=\"tb\"";
				$data[1][$col+2] = $grow['home_score'];		$params[1][$col+2] = " style=\"text-align:center;font-weight:bold;\" class=\"trb\"";
				$pres = $db->query("SELECT picks.*,CONCAT(users.firstname,' ',users.lastname) AS name FROM users LEFT JOIN picks ON (picks.user=users.id AND picks.game={$grow['id']}) WHERE users.id!=-1 ORDER BY users.lastname,users.firstname");
				echo $db->error;
				while ($prow = $pres->fetch_assoc()) {
					$data[$row][0] = $prow['name']; 		$params[$row][0] 	  = " style=\"text-align:right;\"  class=\"trbl\"";
					if (time() < $grow['gametime']) {
						$data[$row][$col] = "*";				$params[$row][$col+0] = " style=\"text-align:center;\" class=\"tbl\"";
						$data[$row][$col+1] = "@";				$params[$row][$col+1] = " style=\"text-align:center;\" class=\"tb\"";
						$data[$row][$col+2] = "*";				$params[$row][$col+2] = " style=\"text-align:center;\" class=\"trb\"";
					} else {
						$data[$row][$col] = $prow['away'];		$params[$row][$col+0] = " style=\"text-align:center;\" class=\"tbl\"";
						$data[$row][$col+1] = "@";				$params[$row][$col+1] = " style=\"text-align:center;\" class=\"tb\"";
						$data[$row][$col+2] = $prow['home'];	$params[$row][$col+2] = " style=\"text-align:center;\" class=\"trb\"";
					}
					$row++;
				}
				$row = 2;
				$col += 3;
			}
			print_table($data,"<table border=\"0\" cellspacing=\"0\">",$params);
			echo "<br />\n";
		}
	}
}
?>
