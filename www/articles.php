<?php
	require('php/env_need_login.php');
	
	// Shopping card additions by parameters
	if(!empty($_POST)) {
		$db_con = new DBConnection();
		foreach($_POST as $name=>$value)
			if(strncmp($name, 'amount_article_', 15) === 0) {
				$article_id = intval(substr($name, 15));
				$article_amount = intval($value);
				if($article_amount > 0)
					if(!$db_con->execute('call P_IntoShoppingCard(?, ?, ?)', 'iii', $_SESSION['login']['id'], $article_id, $article_amount))
						set_error_message('Insertion failed!');
			}
		$db_con->close();
		redirect('/shopping_card.php');
	}
	
	require('html/header.htm');
	require('html/nav.php');
	
	show_error_message();
?>
<div class="p-3 text-center">
	<h1>Articles</h1>
	<form action="" method="post">
		<table class="d-inline text-left table table-sm table-responsive table-bordered table-striped">
			<caption class="text-right"><input type="submit" value="Add to Shopping Card" class="btn btn-success" /></caption>
			<thead class="thead-dark">
				<th class="sortable">ID</th>
				<th>Image</th>
				<th class="sortable">Vendor</th>
				<th class="sortable">Model</th>
				<th class="sortable">Category</th>
				<th class="sortable">Description</th>
				<th class="sortable">Price</th>
				<th class="sortable">Discount</th>
				<th class="sortable">Storage</th>
				<th>Amount</th>
			</thead>
			<tbody>
				<?php
					$db_con = new DBConnection();
					if($data = $db_con->query('SELECT * FROM V_AllArticles'))
						foreach($data as $row)
							printf(
								'<tr>' .
								'<td>%d</td>' .
								'<td><img src="%s" height="32" /></td>' .
								'<td>%s</td>' .
								'<td>%s</td>' .
								'<td>%s</td>' .
								'<td>%s</td>' .
								'<td>%.2f&euro;</td>' .
								'<td>%d%%</td>' .
								'<td>%d</td>' .
								'<td><input type="number" name="amount_article_%d" min="0" max="%d" step="1" value="0" autocomplete="off" %s  /></td>' .
								'</tr>',
								$row['Id'],
								$row['Image_Url'],
								$row['Vendor'],
								$row['Model'],
								$row['Category'],
								$row['Description'],
								$row['Price'],
								$row['Discount'],
								$row['Amount'],
								$row['Id'], $row['Amount'], $row['Amount'] == 0 ? 'disabled' : null
							);
					$db_con->close();
				?>
			</tbody>
		</table>
	</form>
</div>
<?php
	require('html/footer.htm');
?>