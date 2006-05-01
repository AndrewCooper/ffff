<?php
	LOGIN_CHECK();
	include("head.php");
	global $db_link;
	
	echo <<<CODE
	<br /><img src="images/logo.gif" /><br /><br />
CODE;
	
	if ($_GET['action'] == "error") {
		echo <<<CODE
			<div style="color:red; text-algin:center;">
				{$_SESSION['error']}
			</div>
CODE;
	}
	echo <<<CODE
	<div class="databox">
		<form action="index.php?action=login" method="post">
		<input type="hidden" name="login" value="true" />
		Name:<input type="text" name="username" value="Name" size="10" maxlength="20" />&nbsp;
		Password:<input type="password" name="passwd" size="10" maxlength="20" />
		Log&nbsp;in&nbsp;automatically*:<input type="checkbox" name="remember" value="yes" />
		<input type="submit" name="submitter" value="Login" />
		</form>
	</div>
	<span style="font-size:smaller;">*Cookies must be enabled in your browser for automatic login.</span>
CODE;
	
	echo <<<CODE
	<hr />
	<div style="font-size:smaller; color:black;">The Fall Football Fanatic Frenzy is offered as a service of <a href="http://hkcreations.org">HK Creations</a>.</div>
	<table border="1" align="center" cellpadding="5">
	<tr><td colspan="2" align="center">Interesting Links:</td></tr>
	<tr><td colspan="2" align="center"><a href="http://store.apple.com/"><img src="images/madeonamac.gif" /></a></td>
	<tr><td align="center"><a href="http://www.php.net/"><img src="images/php-power-white.gif" /></a></td>
	<td align="center"><a href="http://www.mysql.com/"><img src="images/poweredbymysql-88.gif" /></a></td></tr>
	<tr><td colspan="2" align="center"><a href="http://www.apache.org/"><img src="images/apache_pb.gif" /></a></td>
	</tr>
	</table>
CODE;
	include("foot.php");

function LOGIN_CHECK() {
	session_start();
	if ($_GET["action"] == "login") {
		$db_link = mysql_connect("192.168.0.101","hkc","hkc");
		mysql_select_db("ffff");
		$find_query = "SELECT * FROM participants WHERE name=\"".$_POST['username']."\"";
		$find_result = mysql_query($find_query,$db_link);
		if (mysql_num_rows($find_result) == 0) {
			$_SESSION['error'] = "Username not found.";
			header("Location: http://".$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF'])."/index.php?action=error");
		} else {
			$find_row = mysql_fetch_assoc($find_result);
			if (crypt($_POST['passwd'],$find_row['passwd']) == $find_row['passwd']) {
				$_SESSION['login_id'] = $find_row['id'];
				$_SESSION['is_admin'] = $find_row['admin'];
				$_SESSION['full_name'] = $find_row['firstname']." ".$find_row['lastname'];
				if ($_POST['remember'] == "yes") {
					$cookievars = array($_SESSION['login_id'],$_SESSION['is_admin'],$_SESSION['full_name'],$_POST['remember']);
					$cookieS = implode(":",$cookievars);
					hk_setcookie($cookieS);
				}
				header("Location: http://".$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF'])."/toc.php?".strip_tags(SID));
	
			} else {
				$_SESSION['error'] = "Password for {$find_row['name']} invalid.";
				header("Location: http://".$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF'])."/index.php?action=error");
			}
		}
	}
}

function hk_setcookie($value) {
	if(!setcookie("ffff_auto_login",$value,time()+604800)) {
		echo "Error creating cookie";
	}
}
?>
