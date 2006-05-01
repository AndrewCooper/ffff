<?php
session_start();
include('../functions.php');
include('head.html');
$ud = $_SESSION['ffff_user_data'];
$thisweek = hk_get_week();
$_SESSION['thisweek'] = $thisweek;
$url="http://sports.espn.go.com/ncf/rankings?poll=2&week=".$thisweek['id'];
$filestr=file_get_contents($url);
$pattern="<!-- 2 -->\n<!-- {$thisweek['id']} -->\n<!-- 2004 -->\n<!-- 2 -->\n";
$pos1=strpos($filestr,$pattern)+strlen($pattern);
$pos2=strpos($filestr,"\n",$pos1);
$sub=substr($filestr,$pos1,$pos2-$pos1);
$sub2=str_replace(">",">\n",$sub);
echo "<table>".$sub2."</table>";

$rowpat="'<tr[^>]*>.*?</tr>'si";
$celpat="'<td[^>]*>.*?</td>'si";
$apat="'<a[^>]*>(.*?)</a>'si";
$bpat="'<b[^>]*>(([^\\.]*?)([\\.]?))</b>'si";
$num=preg_match_all($rowpat,$sub,$matches);
$i=0;
$rows=$matches[0];
$db=hk_db_connect();
$db->query("UPDATE teams SET rank=-1");
echo "<xmp>";
foreach ($rows as $row) {
	if ($i==0) {
		$num=preg_match_all($celpat,$row,$matches);
		$cells=$matches[0];
		preg_match($bpat,$cells[0],$rank);
		preg_match($apat,$cells[1],$team);
		preg_match($bpat,$cells[2],$rec);
		$q="UPDATE teams SET rank={$rank[2]},record='{$rec[2]}' WHERE name='{$team[1]}'";
		$db->query($q);
		echo $q.":".$db->error."\n";
	}
	$i=(++$i)%4;
}
echo "</xmp>";
	
?>
