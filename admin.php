<?php
session_start();
include('functions.php');
if(!isset($_SESSION['ffff_user_data'])) {
	hk_redir_rel('/index.php');
} elseif ($_SESSION['ffff_user_data']['is_admin']!=1) {
	$_SESSION['error']="You do not have access to the administration section";
	hk_redir_rel('/toc.php');
}
?>
<DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN"
		"http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
	<title>ffff2 administration</title>
</head>
<frameset cols="215px,*">
	<frame name="nav" id="nav" src="admin/nav.php" frameborder="1" marginheight="0" marginwidth="0" />
	<frame name="main" id="main" src="toc.php" frameborder="1" marginheight="0" marginwidth="0" />
</frameset>
</html>
