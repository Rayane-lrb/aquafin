<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class OrderStatusUpdated extends Notification
{
    use Queueable;

    public function __construct(public Order $order) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $status  = $this->order->status;
        $product = $this->order->product->name ?? 'product';

        [$icon, $message] = match ($status) {
            'goedgekeurd' => ['✅', "Je bestelling van \"{$product}\" is goedgekeurd!"],
            'geleverd'    => ['📦', "Je bestelling van \"{$product}\" is geleverd!"],
            'afgekeurd'   => ['❌', "Je bestelling van \"{$product}\" werd helaas afgekeurd."],
            default       => ['ℹ️', "Update voor je bestelling van \"{$product}\": {$status}."],
        };

        return [
            'order_id'   => $this->order->id,
            'product'    => $product,
            'status'     => $status,
            'icon'       => $icon,
            'message'    => $message,
        ];
    }
}
