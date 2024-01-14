<?php

namespace App\Models;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class Notification extends Model implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels, HasFactory;

    public function __construct(public $message)
    {
    }

    public function broadcastOn()
    {
        return ['my-channel'];
    }

    public function broadcastAs()
    {
        return 'my-event';
    }
}
