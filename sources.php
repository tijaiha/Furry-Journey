<?php
require_once 'core/init.php';
require_once 'includes/loggedin.php';
$editing = NULL;
?>

<div class="transactionwrapper">

	<?php
	//error_reporting(0);
	if (!empty($_POST['createSubmit'])) {
		$error = "";
		if (!$_POST['sourceName']) {
			$error .= "First Name";
		}
		if (!$error && !$editing) {
			$source = new Source(escape($_POST['sourceName']));
			if ($_POST['sourceActive']) {
				$_POST['sourceActive'] = 1;
			} else {
				$_POST['sourceActive'] = 0;
			}
			$source->SetActive($_POST['sourceActive']);
			$source->SetName(escape($_POST['sourceName']));
			$source->WriteSource();
		}
	}
	if (!empty($_POST['editSubmit'])) {
		$source = new Source((int) $_POST['editSourceID']);
		$editing = True;
	}
	if (!empty($_POST['updateSubmit'])) {
		$source = new Source((int) $_POST['sourceID']);
		$error = "";
		if (!$_POST['sourceName']) {
			$error .= "Sourcename";
		}
		if (!$error) {
			if ($_POST['sourceActive']) {
				$_POST['sourceActive'] = 1;
			} else {
				$_POST['sourceActive'] = 0;
			}
			$source->SetActive($_POST['sourceActive']);
			$source->SetName(escape($_POST['sourceName']));
			$source->UpdateSource();
		}
	}
	?>

	<form action="index.php?page=sources" method="post" autocomplete="off">
		<input type="hidden" id="sourceID" name="sourceID" value="<?php
		if (!empty($_POST['editSubmit'])) {
			echo $source->GetID();
		}
		?>">
		<table class="spacing">
			<tr>
				<td><label for="sourceActive">Active: </label></td>
				<td><label for="sourceName">Source Name: </label></td>
			</tr>
			<tr>
				<td>
					<input type="checkbox" id="sourceActive" name="sourceActive" <?php
					if (!empty($_POST['editSubmit'])) {
						if($source->GetActive() == 1){
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
					<input type="text" autocomplete="off" id="sourceName" name="sourceName" value="<?php
					if (!empty($_POST['editSubmit'])) {
						echo $source->GetName();
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
			$sourcelist = $db->fetchSources();
			$btd = "<td><p>";
			$etd = "</p></td>";
			//var_dump($sourcelist);
			foreach ($sourcelist as $key => $value) {
				if ($value['active'] == 1) {
					$value['active'] = "Active";
					$row = '<tr class="active">';
				} else {
					$value['active'] = "Inactive";
					$row = '<tr class="inactive">';
				}
				echo
				$row .
				'<form action="index.php?page=sources" method="post" autocomplete="off"><input type="hidden" id="editSourceID" name="editSourceID" value="' . $value['id'] . '">' .
				$btd . $value['active'] . $etd .
				$btd . $value['name'] . $etd .
				'<td><input type="submit" name="editSubmit" value="Edit"></td></form></tr>';
			}
			?>

		</table>
	</form>
</div>

<div class="actionwrapper">

</div>