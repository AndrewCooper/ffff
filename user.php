<?php
session_start();
include('functions.php');
if(!isset($_SESSION['ffff_user_data'])) {
	hk_redir_rel('index.php');
} elseif($_POST['submitter']=="Submit Information") {
	if($_SESSION['ffff_user_data']['id']!=-1) {
		$db=hk_db_connect();
		$_SESSION['message']="UPDATE users SET username=\"{$_POST['newUsername']}\", firstname=\"{$_POST['newFirstname']}\", lastname=\"{$_POST['newLastname']}\", email=\"{$_POST['newEmail']}\" WHERE id={$_SESSION['ffff_user_data']['id']}";
		if ($db->query("UPDATE users SET username=\"{$_POST['newUsername']}\", firstname=\"{$_POST['newFirstname']}\", lastname=\"{$_POST['newLastname']}\", email=\"{$_POST['newEmail']}\" WHERE id={$_SESSION['ffff_user_data']['id']}")) {
			$_SESSION['message']="Information successfully updated";
		} else {
			$_SESSION['error']="Error updating information:<br />".$db->error;
		}
		if ($_POST['oldPassword']!=NULL || $_POST['newPassword1']!=NULL) {
			$res=$db->query("SELECT * FROM users WHERE id={$_SESSION['ffff_user_data']['id']}");
			$data=$res->fetch_assoc();
			if (md5($_POST['oldPassword'])==$data['password']) {
				if ($_POST['newPassword1']==$_POST['newPassword2']) {
					$db->query("UPDATE users SET password=\"".md5($_POST['newPassword1'])."\" WHERE id={$_SESSION['ffff_user_data']['id']}");
					$_SESSION['message'].="<br />Password successfully changed.";
				} else {
					$_SESSION['error']="Error setting password:<br />New passwords do not match.";
				}
			} else {
				$_SESSION['error']="Error setting password:<br />Old password not correct.";
			}
			$res->close();
		}
		$db->close();
		hk_redir_rel('user.php');
	} else {
		$_SESSION['error']="I'm sorry, the guest account may not make account changes.";
		hk_redir_rel('user.php');
	}
} else {
	include('head.html');
	echo <<<CODE
	<div class="head5" style="text-align:right;"><a href="admin.php">[Administration]</a></div>
	<img src="images/logo.gif" /><br />
	<div style="font-size:smaller;"><a href="toc.php">[Back to Table of Contents]</a></div><br />
	<div class="head1">Edit User Information</div>
CODE;
	hk_check_status();
	get_userinfo_table();
	include('foot.html');
}

function get_userinfo_table() {
	$db = hk_db_connect();
	$res = $db->query("SELECT * FROM users WHERE id={$_SESSION['ffff_user_data']['id']}");
	$data = $res->fetch_assoc();
	echo <<<CODE
	<div class="box1">
	<form action="user.php?action=user_update" method="post">
	<table border="0" cellspacing="0" cellpadding="2" style="margin:auto;text-align:center;">
	<tfoot><tr><td colspan="2"><input type="submit" name="submitter" value="Submit Information" /></td></tr></tfoot>
	<tr><td style="width:50%;text-align:right;" class="tl">Username:</td>	<td style="width:50%;text-align:left;" class="tr"><input type="text" name="newUsername" size="20" maxlength="20" value="{$data['username']}" readonly="readonly" /></td></tr>
	<tr><td style="width:50%;text-align:right;" class="l">Email:</td>		<td style="width:50%;text-align:left;" class="r"><input type="text" name="newEmail" size="20" maxlength="255" value="{$data['email']}" /></td></tr>
	<tr><td style="width:50%;text-align:right;" class="l">First Name:</td>	<td style="width:50%;text-align:left;" class="r"><input type="text" name="newFirstname" size="20" maxlength="20" value="{$data['firstname']}" /></td></tr>
	<tr><td style="width:50%;text-align:right;" class="bl">Last Name:</td>	<td style="width:50%;text-align:left;" class="rb"><input type="text" name="newLastname" size="20" maxlength="20" value="{$data['lastname']}" /></td></tr>
	<tr><td colspan="2"><hr /><div class="box2" style="margin:auto;width:75%;">To change your password, enter your current password, the new password, and the new password again to confirm.</div></td></tr>
	<tr><td style="width:50%;text-align:right;" class="tl">Current Password:</td>	<td style="width:50%;text-align:left;" class="tr"><input type="password" name="oldPassword" size="20" maxlength="20" /></td></tr>
	<tr><td style="width:50%;text-align:right;" class="l">New Password:</td>		<td style="width:50%;text-align:left;" class="r"><input type="password" name="newPassword1" size="20" maxlength="20" /></td></tr>
	<tr><td style="width:50%;text-align:right;" class="bl">Confirm Password:</td>	<td style="width:50%;text-align:left;" class="rb"><input type="password" name="newPassword2" size="20" maxlength="20" /></td></tr>
	</table>
	</form>
	</div>
CODE;
	$res->close();
	$db->close();
}
?>
