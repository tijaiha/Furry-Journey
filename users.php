<?php

require_once 'core/init.php';


$db = new DB;
$query = $db->runQuery("SELECT * FROM permissions");
$permissions = $query->fetchAll(PDO::FETCH_ASSOC);
$editResult = "";

?>

<div class="transactionwrapper">

	<?php
	//error_reporting(0);

	if (!empty($_POST['createSubmit'])) {

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

		$user = new User(escape($_POST['userName']));

		// User exists
		if ($user->GetResult){

		}

		// User does not exist
		if (!$user->GetResult){
			if (!$error) {

				$user->SetActive($_POST['userActive']);
				$user->SetFirst(escape($_POST['firstName']));
				$user->SetLast(escape($_POST['lastName']));
				$user->SetUser(escape($_POST['userName']));
				$user->SetPass(escape($_POST['password']));
				$user->SetPerm(escape($_POST['role']));
				$user->WriteUser();

			}
		}




	}

	if (!empty($_POST['editSubmit'])) {
		$user = new User((int) $_POST['userID']);
	}

	?>

	<form action="index.php?page=users" method="post" autocomplete="off">
		<input type="hidden" id="userID" name="userID" value="<?php
		if (!empty($_POST['editSubmit'])) {
			echo $user->GetID();
		}
		?>">
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
					<input type="checkbox" id="userActive" name="userActive" <?php
						if (!empty($_POST['editSubmit'])) {
							if($user->GetActive() == "on"){
								echo "checked";
							} else {
								echo "";
							}
						} else {
							echo "checked";
						}					

					 ?>>
				</td>
				<td>
					<input type="text" autocomplete="off" id="userName" name="userName" value="<?php
					if (!empty($_POST['editSubmit'])) {
						echo $user->GetUser();
					}
					?>">
				</td>
				<td>
					<input type="text" autocomplete="off" id="password" name="password" value="<?php
					if (!empty($_POST['editSubmit'])) {
						echo $user->GetPass();
					}
					?>">
				</td>
				<td>
					<input type="text" autocomplete="off" id="firstName" name="firstName" value="<?php
					if (!empty($_POST['editSubmit'])) {
						echo $user->GetFirst();
					}
					?>">
				</td>
				<td>
					<input type="text" autocomplete="off" id="lastName" name="lastName" value="<?php
					if (!empty($_POST['editSubmit'])) {
						echo $user->GetLast();
					}
					?>">
				</td>
				<td>
					<select id= "role" name="role">	

						<?php

						if (!empty($_POST['editSubmit'])) {

							foreach ($permissions as $key => $value) {
								if($value['id_pk'] == $user->GetPerm()) { 

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

						} else {
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
						}
					?>

				</select>
			</td>
			<td>
				<input type="submit" name="createSubmit">
			</td>
		</tr>
		<!-- END OF FORM -->

		<!-- PULL FROM DATABASE -->
		<?php
		$db = new DB;
		$userlist = $db->fetchUsers();

		$btd = "<td><p>";
		$etd = "</p></td>";

			//var_dump($userlist);

		foreach ($userlist as $key => $value) {

			echo 
			'<tr><form action="index.php?page=users" method="post" autocomplete="off"><input type="hidden" id="userID" name="userID" value="' . $value['id'] . '">' .
			$btd . $value['active'] . 
			$btd . $value['user'] . $etd . 
			$btd . $etd . 
			$btd . $value['first'] . $etd . 
			$btd . $value['last'] . $etd . 
			$btd . $value['role'] . $etd . 
			$btd . 
			'<input type="submit" name="editSubmit" value="Edit">' .
			$etd . 
			'</form></tr>';

		}



		?>

	</table>
</form>
</div>

<div class="actionwrapper">

</div>

