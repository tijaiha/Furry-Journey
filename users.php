<?php

require_once 'core/init.php';
require_once 'includes/loggedin.php';


$db = new DB;
$query = $db->runQuery("SELECT * FROM permissions");
$permissions = $query->fetchAll(PDO::FETCH_ASSOC);
$editing = NULL;


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

		if (!$error && !$editing) {

			$user = new User(escape($_POST['userName']));

			if ($_POST['userActive']) {
				$_POST['userActive'] = 1;
			} else {
				$_POST['userActive'] = 0;
			}

			$user->SetActive($_POST['userActive']);
			$user->SetFirst(escape($_POST['firstName']));
			$user->SetLast(escape($_POST['lastName']));
			$user->SetUser(escape($_POST['userName']));
			$user->SetPass(escape($_POST['password']));
			$user->SetPerm(escape($_POST['role']));
			$user->WriteUser();
		}

	}

	if (!empty($_POST['editSubmit'])) {

		$user = new User((int) $_POST['editUserID']);
		$editing = True;
	}

	if (!empty($_POST['updateSubmit'])) {

		$user = new User((int) $_POST['userID']);

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

			if ($_POST['userActive']) {
				$_POST['userActive'] = 1;
			} else {
				$_POST['userActive'] = 0;
			}

			$user->SetActive($_POST['userActive']);
			$user->SetFirst(escape($_POST['firstName']));
			$user->SetLast(escape($_POST['lastName']));
			$user->SetUser(escape($_POST['userName']));
			$user->SetPass(escape($_POST['password']));
			$user->SetPerm(escape($_POST['role']));
			$user->UpdateUser();
		}
	}
	?>

	<form action="index.php?page=users" method="post" autocomplete="off">
		<input type="hidden" id="userID" name="userID" value="<?php
		if (!empty($_POST['editSubmit'])) {
			echo $user->GetID();
		}
		?>">
		<table class="spacing">
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
						if($user->GetActive() == 1){
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
					<?php if(!$editing) {
						echo '<input type="submit" name="createSubmit" value="Create">';
					} else {
						echo '<input type="submit" name="updateSubmit" value="Update">';
					}?>
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

				if ($value['active'] == 1) {
					$value['active'] = "Active";
					$row = '<tbody class="row"><tr class="user active"><div>';
				} else {
					$value['active'] = "Inactive";
					$row = '<tbody class="row"><tr class="user inactive"><div>';
				}

				echo
				$row .
				'<form action="index.php?page=users" method="post" autocomplete="off"><input type="hidden" id="editUserID" name="editUserID" value="' . $value['id'] . '">' .
				$btd . $value['active'] . $etd .
				$btd . $value['user'] . $etd .
				$btd . $value['pass'] . $etd .
				$btd . $value['first'] . $etd .
				$btd . $value['last'] . $etd .
				$btd . $value['role'] . $etd .
				'<td><input type="submit" name="editSubmit" value="Edit"></td></form></div></tr></tbody>';
			}
			?>

		</table>
	</form>
</div>

<div class="actionwrapper">

</div>