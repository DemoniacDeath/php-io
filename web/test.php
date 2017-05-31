<?php

require_once '../Backdoor.php';

$clientId = $_REQUEST['clientId'];

$bd = new Backdoor;

$bd->join($clientId, 'test_room');
$bd->sendTo($clientId, 'test', ['text' => 'ololo']);
$bd->sendIn('test_room', 'test', ['text' => 'ololoevo']);
