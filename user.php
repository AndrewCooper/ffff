<?php
	include("head.php");
	global $db_link;
	echo "<h1>Edit User Preferences</h1>";
	echo "<a href=\"toc.php\">Back to Table of Contents</a><br />";
	echo "<br />\n";

	if ($_GET['action'] == "user_update") {
		if ($_POST['newPassword1'] == "") {
			$passq = "";
		} else if ($_POST['newPassword1'] == $_POST['newPassword2']) {
			$passq = "passwd=\"".crypt($_POST['newPassword1'])."\",";
		} else {
			unset($passq);
			$error_msg = "<div style=\"color:red; font-weight: bold; text-align:center; white-space:nowrap;\">Passwords do not match. Go back and try again</div><br />";
		}
		if (isset($passq)) {
			$update_query = "UPDATE participants set name=\"".$_POST['newUsername']."\",".$passq."email=\"".$_POST['newEmail']."\",firstname=\"".$_POST['newFirstName']."\",lastname=\"".$_POST['newLastName']."\",phone=\"".$_POST['newPhone']."\" WHERE id=\"".$_SESSION['login_id']."\"";
			echo $update_query;
			if (mysql_query($update_query,$db_link)) {
				$success_msg = "<div style=\"font-weight:bold;\">User Update Successful.</div>";
			} else {
				$error_msg = "There were problems with the update.<br />".mysql_error()."<br />";
			}
		}
	}
	if (!isset($success_msg)) {
		if (isset($error_msg)) {
			echo $error_msg;
			$data = array('name' => $_POST['newUsername'], 'email' => $_POST['newEmail'], 'firstname' => $_POST['newFirstName'], 'lastname' => $_POST['newLastName'], 'phone' => $_POST['newPhone']);
		} else {
			$data_query = "SELECT * FROM participants WHERE id=\"{$_SESSION['login_id']}\"";
			$data_result = mysql_query($data_query,$db_link);
			echo mysql_error();
			$data = mysql_fetch_assoc($data_result);
		}
		echo <<<CODE
		<div class="databox">
			<form action="user.php?action=user_update" method="post">
				<table border="0" cellspacing="0">
					<tr><td style="text-align:right;">Username:</td><td><input type="text" name="newUsername" size="20" maxlength="20" value="{$data['name']}" /></td></tr>
					<tr><td style="text-align:right;">Password:</td><td><input type="password" name="newPassword1" size="20" maxlength="20" /></td></tr>
					<tr><td style="text-align:right;">Confirm Password:</td><td><input type="password" name="newPassword2" size="20" maxlength="20" /></td></tr>
					<tr><td style="text-align:right;">Email:</td><td><input type="text" name="newEmail" size="20" maxlength="255" value="{$data['email']}" /></td></tr>
					<tr><td style="text-align:right;">First Name:</td><td><input type="text" name="newFirstName" size="20" maxlength="20" value="{$data['firstname']}" /></td></tr>
					<tr><td style="text-align:right;">Last Name:</td><td><input type="text" name="newLastName" size="20" maxlength="20" value="{$data['lastname']}" /></td></tr>
					<tr><td style="text-align:right;">Phone #:</td><td><input type="text" name="newPhone" size="20" maxlength="20" value="{$data['phone']}" /></td></tr>
					<tr><td style="border-top:1px solid black;"></td><td style="border-top:1px solid black;"><input type="reset" name="reset" value="Reset" />&nbsp;<input type="submit" name="submitter" value="Update" /></td></tr>
				</table>
			</form>
		</div>
CODE;
	} else {
		echo $success_msg;
		echo "<a href=\"user.php\">Back to User Preferences</a><br />";
		echo "<a href=\"toc.php\">Back to Table of Contents</a><br />";
	}
	include("foot.php");
?>
