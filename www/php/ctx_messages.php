<?php
	// Error
	$_errorMessage_5a78pz94 = '';
	function set_error_message($msg) {
		global $_errorMessage_5a78pz94;
		$_errorMessage_5a78pz94 = $msg;
	}
	function show_error_message() {
		global $_errorMessage_5a78pz94;
		if($_errorMessage_5a78pz94)
			echo '<div class="alert alert-danger" role="alert">' . $_errorMessage_5a78pz94 . '</div>';
	}
	
	// Info
	function show_info_message($msg) {
		echo '<div class="alert alert-info" role="alert">' . $msg . '</div>';
	}
?>