<?php

require_once 'core/init.php';

$db = new DB;
$query = $db->runQuery("SELECT * FROM permissions");
$permissions = $query->fetchAll(PDO::FETCH_ASSOC);

//var_dump($permissions);
?>

<div class="transactionwrapper">

	<?php
	error_reporting(0);

	$username = $_POST['userName'];
	$firstname = $_POST['firstName'];
	$lastname = $_POST['lastName'];

	if($_POST['formSubmit'] == "Submit") {
		$error = "";

		if (!$_POST['firstName']) {
			$error .= "First Name";
		}

		if (!$_POST['lastName']) {
			$error .= "Last Name";
		}

		if (!$_POST['userName']) {
			$error .= "Username";
		}

		if (!$_POST['password']) {
			$error .= "Password";
		}

		if (!$error) {

			$user = [
			'first_name' => $_POST['firstName'],
			'last_name' => $_POST['lastName'],
			'username' => $_POST['userName'],
			'password' => $_POST['password'],
			'permissions_fk' => $_POST['role']
			];
			//var_dump($user);

			try {
				$db = new DB;
				$valid = $db->userExists($user['username']);

				if (!$valid) {
					$query = $db->insert("user", $user);
				} else {
					echo "User " . $user['username'] . " exists!";
				}

			} catch (Exception $e) {
				echo 'Caught exception: ',  $e->getMessage(), "\n";
			}
		} else {
			echo "Required:<br>" . $error;
		}
	}
	?>

	<form action="index.php?page=users" method="post" autocomplete="off">
		<label for="userName">Username: </label>
		<input type="text" autocomplete="off" id="userName" name="userName" value=""><br>
		<label for="password">Password: </label>
		<input type="text" autocomplete="off" id="password" name="password" value=""><br>
		<label for="firstName">First Name: </label>
		<input type="text" autocomplete="off" id="firstName" name="firstName" value=""><br>
		<label for="lastName">Last Name: </label>
		<input type="text" autocomplete="off" id="lastName" name="lastName" value=""><br>
		<label for="role">Role: </label>
		<select id= "role" name="role"><br>
			<?php
			foreach ($permissions as $key => $value) {
				if($value['permission_name'] == "Clerk") { 
					echo '<option selected value="' . 
					$value['id_pk'] . 
					'">' . 
					$value['permission_name'] . 
					'</option>';
				} else {
					echo '<option value="' . 
					$value['id_pk'] . 
					'">' . 
					$value['permission_name'] . 
					'</option>';
				}
			}
			?>
		</select>
		<input type="submit" name="formSubmit">

	</form>
</div>

<div class="actionwrapper">

	<?php
	$db = new DB;
	$userlist = $db->fetchUsers();

	$bdiv = "<div><p>";
	$ediv = "</p></div>";

	foreach ($userlist as $key => $value) {

		echo 	
		$bdiv . $value['id'] . $ediv . 
		$bdiv . $value['user'] . $ediv . 
		$bdiv . $value['first'] . $ediv . 
		$bdiv . $value['last'] . $ediv . 
		$bdiv . $value['role'] . $ediv;

	}

//var_dump($userlist);

	?>

</div>

