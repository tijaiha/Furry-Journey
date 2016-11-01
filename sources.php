<?php 
require_once 'core/init.php';
require_once 'functions/ui.php';
//require_once 'includes/loggedin.php';

?>

<div class="transactionwrapper">
	<?php
	//error_reporting(0);


	// If create source submit is pressed
	if (!empty($_POST['createSubmit'])) {

		$error = "";

		// Check if field is populated : throw an eror.
		if (!$_POST['sourceName']) {
			$error .= "Source Name";
		}

		// Create new source object using submitted name
		$source = new Source(escape($_POST['sourceName']));


		// To-Do:
		// THROW ERROR IF source EXISTS!
		//


		// If there are no results with the source name
		if (!$source->GetID()){

			// Check for form population errors
			if (!$error) {

				// Populate $_POST['sourceActive'] with a 1 or 0 
				// (check box defaults to "on" or null)
				if ($_POST['sourceActive']) {
					$_POST['sourceActive'] = 1;
				} else {
					$_POST['sourceActive'] = 0;
				}

				// Set source class properties to values from POST
				$source->SetActive($_POST['sourceActive']);
				$source->SetName(escape($_POST['sourceName']));
				
				// Write the source to database
				$source->WriteSource();

			}
		} 


	}

	// If edit source is pressed
	if (!empty($_POST['editSubmit'])) {

		// Create new source object populated with
		// source ID passed via POST
		$source = new Source((int) $_POST['sourceID']);

		var_dump($source);

	}

	?>

	<form action="index.php?page=sources" method="post" autocomplete="off">
		<input type="hidden" id="sourceID" name="sourceID" value="<?php

		// Populate hidden form with source ID that is being edited
		if (!empty($_POST['editSubmit'])) {
			echo $source->GetID();
		}
		?>">

		<table>
			<tr>
				<td><label for="sourceActive">Active: </label></td>
				<td><label for="sourceName">Source Name: </label></td>
			</tr>
			<tr>
				<td>
					<input type="checkbox" id="sourceActive" name="sourceActive" <?php

					// Populate form check box if edited source is active
					// else default the box to checked for creating new source
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

					// Populate source name form field with edited source 
					if (!empty($_POST['editSubmit'])) {
						echo $source->GetName();
					}
					?>">
				</td>
				<td>
					<input type="submit" name="createSubmit">
				</td>
			</tr>
			<!-- END OF FORM -->

			<!-- PULL FROM DATABASE -->
			<?php

			// Create database object, connect, and fetch a list of all sources.
			$db = new DB();
			$sourcelist = $db->fetchSources();

			// Vars for creating html table
			$btd = "<td><p>";
			$etd = "</p></td>";

			// Create and populate table with all sources.
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
				'<form action="index.php?page=sources" method="post" autocomplete="off"><input type="hidden" id="sourceID" name="sourceID" value="' . $value['id'] . '">' .
				$btd . $value['active'] . $etd .
				$btd . $value['name'] . $etd . 
				'<td><input type="submit" name="editSubmit" value="Edit"></td></form></tr>';

			}?>

		</table>
	</form>
</div>

<div class="actionwrapper">

</div>