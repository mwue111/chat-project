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

    //formato en el que se envía el mensaje al front
    public function broadcastWith() {
        return [
            'id' =>  $this->chat->id,
            'sender' => [
                'id' =>  $this->chat->FromUser->id,
                'full_name' =>  $this->chat->FromUser->name . ' ' .  $this->chat->FromUser->surname,
                'avatar' =>  $this->chat->FromUser->avatar ? env('APP_URL') . 'storage/' .  $this->chat->FromUser->avatar : null,
            ],
            'message' =>  $this->chat->message,
            // 'message' =>  $this->chat->message ? $this->chat->message : null,
            'file' =>  $this->chat->ChatFile ? [    //función ChatFile del modelo chat
                'id' => $this->chat->ChatFile->id,
                'file_name' => $this->chat->ChatFile->file_name,
                'type' => $this->chat->ChatFile->type,
                'resolution' => $this->chat->ChatFile->resolution,
                'size' => $this->chat->ChatFile->size,
                'uniqid' => $this->chat->ChatFile->uniqid,
                'file' => env('APP_URL') . 'storage/' . $this->chat->ChatFile->file,
                'created_at' => $this->chat->ChatFile->created_at->format('Y-m-d h:i A'),
            ] : null,
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
