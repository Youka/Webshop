<?php
	function redirect($location) {
		header('Location: ' . $location);
		exit();
	}
?>