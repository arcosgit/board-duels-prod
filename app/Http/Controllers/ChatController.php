<?php

namespace App\Http\Controllers;

use App\Events\Chat\StoreMessageEvent;
use App\Events\Notification\NewMessageEvent;
use App\Events\TestEvent;
use App\Http\Requests\Chat\StoreRequest;
use App\Models\Chat;
use App\Models\Message;
use App\Models\Setting;
use App\Models\User;
use Cache;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ChatController extends BaseController
{
    public function show($id, Request $request)
    {
        $chat = Chat::find($id);
        if($chat == null){
            return redirect('/');
        }
        $checkUserInChat = Chat::where('id', $id)->where('user1_id', auth()->id())->orWhere('user2_id', auth()->id())->get();
        $checkFriendInChat = Chat::where('id', $id)->where('user1_id', $request->query('friendId'))->orWhere('user2_id', $request->query('friendId'))->get();
        if(count($checkUserInChat) == 0 || count($checkFriendInChat) == 0){
            return redirect('/profile');
        }
        $friendId = $request->query('friendId') ?? null;
        $friendName = $request->query('friendName') ?? null;
        if($friendId == null || $friendName == null){
            return redirect('/about');
        }
        $permission = Setting::where('user_id', $request->query('friendId'))->get('message_can');
        if(Cache::has('request_message_blocked:' . auth()->id()) || $permission[0]->message_can == 0){
            $blockMessage = Cache::get('request_message_blocked:' . auth()->id());
            $userLimitedMessages = $permission[0]->message_can;
            return Inertia::render('chat/Show', compact('friendId', 'friendName', 'id', 'blockMessage', 'userLimitedMessages'));
        }
        return Inertia::render('chat/Show', compact('friendId', 'friendName', 'id'));
    }

    public function open($id, Request $request)
    {
        $request->validate([
            'friendId' => 'required|exists:users,id'
        ],[
            'friendId.exists' => 'Данного пользователя не существует'
        ]);
        $friendInChat = Chat::where('user1_id', auth()->id())->
        where('user2_id', $request->friendId)
        ->orWhere('user2_id', auth()->id())->where('user1_id', $request->friendId)->with('user1:id,name', 'user2:id,name')
        ->get()->map(function($chat) {
            if($chat->user1_id == auth()->id()){
                return [
                    'id' => $chat->user2->id,
                    'name' => $chat->user2->name
                ];
            } else{
                return [
                    'id' => $chat->user1->id,
                    'name' => $chat->user1->name
                ];
            }
        });
        return response()->json([
            'info' => [
                'chatId' => $id,
                'friendInChat' => $friendInChat[0]
            ],
        ]);
    }


    public function store(StoreRequest $request)
    {
        $blocked = $this->service->blockingRequests(auth()->id(), 'request_message_blocked', 'request_message_attempts', 30);
        if ($blocked){
            return response()->json([
                'blockMessage' => ['Максимум 30 сообщений за 5 минут, чаты временно заблокированы на 1 час']
            ]);
        }
        $permission = Setting::where('user_id', $request->receiverId)->get('message_can');
        if($permission[0]->message_can == 0){
            return response()->json([
                'permission' => ['Пользователь ограничил возможность отправки сообщений']
            ]);
        }
        $message = Message::create([
            'chat_id' => $request->chatId,
            'user_id' => auth()->id(),
            'message' => $request->message
        ])->load('user');
        $user = User::find($request->receiverId);
        broadcast(new StoreMessageEvent($message))->toOthers();
        broadcast(new NewMessageEvent($message, $request->receiverId))->toOthers();
        return response()->json([
            'message' => $message
        ]);
    }

    public function getMessages(Request $request)
    {
        $messages = Message::where('chat_id', $request->query('chatId'))->latest()->paginate(20);
        return response()->json([
            'messages' => $messages
        ]);
    }
}
