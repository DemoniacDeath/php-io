<?php

class Backdoor
{
    const MESSAGE_TYPE_SEND_TO_CLIENT = 'send_to';
    const MESSAGE_TYPE_SEND_INTO_ROOM = 'send_in';
    const MESSAGE_TYPE_JOIN = 'join';
    const MESSAGE_TYPE_LEAVE = 'leave';

    public function sendTo($clientId, $message, $data)
    {
        $this->send([
            'type' => self::MESSAGE_TYPE_SEND_TO_CLIENT,
            'clientId' => $clientId,
            'message' => $message,
            'data' => $data,
        ]);
    }

    public function sendIn($roomId, $message, $data)
    {
        $this->send([
            'type' => self::MESSAGE_TYPE_SEND_INTO_ROOM,
            'roomId' => $roomId,
            'message' => $message,
            'data' => $data,
        ]);
    }

    public function join($clientId, $roomId)
    {
        $this->send([
            'type' => self::MESSAGE_TYPE_JOIN,
            'clientId' => $clientId,
            'roomId' => $roomId,
        ]);
    }

    public function leave($clientId, $roomId)
    {
        $this->send([
            'type' => self::MESSAGE_TYPE_LEAVE,
            'clientId' => $clientId,
            'roomId' => $roomId,
        ]);
    }

    protected function send($data)
    {
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

        $result = json_decode($result, 1);
        if (!empty($result['error']))
            throw new Exception($result['error']);

        return (!empty($result['status']) && $result['status'] == 'ok');
    }
}
