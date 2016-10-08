<?php

require_once 'core/init.php';
$sid = session_id();

?>

<!doctype html>

<html lang="en">
<head>
	<meta charset="utf-8">

	<link rel="stylesheet" href="css/reset.css">
	<link rel="stylesheet" href="css/styles.css">

</head>

<body>
	<div class="pagewrapper">
		<div class="sidebar">
			<a href="index.php?page=daily">Daily</a><br>
			<a href="index.php?page=users">Users</a><br>
		</div>
		<div class="contentwrapper">
			<div class="header">
				<div class="logo">
					<img src="images/logo.png"/>
				</div>
				<div class="pagetitle">
					<h3>Daily Balance Sheet</h3>
				</div>
				<div class="split"></div>
				<div class="userinfo">
					<div>
						<span>store #1</span>
						<span>Arrowood Blvd</span>
					</div>
					<div>
						<span>wednesday</span>
						<span>september 14, 2016</span>
					</div>
					<div>
						<span>user id</span>
						<span>kelly</span>
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