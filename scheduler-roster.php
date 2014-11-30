<?php

require_once(__DIR__ . '/.ignore.custom-prefs-authentication.inc.php');
require_once('smcanvaslib/config.inc.php');
require_once('smcanvaslib/include/canvas-api.inc.php');

if (isset($_REQUEST['appointment_group_id']) && is_numeric($_REQUEST['appointment_group_id'])) {
	$api = new CanvasApiProcess(CANVAS_API_URL, CANVAS_API_TOKEN);
	$appointmentGroup = $api->get("appointment_groups/{$_REQUEST['appointment_group_id']}");
	$roster = $api->get("appointment_groups/{$_REQUEST['appointment_group_id']}/users");
?>
<html>
	<head>
	</head>
	<body onload="window.print();">
		<h1><?= $appointmentGroup['title']; ?></h1>
		<h3><?= $appointmentGroup['location_name'] ?><br/><?= $appointmentGroup['location_address'] ?></h3>
		<p><?= $appointmentGroup['description'] ?></p>
		<ol>
			<?php
			
			do {
				foreach($roster as $signup) {
					echo "\t\t\t<li>{$signup['short_name']}</li>\n";
				}
			} while ($roster = $api->nextPage());
				
			?>
		</ol>
	</body>
</html>

<?php } else {
	header('Content-Type: application/javascript');
?>

function stmarks_addSchedulerRosterLink(appointmentGroup) {
	appointmentGroup = appointmentGroup[0];
	appointmentGroupId = appointmentGroup.getAttribute('data-appointment-group-id');
	signups = appointmentGroup.getElementsByClassName('ag-x-of-x-signed-up')[0];
	signups.innerHTML = signups.innerHTML + ' &mdash; <a href="<?= APP_URL . '/' . basename(__FILE__) ?>?appointment_group_id=' + appointmentGroupId + '" target="_blank">Print Roster</a>';
}

function stmarks_schedulerRoster() {
	stmarks_waitForDOMByClassName(/calendar2#view_name=scheduler/,'appointment-group-item active', stmarks_addSchedulerRosterLink);
}
<?php } ?>