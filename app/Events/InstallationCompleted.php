<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class InstallationCompleted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $installation;

    public function __construct($installation)
    {
        $this->installation = $installation;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('installations');
    }
}
