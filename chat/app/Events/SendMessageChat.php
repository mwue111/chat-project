<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SendMessageChat implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $chat;

    //el constructor recibe un objeto del mdelo Chat
    public function __construct($chat)
    {
        $this->chat = $chat;
    }

    //formato en el que se recibe el mensaje
    public function broadcastWith() {
        return [
            'id' =>  $this->chat->id,
            'sender' => [
                'id' =>  $this->chat->FromUser->id,
                'full_name' =>  $this->chat->FromUser->name . ' ' .  $this->chat->FromUser->surname,
                'avatar' =>  $this->chat->FromUser->avatar ? env('APP_URL') . 'storage/' .  $this->chat->FromUser->avatar : null,
            ],
            'message' =>  $this->chat->message,
            // 'file' =>  $this->chat->file,
            'read_at' =>  $this->chat->read_at,
            'time' =>  $this->chat->created_at->diffForHumans(),
            'created_at' =>  $this->chat->created_at,
        ];
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('chat.room.' . $this->chat->ChatRoom->uniqid),
        ];
    }
}
