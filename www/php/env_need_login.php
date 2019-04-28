<?php
	require('env.php');

	if(!isset($_SESSION['login']))
		redirect('/login.php');
?>