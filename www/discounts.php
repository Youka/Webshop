<?php
	require('php/env_need_login.php');
	
	require('html/header.htm');
	require('html/nav.php');
?>
<div class="p-3 text-center">
	<h1>Discounts</h1>
	<ul class="list-group">
		<?php
			// Only premium
			if($_SESSION['login']['premium']) {
				$db_con = new DBConnection();
				if($data = $db_con->query('SELECT * FROM V_ActiveDiscounts'))
					foreach($data as $row)
						printf(
							'<li class="list-group-item">' .
							'<h5>%s</h5>' .
							'<img src="%s" height="64" />%s: %s<span class="ml-5">%s</span><small class="ml-5">%s [#%d]</small><br />' .
							'<span class="font-weight-bold">%.2f&euro;<span class="text-success"> - %d%% = %.2f&euro;</span></span><br />' .
							'<small>From %s until %s</small>' .
							'</li>',
							$row['Reason'],
							$row['Image_Url'], $row['Vendor'], $row['Model'], $row['Description'], $row['Category'], $row['Article_Id'],
							$row['Price'], $row['Percent'], $row['Price'] * (1 - $row['Percent'] / 100.0),
							$row['Start_Date'], $row['End_Date']
						);
				$db_con->close();
			} else
				show_info_message('Premium required!');
		?>
	</ul>
</div>
<?php
	require('html/footer.htm');
?>