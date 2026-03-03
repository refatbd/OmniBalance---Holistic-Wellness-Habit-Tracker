<?php

namespace App\Notifications; 

use NotificationChannels\WebPush\WebPushMessage;
use NotificationChannels\WebPush\WebPushChannel;
use Illuminate\Notifications\Notification;

class DailyReminder extends Notification
{
    public function via($notifiable)
    {
        return [WebPushChannel::class];
    }

    public function toWebPush($notifiable, $notification)
    {
        return (new WebPushMessage)
            ->title('Time to hydrate! 💧')
            ->icon('/assets/icons/icon-192x192.png')
            ->body('Don\'t forget to log your daily water and meals.');
    }
}