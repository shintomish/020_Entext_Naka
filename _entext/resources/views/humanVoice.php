<?php
require_once './vendor/autoload.php';
use Twilio\TwiML\VoiceResponse;

$response = new VoiceResponse();
$response->dial('+1808032689820');
// $response->play('https://api.twilio.com/cowbell.mp3', ['loop' => 10]);
$response->say('Goodbye');

echo $response;
