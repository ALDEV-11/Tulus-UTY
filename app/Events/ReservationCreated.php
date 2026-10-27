<?php

namespace App\Events;

use App\Models\Reservasi;
use App\Services\ReservationNotificationService;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ReservationCreated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Reservasi $reservasi)
    {
    }

    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel('admin.reservations');
    }

    public function broadcastAs(): string
    {
        return 'reservation.created';
    }

    public function broadcastWith(): array
    {
        $service = app(ReservationNotificationService::class);
        $payload = $service->payload($this->reservasi);
        $payload['pending_count'] = $service->pendingCount();

        return $payload;
    }
}
