<?php
	require('php/env_need_login.php');
	
	// Variable action by parameter
	if(!empty($_POST)) {
		// Delete list item
		$first_key = key($_POST);
		if(strncmp($first_key, "delete_article_", 15) === 0) {
			$db_con = new DBConnection();
			if(!$db_con->execute('call P_OutOfShoppingCard(?, ?)', 'ii', $_SESSION['login']['id'], intval(substr($first_key, 15))))
				set_error_message('Deletion failed!');
			$db_con->close();
		}
		// Order!
		if(isset($_POST['pay-method']) && isset($_POST['delivery-address'])) {
			$db_con = new DBConnection();
			$db_con->execute('call P_OrderFromShoppingCard(?, ?, ?)', 'iss', $_SESSION['login']['id'], $_POST['pay-method'], $_POST['delivery-address']);
			$db_con->close();
			redirect('/orders.php');
		}
	}
	
	require('html/header.htm');
	require('html/nav.php');
	
	show_error_message();
?>
<div class="p-3 text-center">
	<h1>Shopping Card</h1>
	<!-- Data -->
	<form action="" method="post">
		<!-- Articles list -->
		<ul class="list-group">
		<?php
			$db_con = new DBConnection();
			if($data = $db_con->query('call P_GetShoppingCard(?)', 'i', $_SESSION['login']['id'])) {
				foreach($data as $row)
					printf(
						'<li class="list-group-item">' .
						'<input type="submit" name="delete_article_%d" value="&times;" class="position-absolute" style="top: 0; right: 0;" />' .
						'<h5>Article #%s</h5>' .
						'<div><span class="font-weight-bold">Amount:</span> %d</div>' .
						'</li>',
						$row['Article_Id'],
						$row['Article_Id'],
						$row['Amount']
					);
			} else
				echo 'No entry yet!';
		?>
		</ul>
		<!-- Total price -->
		<h3 class="p-5">
			<?php
				if($total_price = $db_con->queryScalar('SELECT F_TotalPriceFromShoppingCard(?)', 'i', $_SESSION['login']['id']))
					printf('<u>TotalPrice: %.2f&euro;</u>', $total_price);
				$db_con->close();
			?>
		</h3>
	</form>
	<!-- Order formular -->
	<form action="" method="post" class="bordered-container w-25 mx-auto text-left">
		<div class="form-group">
			<label for="delivery-address">Delivery address:</label>
			<input type="text" name="delivery-address" id="delivery-address" placeholder="Input delivery address" required class="form-control" <?php if(empty($data)) echo 'disabled'; ?> />
		</div>
		<div class="form-group">
			<label for="pay-method">Pay method:</label>
			<input type="text" name="pay-method" id="pay-method" placeholder="Input pay method" required class="form-control" <?php if(empty($data)) echo 'disabled'; ?> />
		</div>
		<input type="submit" value="Order" class="btn btn-primary" <?php if(empty($data)) echo 'disabled'; ?> />
	</form>
</div>
<?php
	require('html/footer.htm');
?>