<?php
session_start();
include('../functions.php');
if(!isset($_SESSION['ffff_user_data'])) {
	hk_redir_rel('../index.php');
} elseif ($_SESSION['ffff_user_data']['is_admin']!=1) {
	$_SESSION['error']="You do not have access to the administration section";
	hk_redir_rel('../toc.php');
} elseif ($_GET['a']=='del') {
	get_user_delete();
} elseif ($_POST['submitter'] == "Submit") {
	$db=hk_db_connect();
	if ($_POST['newAdmin']=="on") {
		$admin=1;
	} else {
		$admin=0;
	}
	if ($_POST['action']=="edit") {
		$res=$db->query("SELECT password FROM users WHERE id={$_POST['uid']}");
		$row=$res->fetch_assoc();
		if ($_POST['newPassword']!="") {
			$password=md5($_POST['newPassword']);
		} else {
			$password=$row['password'];
		}
		$query="UPDATE users SET username=\"{$_POST['newUsername']}\", firstname=\"{$_POST['newFirstname']}\", lastname=\"{$_POST['newLastname']}\", password=\"$password\", email=\"{$_POST['newEmail']}\", admin=$admin WHERE id={$_POST['uid']}";
		$getstr="?a=e";
		$db->query($query);
		$_SESSION['error'].=$db->error;
	} else {
		$cols = "username, firstname, lastname, password, email, admin";
		$values = "'{$_POST['newUsername']}', '{$_POST['newFirstname']}', '{$_POST['newLastname']}', '".md5($_POST['newPassword'])."', '{$_POST['newEmail']}', '$admin'";
		$query="INSERT INTO users ($cols) VALUES ($values)";
		$db->query($query);
		$_SESSION['error'].=$db->error;
		$res=$db->query("SELECT id FROM users WHERE username='{$_POST['newUsername']}'");
		$_SESSION['error'].=$db->error;
		$row=$res->fetch_assoc();
		$db->query("INSERT INTO scores (user) VALUES ({$row['id']})");
		$_SESSION['error'].=$db->error;
	}
	if ($_SESSION['error']=="") {
		$_SESSION['message']="User created or updated successfully.";
	} else {
		$_SESSION['error']="There were problems creating or updating the user.";
	}
	$_SESSION['message'].="<br />".$query;
	$_SESSION['message'].="\n<script language=\"JavaScript\">\nparent.nav.location.reload();\n</script>";
	hk_redir_rel('user.php'.$getstr);
} else {
	include('head.html');
	echo <<<CODE
	<div class="head5" style="text-align:right;"><a href="admin.php">[Administration]</a></div>
	<img src="../images/logo.gif" /><br />
	<div style="font-size:smaller;"><a href="../toc.php">[Back to Table of Contents]</a></div><br />
	<div class="head1">Create/Edit User</div>
CODE;
	hk_check_status();
	get_userinfo_table();
	include('foot.html');
}
function get_userinfo_table() {
	if ($_GET['a']=="e") {
	echo "Editing";
	$db = hk_db_connect();
	$res = $db->query("SELECT * FROM users WHERE id={$_SESSION['ffff_user_data']['id']}");
	$data = $res->fetch_assoc();
	$res->close();
	$db->close();
	$user="<input type=\"hidden\" name=\"uid\" value=\"{$_SESSION['ffff_user_data']['id']}\" />";
	$unedit="readonly=\"readonly\"";
	if ($data['admin']=1) {
		$uadmin="checked=\"checked\"";
	}
	$action="<input type=\"hidden\" name=\"action\" value=\"edit\" />";
	}
	echo <<<CODE
	<div class="box1">
	<form action="user.php" method="post">
	$user
	$action
	<table border="0" cellspacing="0" cellpadding="2" style="margin:auto;text-align:center;">
	<tfoot><tr><td colspan="2"><input type="submit" name="submitter" value="Submit" /></td></tr></tfoot>
	<tr><td style="width:50%;text-align:right;" class="tl">Username:</td>	<td style="width:50%;text-align:left;" class="tr"><input type="text" name="newUsername" size="20" maxlength="20" value="{$data['username']}" $unedit /></td></tr>
	<tr><td style="width:50%;text-align:right;" class="l">Email:</td>		<td style="width:50%;text-align:left;" class="r"><input type="text" name="newEmail" size="20" maxlength="255" value="{$data['email']}" /></td></tr>
	<tr><td style="width:50%;text-align:right;" class="l">First Name:</td>	<td style="width:50%;text-align:left;" class="r"><input type="text" name="newFirstname" size="20" maxlength="20" value="{$data['firstname']}" /></td></tr>
	<tr><td style="width:50%;text-align:right;" class="bl">Last Name:</td>	<td style="width:50%;text-align:left;" class="rb"><input type="text" name="newLastname" size="20" maxlength="20" value="{$data['lastname']}" /></td></tr>
	<tr><td colspan="2"><hr style="width:100%;" /><div class="box2" style="margin:auto;width:75%;">To change or set the user's password, enter it below.</div></td></tr>
	<tr><td style="width:50%;text-align:right;" class="tbl">New Password:</td>		<td style="width:50%;text-align:left;" class="trb"><input type="password" name="newPassword" size="20" maxlength="20" /></td></tr>
	<tr><td colspan="2"><hr style="width:100%;" /><div class="box2" style="margin:auto;width:75%;">To make this user an administrator, check the box below.</div></td></tr>
	<tr><td style="width:50%;text-align:right;" class="tbl">Is Administrator:</td>		<td style="width:50%;text-align:left;" class="trb"><input type="checkbox" name="newAdmin" size="20" maxlength="20" $uadmin /></td></tr>
	</table>
	</form>
	</div>
CODE;
}

function get_user_delete() {
include('head.html');
echo <<<CODE
<div class="head5" style="text-align:right;"><a href="admin.php">[Administration]</a></div>
<img src="../images/logo.gif" /><br />
<div style="font-size:smaller;"><a href="../toc.php">[Back to Table of Contents]</a></div><br />
<div class="head1">Delete User</div>
CODE;
hk_check_status();
if ($_POST['submitter']=="Delete") {
	$db=hk_db_connect();
	$row=$db->query("SELECT * FROM users WHERE id={$_POST['delid']}")->fetch_assoc();
	echo <<<CODE
	<div class="head2" style="color=red;">WARNING</div>
	<div class="box1">You are about to delete all scores, picks, and user data for {$row['firstname']} {$row['lastname']}. This action is NOT reversable.</div>
	<div class="head3">Confirm Deletion?<br /><form action="user.php?a=del" method="post"><input type="hidden" name="uid" value="{$_POST['delid']}" /><input type="submit" name="submitter" value="Confirm" /></form></div>
CODE;
} elseif ($_POST['submitter']=="Confirm") {
	$uid=$_POST['uid'];
	$db=hk_db_connect();
	$db->query("DELETE FROM users WHERE id=$uid");
	echoln($db->error);
	$db->query("DELETE FROM scores WHERE user=$uid");
	echoln($db->error);
	$db->query("DELETE FROM picks WHERE user=$uid");
	echoln($db->error);
	echo "<div class=\"box1\">User Successfully Deleted.</div>";
} else {
	echo <<<CODE
	<div class="head3">User to Delete:
	<form action="user.php?a=del" method="post">
	<select name="delid">
CODE;
	$db = hk_db_connect();
	$ures= $db->query("SELECT firstname,lastname,id FROM users WHERE id!=-1 ORDER BY lastname,firstname");
	while($urow=$ures->fetch_assoc()) {
	echo "<option value=\"{$urow['id']}\" >{$urow['firstname']}&nbsp;{$urow['lastname']}</option>\n";
	}
	echo <<<CODE
	</select>\n<br /><input type="submit" name="submitter" value="Delete" />
	</form></div>
CODE;
}		
include('foot.html');
}
	
?>
