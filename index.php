<?php

require_once 'core/init.php';
require_once 'functions/ui.php';
require_once 'includes/loggedin.php';

?>

<!doctype html>

<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Argus Check Cashing - <?php SetPageTitle($_GET["page"]);?></title>
	<link rel="stylesheet" href="css/reset.css">
	<link rel="stylesheet" href="css/styles.css">

</head>

<body>
	<div class="pagewrapper">
		<div class="sidebar">
			<div><?php CreateMenu($_SESSION['role'], $_SESSION['user_id']);?></div>
		</div>
		<div class="contentwrapper">
			<div class="header">
				<div class="logo">
					<img src="images/logo.png"/>
				</div>
				<div class="pagetitle">
					<h3><?php SetPageTitle($_GET["page"]);?></h3>
				</div>
				<div class="split"></div>
				<div class="userinfo">
					<div>
						<span><?php if (isset($_GET['s'])) {echo "store #" . $_GET['s'];}?></span>
						<span><?php echo StoreName($_GET['s']);?></span>
					</div>
					<div>
						<span><?php echo date("l");?></span>
						<span><?php echo date("F j, Y");?></span>
					</div>
					<div>
						<span>user</span>
						<span><?php echo $_SESSION['first_name'] . " " . $_SESSION['last_name'];?></span>
					</div>
				</div>
			</div>
			<div class="headershadow"></div>
			<div class="content">
				<?php
					if(isset($_GET["page"])) {
						include $_GET["page"] . ".php";
					} else {
						include "daily.php";
					}
				?>
			</div>
		</div>
	</div>
</body>
</html>