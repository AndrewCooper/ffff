<?php
session_start();
include('functions.php');
if (isset($_COOKIE['ffff_auto_login'])) {
	hk_redir_rel("login.php");
}
?>

<?php 
include("head.html");

hk_check_status();
?>
<br /><img src="images/logo.gif" /><br /><br />
	<form action="login.php" method="post">
<div class="databox">
	Name:<input type="text" name="username" value="guest" size="10" maxlength="20" />&nbsp;
	Password:<input type="password" name="passwd" size="10" maxlength="20" />
	Log&nbsp;in&nbsp;automatically*:<input type="checkbox" name="remember" value="yes" />
	<input type="submit" name="submitter" value="Login" />
</div>
<div style="font-size:smaller;">*Cookies must be enabled in your browser for automatic login.</div>
	</form>
<hr />
<div style="font-size:smaller; color:black;">The Fall Football Fanatic Frenzy is offered as a service of <a href="http://hkcreations.org">HK Creations</a>.</div>
<table border="1" align="center" cellpadding="5">
<tr><td colspan="2" align="center">Interesting Links:</td></tr>
<tr><td colspan="2" align="center"><a href="http://store.apple.com/"><img src="images/madeonamac.gif" /></a></td></tr>
<tr><td align="center"><a href="http://www.php.net/"><img src="images/php-power-white.gif" /></a></td></tr>
<td align="center"><a href="http://www.mysql.com/"><img src="images/poweredbymysql-88.gif" /></a></td></tr>
<tr><td colspan="2" align="center"><a href="http://www.apache.org/"><img src="images/apache_pb.gif" /></a></td></tr>
</table>

<?php include("foot.html"); ?>
