<?php
require_once 'core/init.php';
require_once 'functions/ui.php';
require_once 'includes/loggedin.php';

StoreAuth();

if (isset($_SESSION['user']) && !in_array($_GET['s'], $_SESSION['storeauth'])) {
	//echo $_SESSION['storeauth'][0];
	header('Location: index.php?page=daily&s=' . $_SESSION['storeauth'][0]);
	}
?>

<div class="transactionwrapper">
					<div class="transtop">
						<div class="revenue">
							<div class="startingcontainer">
								<div><h1>Starting Cash</h1></div>
								<div><h1>$10,000</h1></div>
							</div>
							<div class="revenueheader">
								<div><h1>Store Revenue (Cash In)</h1></div>
							</div>
							<div class="revenuesource">
								<div><p>Source</p></div>
								<div><p>Input</p></div>
							</div>
						</div>
						<div class="deductions">
							<div class="endingcontainer">
								<div><h1>Ending Cash</h1></div>
								<div><h1>$250</h1></div>
							</div>
							<div class="deductionsheader">
								<div><h1>Store Revenue (Cash Out)</h1></div>
							</div>
							<div class="deductionssource">
								<div><p>Source</p></div>
								<div><p>Input</p></div>
							</div>
						</div>
					</div>
					<div class="transmid">
						<div class="revenuecontainer">
							<div><h1>Total Revenue</h1></div>
							<div><h1>$11,000</h1></div>
						</div>
						<div class="deductionscontainer">
							<div><h1>Total Deductions</h1></div>
							<div><h1>$20,750</h1></div>
						</div>
					</div>
					<div class="transbottom">
						<div><h1>End of Day Balance</h1></div>
						<div><h1>$0</h1></div>
					</div>
				</div>

				<div class="actionwrapper">
					<!-- <div class="navbuttons">
						<div><h1>Prev</h1></div>
						<div><h1>Next</h1></div>
					</div>

					<div class="calendar">

					</div>

					<div class="savebutton">
						<div><h1>Save Work</h1></div>
						<div><p>Last Saved 5 Min Ago</p></div>
					</div>

					<div class="submitbutton">
						<div><h1>Submit Report</h1></div>
					</div> -->

				</div>