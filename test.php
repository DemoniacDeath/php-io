<?php

$data = [
    'test' => 666,
];

$body = json_encode($data);

$ch = curl_init('http://localhost:8080/backdoor');
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/json',
    'Content-Length: ' . strlen($body))
);

$result = curl_exec($ch);

echo $result;
