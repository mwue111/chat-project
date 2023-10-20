<?php

namespace App\Http\Controllers\Chat;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Chat\ChatRoom;
use App\Models\Chat\Chat;
use App\Http\Resources\Chat\ChatGResource;

class ChatController extends Controller
{
    public function __construct() {
        $this->middleware('auth:api');
    }

    //función para iniciar una conversación individual
    public function startChat(Request $request) {

        date_default_timezone_set("Europe/Madrid");
        //comprobar que la conversación es con otro usuario o con uno mismo
        if($request->to_user_id == auth('api')->user()->id){
            return response()->json(['error' => 'No puedes iniciar un chat contigo mismo.']);
        }

        //comprobar si existe una sala de chat con estas dos personas

        //whereIn permite pasar varias condiciones en un array: permite comprobar si la sala de chat existía previamente con el first_user siendo quien envía o quien recibe el mensaje
        $chatRoomExists = ChatRoom::whereIn('first_user', [$request->to_user_id, auth('api')->user()->id])
                                ->whereIn('second_user', [$request->to_user_id, auth('api')->user()->id])
                                ->count();

        if($chatRoomExists > 0) {
            //cargar sala de chat existente
            $chatRoom = ChatRoom::whereIn('first_user', [$request->to_user_id, auth('api')->user()->id])
                                ->whereIn('second_user', [$request->to_user_id, auth('api')->user()->id])
                                ->first();

            Chat::where('user_id', $request->to_user_id)    //en chats el user_id siempre es el destinatario
                ->where('chat_room_id', $chatRoom->id)
                ->where('read_at', null)
                ->update(['read_at' => now()]);

            $chats = Chat::where('chat_room_id', $chatRoom->id)
                        ->orderBy('created_at', 'desc')
                        ->paginate(10);

            $data = [];
            $data['room_id'] = $chatRoom->id;
            $data['room_uniqid'] = $chatRoom->uniqid;
            $to_user = User::find($request->to_user_id);
            $data['user'] = [
                'id' => $to_user->id,
                'full_name' => $to_user->name . ' ' . $to_user->surname,
                'avatar' => $to_user->avatar ? env('APP_URL') . 'storage/' . $to_user->avatar : 'https://cdn-icons-png.flaticon.com/512/3135/3135715.png',
            ];

            if(count($chats) > 0){
                //hay mensajes en la sala de chat
                foreach($chats as $chat) {
                    //el doble corchete es como hacer un array push()
                    $data['messages'][] = [
                        'id' => $chat->id,
                        'sender' => [
                            'id' => $chat->FromUser->id,
                            'full_name' => $chat->FromUser->name . ' ' . $chat->FromUser->surname,
                            'avatar' => $chat->FromUser->avatar ? env('APP_URL' . 'storage/' . $chat->FromUser->avatar) : null,
                        ],
                        'message' => $chat->message,
                        // 'file' => $chat->file,
                        'read_at' => $chat->read_at,
                        'time' => $chat->created_at->diffForHumans(),
                        'created_at' => $chat->created_at,
                    ];
                }
            }
            else{
                //no hay mensajes previos en el chat
                $data['messages'] = [];
            }

            $data['exist'] = 1;
            $data['last_page'] = $chats->lastPage();

            return response()->json($data);
        }
        else{
            //crear nueva sala de chat
            $chatRoom = ChatRoom::create([
                'first_user' => auth('api')->user()->id,
                'second_user' => $request->to_user_id,
                'last_at' => now()->format("Y-m-d H:i:s.u"),
                'uniqid' => uniqid(),
            ]);

            $data = [];
            $data['room_id'] = $chatRoom->id;
            $data['room_uniqid'] = $chatRoom->uniqid;
            $to_user = User::find($request->to_user_id);
            $data['user'] = [
                'id' => $to_user->id,
                'full_name' => $to_user->name . ' ' . $to_user->surname,
                'avatar' => $to_user->avatar ? env('APP_URL') . 'storage/' . $to_user->avatar : 'https://cdn-icons-png.flaticon.com/512/3135/3135715.png',
            ];
            $data['messages'] = [];
            $data['exist'] = 0;
            $data['last_page'] = 1;

            return response()->json($data);
        }
    }

    //función para el envío de mensajes de texto
    public function sendMessageText(Request $request) {
        date_default_timezone_set("Europe/Madrid");

        $request->request->add(['user_id' => auth('api')->user()->id]);
        $chat = Chat::create($request->all());

        //modificar el campo last_at en chat_rooms
        $chat->ChatRoom->update(['last_at' => now()->format("Y-m-d H:i:s.u")]);

        //generar evento para notificar al receptor del mensaje
        //hacer push del mensaje recibido
            //SendMessageChat
        //notificar al panel lateral que contiene todos los chats
            //RefreshMyChatRoom
        //notificar al panel lateral que contiene todos los chats en las vistas del receptor
            //RefreshMyChatRoom
    }

    //listar todos los chats (barra lateral)
    public function listMyChats() {
        //muestra las salas de chat donde el usuario autenticado es el primero o el segundo y ordénalo poniendo el chat más reciente primero.

        $chatrooms = ChatRoom::where('first_user', auth('api')->user()->id)
                    ->orWhere('second_user', auth('api')->user()->id)
                    ->orderBy('last_at', 'desc')
                    ->get();

        //en la respuesta se requiere el nombre del usuario con el que se habla/el nombre del grupo, foto asociada, hora del último mensaje y el último mensaje en sí. Para conseguirlo, hay que mapear la respuesta obtenida en la consulta.
        return response()->json(['chatrooms' => $chatrooms->map(function($item) {
            //Primero se ha hecho en el controlador, pero luego se ha pasado la lógica al resource chat/ChatGResource para tenerlo más encapsulado.
            return ChatGResource::make($item);

            /*
            Lógica pasada al resource:
            [
                'friend_first' => $item->first_user != auth('api')->user()->id ?
                    [
                        'id' => $item->FirstUser->id,
                        'full_name' => $item->FirstUser->name . ' ' . $item->FirstUser->surname,
                        'avatar' =>  $item->FirstUser->avatar ? env('APP_URL') . 'storage/' . $item->FirstUser->avatar : null,
                    ] : null,
                'friend_second' => $item->second_user ?
                    $item->second_user != auth('api')->user()->id ?
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
                        'last_message_is_mine' => $item->ChatGroup->last_user_message ?
                                $item->ChatGroup->last_user_message === auth('api')->user()->id
                            : null,
                        'last_time' => $item->ChatGroup->last_time_created_at,
                        'count_message' => $item->ChatGroup->getCountMessages(auth('api')->user()->id),

                    ] : null,
                'uniqid' => $item->uniqid,
                'is_active' => false,
                'last_message' => $item->last_message,
                'last_message_is_mine' => $item->last_user_message ? $item->last_user_message === auth('api')->user()->id : null,
                'last_time' => $item->last_time_created_at,
                'count_message' => $item->getCountMessages(auth('api')->user()->id),
            ];
            */
        })]);
    }
}
