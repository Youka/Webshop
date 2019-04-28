<nav class="nav bg-dark">
	<!-- Menu -->
	<a href="/articles.php" class="nav-link text-light">Articles</a>
	<a href="/discounts.php" class="nav-link text-light">Discounts</a>
	<?php
		if($_SESSION['login']['superuser'])
			echo '<a href="/admin_view.php" class="nav-link text-light">Admin View</a>';
	?>
	<!-- Usercontrol (Tooltip + dropdown button) -->
	<div class="ml-auto m-1 dropdown btn-group">
		<!-- Tooltip button part -->
		<a class="btn btn-sm btn-success" data-toggle="tooltip" data-placement="left" data-html="true" title="<?php
			$login = $_SESSION['login'];
			printf("ID: %d<br />Name: %s<br />E-Mail: %s<br />Creation: %s<br />Role: %s", $login['id'], $login['name'], $login['email'], $login['creation'], $login['role']);
		?>" href="/settings.php">
			<?php echo $_SESSION['login']['name']; ?>
		</a>
		<!-- Dropdown button part -->
		<span class="btn btn-sm btn-success dropdown-toggle dropdown-toggle-split" id="user-control-dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
			<span class="sr-only">Toggle Dropdown</span>
		</span>
		<!-- Dropdown menu -->
		<div class="dropdown-menu dropdown-menu-right" aria-labelledby="user-control-dropdown">
			<a class="dropdown-item" href="/shopping_card.php">Shopping Card</a>
			<a class="dropdown-item" href="/orders.php">Orders</a>
			<a class="dropdown-item" href="/logout.php">Logout</a>
		</div>
	</div>
</nav>