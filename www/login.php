<?php
	require('php/env.php');

	// Login with parameters
	if(isset($_POST['name']) && isset($_POST['pw'])) {
		$db_con = new DBConnection();
		if(($user = $db_con->querySingle('call P_GetUser(?)', 's', $_POST['name'])) && crypt($_POST['pw'], $user['Password']) == $user['Password']) {
			$_SESSION['login'] = [
				'id' => $user['Id'],
				'name' => $user['Name'],
				'role' => $user['Role'],
				'email' => $user['Email'],
				'creation' => $user['Creation_Datetime'],
				'premium' => $user['Is_Premium'],
				'superuser' => $user['Is_Superuser']
			];
		} else
			set_error_message('Login failed! Invalid inputs.');
		$db_con->close();
	}
	// Redirect to main page if logged in
	if(isset($_SESSION['login']))
		redirect('/');

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
			</div>
			<hr />
			<div class="btn-group">
				<input type="submit" value="Login" class="btn btn-primary" />
				<a href="/register.php" class="btn btn-secondary">Register</a>
			</div>
		</form>
	</div>
<?php
	require('html/footer.htm');
?>