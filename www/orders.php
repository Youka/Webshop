<?php
	require('php/env_need_login.php');
	
	require('html/header.htm');
	require('html/nav.php');
?>
<div class="p-3 text-center">
	<h1>Orders</h1>
	<h3>Last order</h3>
	<div class="card card-body mb-4">
		<?php
			$db_con = new DBConnection();
			if($data = $db_con->querySingle('call P_LastOrderSummary(?)', 'i', $_SESSION['login']['id']))
				printf(
					'<p><b>Id:</b> %d</p>' .
					'<p><b>Pay method:</b> %s</p>' .
					'<p><b>Delivery address:</b> %s</p>' .
					'<p><b>Datetime:</b> %s</p>' .
					'<p><b>Articles:</b> %s</p>' .
					'<p><b>Total price:</b> %.2f&euro;</p>',
					$data['Id'],
					$data['Pay_Method'],
					$data['Delivery_Address'],
					$data['Order_Datetime'],
					$data['Articles'],
					$data['Total_Price']
				);
			else
				echo 'No orders yet!';
		?>
	</div>
	<h3>All orders</h3>
	<table class="d-inline text-left table table-sm table-responsive table-bordered table-striped">
		<thead class="thead-dark">
			<th>Order ID</th>
			<th>Pay Method</th>
			<th>Delivery Address</th>
			<th>Order Datetime</th>
			<th>Article ID</th>
			<th>Amount</th>
			<th>Price</th>
			<th>Discount</th>
		</thead>
		<tbody>
		<?php
			if($data = $db_con->query('call P_GetOrders(?)', 'i', $_SESSION['login']['id']))
				foreach($data as $row)
					printf(
						'<tr>' .
						'<td>%d</td>' .
						'<td>%s</td>' .
						'<td>%s</td>' .
						'<td>%s</td>' .
						'<td>%d</td>' .
						'<td>%d</td>' .
						'<td>%.2f&euro;</td>' .
						'<td>%d%%</td>' .
						'</tr>',
						$row['Id'],
						$row['Pay_Method'],
						$row['Delivery_Address'],
						$row['Order_Datetime'],
						$row['Article_Id'],
						$row['Amount'],
						$row['Price'],
						$row['Discount']
					);
			$db_con->close();
		?>
		</tbody>
	</table>
</div>
<?php
	require('html/footer.htm');
?>