<?php

namespace App\Channels\LINEBot;

use App\Channels\LINEBot\Exceptions\CouldNotSendNotification;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Notifications\Events\NotificationFailed;
use Illuminate\Notifications\Notification;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;

class LineChannel
{
    /**
     * @var Dispatcher
     */
    protected $events;

    public function __construct(Dispatcher $events)
    {
        $this->events = $events;
    }

    /**
     * Send the given notification.
     *
     * @return \LINE\LINEBot\Response
     */
    public function send($notifiable, Notification $notification)
    {
        try {
            $destination = $this->getDestination($notifiable, $notification);
            $message = $this->getMessage($notifiable, $notification);

            $response = \LINEBot::pushMessage($destination, $message);
            if (!$response->isSucceeded()) {
                throw CouldNotSendNotification::failedToPushMessage($response);
            }
            return $response;
        } catch (\Exception $e) {
            \Log::error($e);

            $event = new NotificationFailed(
                $notifiable,
                $notification,
                'line',
                ['message' => $e->getMessage(), 'exception' => $e]
            );
            $this->events->dispatch($event);
        }
    }

    /**
     * Get the LINE userId to send a notification to.
     *
     * @throws CouldNotSendNotification
     */
    protected function getDestination($notifiable, Notification $notification)
    {
        if ($to = $notifiable->routeNotificationFor('line', $notification)) {
            return $to;
        }

        return $this->guessDestination($notifiable);
    }

    /**
     * Try to get the LINE userId from some commonly used attributes for that.
     *
     * @throws CouldNotSendNotification
     */
    protected function guessDestination($notifiable)
    {
        $commonAttributes = ['user_id', 'line_id', 'line_user_id'];
        foreach ($commonAttributes as $attribute) {
            if (isset($notifiable->{$attribute})) {
                return $notifiable->{$attribute};
            }
        }

        throw CouldNotSendNotification::invalidReceiver();
    }

    /**
     * Get the LINEBot TextMessageBuilder object.
     *
     * @throws CouldNotSendNotification
     */
    protected function getMessage($notifiable, Notification $notification): TextMessageBuilder
    {
        $message = $notification->toLine($notifiable);
        if (is_string($message)) {
            return new TextMessageBuilder($message);
        }

        if ($message instanceof TextMessageBuilder) {
            return $message;
        }

        throw CouldNotSendNotification::invalidMessageObject($message);
    }
}
