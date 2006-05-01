<?php
	global $db_link;
	if ($_POST['mode'] == "create_user") {
		$cols = "name,firstname,lastname,passwd,email,phone";
		$values = "'{$_POST['username']}','{$_POST['firstname']}','{$_POST['lastname']}','".crypt($_POST['passwd'])."','{$_POST['email']}','{$_POST['phone']}'";
		$query="INSERT INTO participants ($cols) VALUES ($values)";

		echo $query."<br />";
		if (mysql_query($query,$db_link)) {
			echo "User Created Successfully<br />";
			echo "<a href=\"admin.php\">Back to Administration</a>";
		} else {
			echo mysql_error($db_link);
		}
		echo mysql_info($db_link);
	} else {
		echo <<<CODE
		<h2>Create New User</h2>
		<form action="index.php?action=create" method="post">
		<input type="hidden" name="mode" value="create_user" />
		Username:<input type="text" name="username" size="10" maxlength="20" /><br />
		First Name:<input type="text" name="firstname" size="10 maxlength="20" /><br />
		Last Name:<input type="text" name="lastname" size="10" maxlength="20" /><br />
		Password:<input type="password" name="passwd" size="10" maxlength="20" /><br />
		Email:<input type="text" name="email" size="10" maxlength="255" /><br />
		Phone:<input type="text" name="phone" size="10" maxlength="20" /><br />
		<input type="submit" name="submitter" value="Create" />
		</form>
CODE;
	}
?>
