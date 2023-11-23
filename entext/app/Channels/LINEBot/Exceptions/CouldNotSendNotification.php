<?php

namespace App\Channels\LINEBot\Exceptions;

use LINE\LINEBot\MessageBuilder\TextMessageBuilder;
use LINE\LINEBot\Response;

class CouldNotSendNotification extends \Exception
{
    public static function invalidReceiver()
    {
        return new static(
            'The notifiable did not have a receiving LINE userId. Add a routeNotificationForLine
            method or one of the conventional attributes to your notifiable.'
        );
    }

    public static function invalidMessageObject($message)
    {
        $type = is_object($message) ? get_class($message) : gettype($message);

        return new static(
            'Notification was not sent. The message should be a instance of `'.TextMessageBuilder::class."` and a `{$type}` was given."
        );
    }

    public static function failedToPushMessage(Response $response)
    {
        return new static(
            "Failed to send LINE messages\n".
            "httpStatus: {$response->getHTTPStatus()}\n".
            "message: {$response->getJSONDecodedBody()['message']}"
        );
    }
}
