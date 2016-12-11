<?php
require_once 'core/init.php';
require_once 'functions/ui.php';
require_once 'includes/loggedin.php';

StoreAuth();
if (isset($_SESSION['user']) && !in_array($_GET['s'], $_SESSION['storeauth'])) {
	header('Location: index.php?page=daily&s=' . $_SESSION['storeauth'][0]);
}

/*starting cash + cash in - cash out = ending cash = next day starting cash*/

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
				<form action="index.php?page=daily&s=<?php echo $_GET['s']; ?>" method="post" autocomplete="off">
				<?php
				$daily = new Daily($_GET['s'], $_SESSION['user_id']);
				$daily->UpdateTrans();
				$daily->InitDay();
				echo $daily->GetRevenue();
				?>
			</div>
			<div class="deductions">
				<div class="endingcontainer">
					<div><h1>Ending Cash</h1></div>
					<div><h1>$250</h1></div>
				</div>
				<div class="deductionsheader">
					<div><h1>Store Revenue (Cash Out)</h1></div>
				</div>
				<?php
				echo $daily->GetDeduction();
				?>
			</div>
		</div>
		<div class="transmid">
			<div class="revenuecontainer">
				<div><h1>Total Revenue</h1></div>
				<div><h1><?php $daily->TotalRev($_GET['s']); ?></h1></div>
			</div>
			<div class="deductionscontainer">
				<div><h1>Total Deductions</h1></div>
				<div><h1><?php $daily->TotalDed($_GET['s']); ?></h1></div>
			</div>
		</div>
		<div class="transbottom">
			<div><h1>End of Day Balance</h1></div>
			<div><h1><?php $daily->TotalDed($_GET['s']); ?></h1></div>
		</div>
</div>

<div class="actionwrapper">
					 <div class="navbuttons">
						<!-- <div><h1>Prev</h1></div>
						<div><h1>Next</h1></div> -->
					</div>

					<div class="calendar">

					</div>

					<div class="savebutton">
						<div><input type="submit" name="formSave" value="Save"></div>
						<div><p><?php echo $daily->SavedLast($_GET['s'], $daily->GetUser());?></p></div>
					</div>

					<div class="submitbutton">
						<div><input type="submit" name="formSubmit" value="Submit"></div>
					</div>

				</div>
	</form>