<?php
	require('php/env_need_login.php');
	
	// Action with parameters
	if(isset($_POST['pw'])) {
		// Confirm password
		$db_con = new DBConnection();
		if(($user = $db_con->querySingle('call P_GetUser(?)', 's', $_SESSION['login']['name'])) && crypt($_POST['pw'], $user['Password']) == $user['Password']) {
			// Change!
			if(isset($_POST['submit-change']) && isset($_POST['new-pw']) && isset($_POST['new-pw-confirm'])) {
				if($_POST['new-pw'] == $_POST['new-pw-confirm']) {
					if(!$db_con->execute('call P_UpdateUserPassword(?, ?)', 'is', $_SESSION['login']['id'], crypt($_POST['new-pw'])))
						set_error_message('Change failed!');
				} else
					set_error_message('Password not correctly confirmed!');
			}
			// Delete!
			if(isset($_POST['submit-delete'])) {
				if($db_con->execute('call P_DeleteUser(?)', 'i', $_SESSION['login']['id']))
					redirect('/logout.php');
				else
					set_error_message('Deletion failed!');
			}
		} else
			set_error_message('Invalid password.');
		$db_con->close();
	}
	
	require('html/header.htm');
	require('html/nav.php');
	
	show_error_message();
?>
<div class="p-3 text-center">
	<h1>Settings</h1>
	<div class="bordered-container w-25 mx-auto">
		<form action="" method="post">
			<h4>Change password</h4>
			<div class="form-group">
				<input type="password" name="pw" placeholder="Input old password" required class="form-control my-1" />
				<input type="password" name="new-pw" placeholder="Input new password" required class="form-control" />
				<input type="password" name="new-pw-confirm" placeholder="Input new password again" required class="form-control" />
			</div>
			<input type="submit" name="submit-change" value="Change" class="btn btn-warning" />
		</form>
		<hr />
		<form action="" method="post">
			<h4>Delete account</h4>
			<div class="form-group">
				<input type="password" name="pw" placeholder="Input password" required class="form-control" />
			</div>
			<input type="submit" name="submit-delete" value="Delete" class="btn btn-danger" onclick="return confirm('Are you sure?')" />
		</form>
	</div>
</div>
<?php
	require('html/footer.htm');
?>