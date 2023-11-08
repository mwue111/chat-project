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
                date_default_timezone_set("Europe/Madrid");
                // return ChatGResource::make($item);
                return [
                    'friend_first' => $item->first_user != $this->to_user_id ?
                    [
                        'id' => $item->FirstUser->id,
                        'full_name' => $item->FirstUser->name . ' ' . $item->FirstUser->surname,
                        'avatar' =>  $item->FirstUser->avatar ? env('APP_URL') . 'storage/' . $item->FirstUser->avatar : null,
                    ] : null,

                    'friend_second' => $item->second_user ?
                    $item->second_user != $this->to_user_id ?
                        [
                            'id' => $item->SecondUser->id,
                            'full_name' => $item->SecondUser->name . ' ' . $item->SecondUser->surname,
                            'avatar' => $item->SecondUser->avatar ? env('APP_URL') . 'storage/' . $item->SecondUser->avatar : null,
                        ] : null
                    : null,
                    'group_chat' => $item->chat_group_id ?
                        [
                            'id' => $item->ChatGroup->id,
                            'name' => $item->ChatGroup->name,
                            'avatar' => null,

                            'last_message' => $item->ChatGroup->last_message,
                            'last_message_is_mine' => $item->ChatGroup->last_message_user ?
                                    $item->ChatGroup->last_message_user === $this->to_user_id
                                : null,
                            'last_time' => $item->ChatGroup->last_time_created_at,
                            'count_message' => $item->ChatGroup->getCountMessages($this->to_user_id),

                        ] : null,
                    'uniqid' => $item->uniqid,
                    'is_active' => false,
                    'last_message' => $item->last_message,
                    'last_message_is_mine' => $item->last_message_user ? $item->last_message_user === $this->to_user_id : null,
                    'last_time' => $item->last_time_created_at,
                    'count_message' => $item->getCountMessages($this->to_user_id),
                ];
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
