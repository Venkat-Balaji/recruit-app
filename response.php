<?php
require "vendor/autoload.php";

use GeminiAPI\Client;
use GeminiAPI\Resources\Parts\TextPart;

$data = json_decode(file_get_contents("php://input"));

$text = $data->text;

$client = new Client("AIzaSyAPoYC30oi4qC-q1H1SPc-Iw5bw3IeIZ8U");

$response = $client->geminiPro()->generateContent(
    new TextPart($text)
);

echo $response->text();
?>
