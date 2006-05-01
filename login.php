<?php
session_start();
require("functions.php");

if ($_POST['submitter'] == "Login") {
	check_login($_POST['username'],$_POST['passwd'],false);
} elseif ($_GET['a']=='lo') {
	logout();
} elseif ($_GET['a']=='su' && $_POST['submitter']=="Change") {
	switch_user();
} elseif (isset($_COOKIE['ffff_auto_login'])) {
	$cd = explode(":",$_COOKIE['ffff_auto_login']);
	check_login($cd[0],$cd[1],true);
} else {
	$_SESSION['error']="No function specified for login. Contact the webmaster";
	hk_redir_rel("index.php");
}

function check_login($user,$pass,$encrypted) {
	$db = hk_db_connect();
	$result = $db->query("SELECT * FROM users WHERE username=\"$user\"");
	if ($result->num_rows == 0) {
		$_SESSION['error'] = "<br />\nUsername ".$_POST['username']." not found.";
		hk_delcookie("ffff_auto_login");
		hk_redir_rel("index.php");
	} else {
		$find_row = $result->fetch_assoc();
		$p = $find_row['password'];
		if (($encrypted && $pass == $p) || (!$encrypted && md5($pass) == $p)) {
			$fses = array('id' => $find_row['id'],'is_admin' => $find_row['admin'],'full_name' => $find_row['firstname']." ".$find_row['lastname']);
			$_SESSION['ffff_user_data'] = $fses;
			if ($_POST['remember'] == "yes" || isset($_COOKIE['ffff_auto_login'])) {
				$cookievars = array($find_row['username'],$find_row['password']);
				$cookieS = implode(":",$cookievars);
				hk_setcookie("ffff_auto_login",$cookieS);
			} else {
				hk_delcookie("ffff_auto_login");
			}
			unset($_SESSION['error']);
			hk_redir_rel("toc.php");
		} else {
			$_SESSION['error'] = "Password for ".$find_row->username." is invalid.";
			hk_delcookie("ffff_auto_login");
			hk_redir_rel("index.php");
		}
	}
	$result->close();
	$db->close();
}

function logout() {
	hk_delcookie("ffff_auto_login");
	unset($_SESSION['ffff_user_data']);
	hk_redir_rel("index.php");
}

function switch_user() {
	$id = $_POST['newid'];
	$db = hk_db_connect();
	$res = $db->query("SELECT id,admin AS is_admin,CONCAT(firstname,' ',lastname) AS full_name FROM users WHERE id=$id");
	$_SESSION['ffff_user_data'] = $res->fetch_assoc();
	$_SESSION['ffff_user_data']['is_admin']='1';
	hk_redir_rel("admin.php");
}
