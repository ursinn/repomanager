<?php
/**
 *  Update deb mirror repo
 */
$rawParams['action'] = 'update';
$rawParams['snap-id'] = '1';
$rawParams['env-id'] = '';
$rawParams['arch'] = ['amd64', 'armhf'];
$rawParams['env'] = 'pprd';
$rawParams['gpg-check'] = 'false';
$rawParams['gpg-sign'] = 'true';
$rawParams['schedule'] = [
    'scheduled' => 'false',
    'schedule-type' => '',
    'schedule-frequency' => '',
    'schedule-day' => [],
    'schedule-date' => '',
    'schedule-time' => '',
    'schedule-notify-error' => '',
    'schedule-notify-success' => '',
    'schedule-reminder' => [],
    'schedule-recipient' => ['']
];

$stmt = $this->db->prepare("INSERT INTO tasks (Type, Raw_params, Status) VALUES ('immediate', :rawParams, 'queued');");
$stmt->bindParam(':rawParams', json_encode($rawParams));
$stmt->execute();
