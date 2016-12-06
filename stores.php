<?php
require_once 'core/init.php';
require_once 'includes/loggedin.php';
$editing = NULL;
$sourcestoreid;

?>

<div class="transactionwrapper">

	<?php
	//error_reporting(0);
	if (!empty($_POST['createSubmit'])) {
	$error = "";
	if (!$_POST['storeName']) {
		$error .= "First Name";
	}
	if (!$error && !$editing) {
		$store = new Store(escape($_POST['storeName']));
if ($_POST['storeActive']) {
	$_POST['storeActive'] = 1;
} else {
	$_POST['storeActive'] = 0;
}
$store->SetActive($_POST['storeActive']);
$store->SetName(escape($_POST['storeName']));
	$store->WriteStore();
}
}
if (!empty($_POST['dupeSources']) OR !empty($_POST['editSubmit']) OR $_POST['sourceAddSubmit'] OR $_POST['sourceRemoveSubmit']) {
	$store = new Store((int) $_POST['editStoreID']);
	$editing = True;
}
if (!empty($_POST['updateSubmit'])) {
	$store = new Store((int) $_POST['storeID']);
	$error = "";
	if (!$_POST['storeName']) {
		$error .= "Storename";
	}
	if (!$error) {
		if ($_POST['storeActive']) {
			$_POST['storeActive'] = 1;
		} else {
			$_POST['storeActive'] = 0;
		}
		$store->SetActive($_POST['storeActive']);
		$store->SetName(escape($_POST['storeName']));
			$store->UpdateStore();
		}
	}

	if (!empty($_POST['employeeAddSubmit'])) {
			if ($_POST['employee'] == "null") {
				$error = "Please select an employee.";
				echo $error;
			} else {
				$userstore = new DB();
				$userstore->AddUserStore($_POST['employee'],$_POST['addEmployeeStoreID']);
			}
		}

		if (!empty($_POST['employeeRemoveSubmit'])) {
	$removeuser = new DB();
	$removeuser->RemoveUserStore($_POST['removeEmployeeID'], $_POST['removeStoreID']);

}

if (!empty($_POST['sourceAddSubmit'])) {
	if ($_POST['source'] == "null") {
		$error = "Please select a source.";
		echo $error;
	} else {
		$storesource = new DB();
		$storesource->AddStoreSource($_POST['editStoreID'],$_POST['source']);
	}
}

if (!empty($_POST['sourceRemoveSubmit'])) {
	$removesource = new DB();
	$removesource->RemoveStoreSource($_POST['editStoreID'], $_POST['removeSourceID']);
}

if (!empty($_POST['dupeSources'])) {
	$dupe = new DB();
	$dupe->DupSources($_POST['dupe'], $_POST['editStoreID']);
}
?>

<form action="index.php?page=stores" method="post" autocomplete="off">
	<input type="hidden" id="storeID" name="storeID" value="<?php
	if (!empty($_POST['editSubmit'])) {
		echo $store->GetID();
	}
	?>">
	<table>
		<tr>
			<td><label for="storeActive">Active: </label></td>
			<td><label for="storeName">Store Name: </label></td>
		</tr>
		<tr>
			<td>
				<input type="checkbox" id="storeActive" name="storeActive" <?php
				if (!empty($_POST['editSubmit'])) {
					if($store->GetActive() == 1){
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
				<input type="text" autocomplete="off" id="storeName" name="storeName" value="<?php
				if (!empty($_POST['dupeSources']) OR !empty($_POST['editSubmit']) OR $_POST['sourceAddSubmit'] OR $_POST['sourceRemoveSubmit']) {
					echo $store->GetName();
				}
				?>">
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
		$btd = "<td><p>";
		$etd = "</p></td>";
		$storeid;
		$activeid = array();



		$storelist = $db->FetchStores();
		foreach ($storelist as $key => $value) {
			if ($value['active'] == 1) {
				$value['active'] = "Active";
				$row = '<tr class="active">';
			} else {
				$value['active'] = "Inactive";
				$row = '<tr class="inactive">';
			}
			$storeid = $value['id'];
			echo
			$row .
			'<form action="index.php?page=stores" method="post" autocomplete="off"><input type="hidden" id="editStoreID" name="editStoreID" value="' . $value['id'] . '">' .
			$btd . $value['active'] . $etd .
			$btd . $value['name'] . $etd .
			'<td><input type="submit" name="editSubmit" value="Edit"></td></form></tr>';

			$employeelist = $db->FetchEmployees($value['id']);
			foreach ($employeelist as $key => $value) {

				$activeid[] = $value['uid'];
				echo
				'<form action="index.php?page=stores" method="post" autocomplete="off">
				<input type="hidden" id="removeStoreID" name="removeStoreID" value="' . $storeid .
				'">
				<input type="hidden" id="removeEmployeeID" name="removeEmployeeID" value="' . $value['uid'] .
				'"><tr class="employees"><td>'
				. $value['first_name'] .
				'</td><td>'
				. $value['last_name'] .
				'</td><td>'
				. $value['role'] .
				'</td><td><input type="submit" name="employeeRemoveSubmit" value="Remove"></td></form></tr>';
			}
			echo '<tr><td><form action="index.php?page=stores" method="post" autocomplete="off">
			<input type="hidden" id="addEmployeeStoreID" name="addEmployeeStoreID" value="' . $storeid .
			'"><td><select id="employee" name="employee"><option selected value="null">Select Employee</option>';
			$db->nonActiveEmployee($activeid);
			unset($activeid);
			echo'</select></td>
			<td><input type="submit" name="employeeAddSubmit" value="Add"></td></tr>';

		}

		?>

	</table>
</form>
</div>

<div class="actionwrapper">

	<table>
		<tr>
			<tr><td><?php
				if (!empty($_POST['dupeSources']) OR !empty($_POST['editSubmit']) OR $_POST['sourceAddSubmit'] OR $_POST['sourceRemoveSubmit']) {
					echo $store->GetName();
				}
				?></td></tr>
			<td><label for="source">Add Sources:</label></td>
			<td><form action="index.php?page=stores" method="post" autocomplete="off"><input type="hidden" id="editStoreID" name="editStoreID"
			value="<?php echo $_POST['editStoreID'] ?>"><select id="dupe" name="dupe">

			<?php
				$db = new DB();
				$dupelist = $db->FetchStores();

				foreach ($dupelist as $key => $value) {
					echo '<option value="' . $value['id'] . '">' . $value['name'] . '</option>' ;
				}

				?>

			</select><input type="submit" name="dupeSources" value="Copy"></form></td>

		</tr>
		<!-- END OF FORM -->

		<!-- PULL FROM DATABASE -->
		<?php
		if (!empty($_POST['dupeSources']) OR !empty($_POST['updateSubmit']) OR !empty($_POST['sourceRemoveSubmit']) OR !empty($_POST['sourceAddSubmit']) OR !empty($_POST['editSubmit'])) {
	$db = new DB;
	$activeid = array();
	$sourcelist = $db->FetchSources($_POST['editStoreID']);
	foreach ($sourcelist as $key => $value) {
		$activeid[] = $value['sid'];
		echo
		'<form action="index.php?page=stores" method="post" autocomplete="off"><input type="hidden" id="editStoreID" name="editStoreID" value="' . $_POST['editStoreID'] .
		'"><input type="hidden" id="removeSourceID" name="removeSourceID" value="' . $value['sid'] .
		'"><tr class="employees"><td>'
		. $value['source'] .
		'</td><td><input type="submit" name="sourceRemoveSubmit" value="Remove"></td></form></tr>';
	}
	echo '<tr><td><form action="index.php?page=stores" method="post" autocomplete="off">
	<input type="hidden" id="editStoreID" name="editStoreID" value="' . $_POST['editStoreID'] .
	'"><td><select id="source" name="source"><option selected value="null">Select Source</option>';
	$db->nonActiveSource($activeid);
	unset($activeid);
	echo'</select></td>
	<td><input type="submit" name="sourceAddSubmit" value="Add"></td></tr>'
	;
}
?>
</table>
</div>