<?php

namespace AbdullahFaqeir\LaravelAgoraApi\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DispatchAgoraCall implements ShouldBroadcast
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public $agoraChannel;

    public $senderId;

    public $senderDisplayName;

    public $recipientId;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($agoraChannel, $senderId, $senderDisplayName, $recipientId)
    {
        $this->agoraChannel = $agoraChannel;
        $this->senderId = $senderId;
        $this->senderDisplayName = $senderDisplayName;
        $this->recipientId = $recipientId;
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn()
    {
        return new PresenceChannel(config('laravel-agora-api.channel_name'));
    }
}
