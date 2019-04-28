<?php
	require('php/env.php');
	
	// Register with parameters
	if(isset($_POST['name']) && isset($_POST['pw']) && isset($_POST['pw-confirm']) && isset($_POST['email']))
		// Password confirmed?
		if($_POST['pw'] == $_POST['pw-confirm']) {
			$db_con = new DBConnection();
			$changes = $db_con->execute('call P_CreateUser(?, ?, ?)', 'sss', $_POST['name'], crypt($_POST['pw']), $_POST['email']);
			$db_con->close();
			if($changes)
				redirect('/login.php');
			else
				set_error_message('Could not register user! Maybe name or email already exists.');
		} else
			set_error_message('Password not correctly confirmed!');

	require('html/header.htm');
	
	show_error_message();
?>
	<div class="center-screen">
		<form action="" method="post" class="bordered-container">
			<div class="form-group">
				<label for="name">Name:</label>
				<input type="text" name="name" id="name" placeholder="Input user name" required class="form-control" />
			</div>
			<div class="form-group">
				<label for="pw">Password:</label>
				<input type="password" name="pw" id="pw" placeholder="Input password" required class="form-control" />
				<input type="password" name="pw-confirm" placeholder="Input password again" required class="form-control" />
			</div>
			<div class="form-group">
				<label for="email">E-Mail:</label>
				<input type="email" name="email" id="email" placeholder="Input E-Mail" required class="form-control" />
			</div>
			<hr />
			<input type="submit" value="Register" class="btn btn-primary" />
		</form>
	</div>
<?php
	require('html/footer.htm');
?>