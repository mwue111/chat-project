<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

use App\Models\Chat\ChatRoom;
use App\Http\Resources\Chat\ChatGResource;

class RefreshMyChatRoom implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $to_user_id;

    /**
     * Create a new event instance.
     */
    public function __construct($to_user_id)
    {
        $this->to_user_id = $to_user_id;
    }

    public function broadcastWith() {
        $chatrooms = ChatRoom::where('first_user', $this->to_user_id)
                    ->orWhere('second_user', $this->to_user_id)
                    ->orderBy('last_at', 'desc')
                    ->get();

        return [
            'chatrooms' => $chatrooms->map(function($item) {
                return ChatGResource::make($item);
            })
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
            new PrivateChannel('chat.refresh.room.' . $this->to_user_id),
        ];
    }
}
