<?php

require_once(__DIR__ . '/.ignore.custom-prefs-authentication.inc.php');
require_once('smcanvaslib/config.inc.php');
require_once('smcanvaslib/include/canvas-api.inc.php');
require_once('smcanvaslib/include/cache.inc.php');

if (isset($_REQUEST['appointment_group_id']) && is_numeric($_REQUEST['appointment_group_id'])) {
	$appointmentGroup = getCache('key', "scheduler-roster-{$_REQUEST['appointment_group_id']}", 'data');
	if (!$appointmentGroup) {
		$api = new CanvasApiProcess(CANVAS_API_URL, CANVAS_API_TOKEN);
		$appointmentGroup = $api->get(
			"appointment_groups/{$_REQUEST['appointment_group_id']}",
			array(
				'include[]' => 'child_events',
			)
		);
		setCache('key', "scheduler-roster-{$_REQUEST['appointment_group_id']}", 'data', $appointmentGroup);
	}
	$startAt = new DateTime($appointmentGroup['start_at'], new DateTimeZone('Etc/Zulu'));
	$endAt = new DateTime($appointmentGroup['end_at'], new DateTimeZone('Etc/Zulu'));
	$startFormat = 'l, F j, g:i a';
	$whereAmI = new DateTime();
	$startAt->setTimezone($whereAmI->getTimezone());
	$endAt->setTimezone($whereAmI->getTimezone());
	if ($endAt->diff($startAt, true)->days > 0) {
		$endFormat = $startFormat;
	} else {
		$endFormat = 'g:i a';
	}
?>
<html>
	<head>
		<title><?= $appointmentGroup['title'] ?> Roster</title>
		<style>
			body {
				font-family: Helvetica, Arial, sans-serif;
				font-size: 10pt;
			}
		</style>
	</head>
	<body>
		<h1><?= $appointmentGroup['title']; ?></h1>
		<h3><?= $startAt->format($startFormat) ?> &ndash; <?= $endAt->format($endFormat) ?></h2>
		<h3><?= $appointmentGroup['location_name'] ?><br/><?= $appointmentGroup['location_address'] ?></h3>
		<p><?= $appointmentGroup['description'] ?></p>
		<h3>Roster</h3>
		<p>As of <?= $whereAmI->format($startFormat) ?></p>
		<?php
			
			foreach($appointmentGroup['appointments'] as $appointment) {
				$startAt = new DateTime($appointment['start_at'], new DateTimeZone('Etc/Zulu'));
				$endAt = new DateTime($appointment['end_at'], new DateTimeZone('Etc/Zulu'));
				$startAt->setTimezone($whereAmI->getTimezone());
				$endAt->setTimezone($whereAmI->getTimezone());
				if ($endAt->diff($startAt, true)->days > 0) {
					$endFormat = $startFormat;
				} else {
					$endFormat = 'g:i a';
				}
				if (count($appointmentGroup['appointments']) > 1) {
					echo "\t\t<h4>" . $startAt->format($startFormat) . ' &ndash; ' . $endAt->format($endFormat) . "</h4>\n";
				}
				
				echo "\t\t<ol>\n";
				foreach($appointment['child_events'] as $event) {
					$signUp = new DateTime($event['created_at'], new DateTimeZone('Etc/Zulu'));
					$signUp->setTimezone($whereAmI->getTimezone());
					echo "\t\t\t<li>{$event['user']['short_name']} (signed up " . $signUp->format('n/j @ g:i a') . ")</li>\n";
				}
				echo "\t\t</ol>\n";
				
			}
			
		?>
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