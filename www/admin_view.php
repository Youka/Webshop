<?php
	require('php/env_need_login.php');
	
	require('html/header.htm');
	require('html/nav.php');
?>
<div class="p-3 text-center">
	<h1>Admin View</h1>
	<?php
		// Only superusers
		if($_SESSION['login']['superuser']) {
			$db_con = new DBConnection();
			$key_class_lookup = ['PRI' => 'text-primary', 'MUL' => 'text-secondary', 'UNI' => 'font-italic'];
			// Get current database table names
			foreach($db_con->query('SELECT table_name FROM information_schema.tables WHERE table_schema=database() AND table_type="BASE TABLE"') as $table_info) {
				$table_name = $table_info['table_name'];
				// Begin table
				printf('<h4>%s</h4><table class="d-inline text-left table table-sm table-responsive table-bordered table-striped">', $table_name);
				// Add table header
				echo '<thead class="thead-dark">';
				foreach($db_con->query('SELECT column_name, column_key FROM information_schema.columns WHERE table_schema=database() AND table_name="' . $table_name . '"') as $column_info) {
					$key = $column_info['column_key'];
					printf('<th class="%s">%s</th>', isset($key_class_lookup[$key]) ? $key_class_lookup[$key] : null, $column_info['column_name']);
				}
				echo '</thead>';
				// Add table data
				echo '<tbody>';
				foreach($db_con->query('SELECT * FROM ' . $table_name) as $row) {
					echo '<tr>';
					foreach($row as $value)
						printf('<td>%s</td>', $value);
					echo '</tr>';
				}
				echo '</tbody>';
				// Finish table
				echo '</table>';
			}
		} else
			show_info_message('Superuser required!');
	?>
</div>
<?php
	require('html/footer.htm');
?>