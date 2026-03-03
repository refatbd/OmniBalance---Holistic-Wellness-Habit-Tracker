<?php

namespace App\Notifications;

use App\Models\Item;
use NotificationChannels\WebPush\WebPushMessage;
use NotificationChannels\WebPush\WebPushChannel;
use Illuminate\Notifications\Notification;

class LowStockAlert extends Notification
{
    public $item;

    /**
     * Create a new notification instance.
     * We pass the Item model so we can display its name and stock in the alert.
     */
    public function __construct(Item $item)
    {
        $this->item = $item;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable)
    {
        return [WebPushChannel::class];
    }

    /**
     * Get the web push representation of the notification.
     */
    public function toWebPush($notifiable, $notification)
    {
        return (new WebPushMessage)
            ->title('Low Stock Alert! ⚠️')
            ->icon('/assets/icons/icon-192x192.png')
            ->body("You are running low on {$this->item->name}. Only {$this->item->stock} {$this->item->unit} left!")
            ->action('View Inventory', '/items');
    }
}