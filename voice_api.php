<?php

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $message = $_POST['message'];

    $data = json_encode([
        "message" => $message
    ]);

    $ch = curl_init("http://127.0.0.1:8000/chat");

    curl_setopt($ch, CURLOPT_POST, true);

    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json"
    ]);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);

    curl_close($ch);

    echo $response;
}
?>