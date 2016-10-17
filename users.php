<?php

require_once 'core/init.php';


$db = new DB;
$query = $db->runQuery("SELECT * FROM permissions");
$permissions = $query->fetchAll(PDO::FETCH_ASSOC);
$editResult = "";

//var_dump($permissions);
?>

<div class="transactionwrapper">

	<?php
	//error_reporting(0);

	if (!empty($_POST['createSubmit'])) {

		$username = escape($_POST['userName']);
		$firstname = escape($_POST['firstName']);
		$lastname = escape($_POST['lastName']);
		
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
			'first_name' => escape($_POST['firstName']),
			'last_name' => escape($_POST['lastName']),
			'username' => escape($_POST['userName']),
			'password' => escape($_POST['password']),
			'permissions_fk' => escape($_POST['role'])
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

	if (!empty($_POST['editSubmit'])) {

		var_dump($_POST['userID']);
		
		$db = new DB;
		$query = $db->runQuery("SELECT 

			first_name, 
			last_name, 
			username, 
			permissions_fk, 
			user_active 

			FROM user

			WHERE id_pk = " . 
			$_POST['userID']

			);
		$editResult = $query->fetchAll(PDO::FETCH_ASSOC);
	}

	?>

	<form action="index.php?page=users" method="post" autocomplete="off">
		<input type="hidden" id="userID" name="userID" value="

		<?php
		if (!empty($_POST['editSubmit'])) {
			echo $_POST['userID'];
		}
		?>

		">
		<table>
			<tr>
				<td><label for="userActive">Active: </label></td>
				<td><label for="userName">Username: </label></td>
				<td><label for="password">Password: </label></td>
				<td><label for="firstName">First Name: </label></td>
				<td><label for="lastName">Last Name: </label></td>
				<td><label for="role">Role: </label></td>
			</tr>
			<tr>
				<td>
					<input type="checkbox" id="userActive" name="userActive" checked>
				</td>
				<td>
					<input type="text" autocomplete="off" id="userName" name="userName" value="<?php
					if (!empty($_POST['editSubmit'])) {
						echo "something";
					}
					?>">
				</td>
				<td>
					<input type="text" autocomplete="off" id="password" name="password" value="">
				</td>
				<td>
					<input type="text" autocomplete="off" id="firstName" name="firstName" value="<?php
					if (!empty($_POST['editSubmit'])) {
						echo "something";
					}
					?>">
				</td>
				<td>
					<input type="text" autocomplete="off" id="lastName" name="lastName" value="<?php
					if (!empty($_POST['editSubmit'])) {
						echo "something";
					}
					?>">
				</td>
				<td>
					<select id= "role" name="role">	

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
				</td>
				<td>
					<input type="submit" name="createSubmit">
				</td>
			</tr>
			<!-- END OF FORM -->

			<!-- START OF DATABASE -->
			<?php
			$db = new DB;
			$userlist = $db->fetchUsers();

			$btd = "<td><p>";
			$etd = "</p></td>";
			
			foreach ($userlist as $key => $value) {

				echo 
				'<tr><form action="index.php?page=users" method="post" autocomplete="off"><input type="hidden" id="userID" name="userID" value="' . 
				$value['id'] . 
				'">' .
				$btd . $value['active'] . 
				$btd . $value['user'] . $etd . 
				$btd . $etd . 
				$btd . $value['first'] . $etd . 
				$btd . $value['last'] . $etd . 
				$btd . $value['role'] . $etd . 
				$btd . 
				'<input type="submit" name="editSubmit" value="Edit">' .
				$etd . 
				'</tr></form>';

			}



			?>
	<!-- 		<tr>
				<td><label for="userActive">Active: </label></td>
				<td><label for="userName">Username: </label></td>
				<td><label for="password">Password: </label></td>
				<td><label for="firstName">First Name: </label></td>
				<td><label for="lastName">Last Name: </label></td>
				<td><label for="role">Role: </label></td>
			</tr> -->


		</table>
	</form>
</div>

<div class="actionwrapper">

</div>

