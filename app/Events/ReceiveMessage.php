<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use PhpParser\Node\Expr\Array_;

class ReceiveMessage implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public $message ;
    public $from;
    public $to;
    public function __construct($message,$from,$to)
    {
        $this->message=$message;
        $this->from=$from;
        $this->to=$to;

    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): Array
    {
        return[
            new PrivateChannel('receiveMessagePrivate'.$this->to.'.'.$this->from),
            new PrivateChannel('receiveMessagePrivate'.$this->from.'.'.$this->to),

        ];

    }
}
