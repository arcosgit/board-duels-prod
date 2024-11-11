<?php

namespace App\Http\Controllers;

use App\Events\Notification\FriendEvent;
use App\Http\Requests\Friend\FindUserRequest;
use App\Http\Resources\Friend\RequestFriendResource;
use App\Http\Resources\User\FindedResource;
use App\Jobs\Friend\DeleteRequest;
use App\Models\Chat;
use App\Models\Friend;
use App\Models\User;
use App\Models\WaitFriend;
use Cache;
use Illuminate\Http\Request;
use Inertia\Inertia;

class FriendController extends BaseController
{
    public function index()
    {
        $requestsFriendsResult = WaitFriend::where('receiver_id', auth()->id())->with('user')->latest()->get();
        $requestsFriend = RequestFriendResource::collection($requestsFriendsResult)->resolve();
        $friendList = Friend::where('user1_id', auth()->id())
        ->with(['user2:id,name'], 'setting') 
        ->orWhere('user2_id', auth()->id())
        ->with(['user1:id,name'], 'setting')
        ->get()
        ->map(function($friend) {
            // Определяем кто является другом
            if ($friend->user1_id == auth()->id()) {
                return [
                    'id' => $friend->user2->id,
                    'name' => $friend->user2->name,
                    'settings' => $friend->user2->setting
                ];
            } else {
                return [
                    'id' => $friend->user1->id,
                    'name' => $friend->user1->name,
                    'settings' => $friend->user1->setting
                ];
            }
        });
        $chats = Chat::where('user1_id', auth()->id())->orWhere('user2_id', auth()->id())->get()
        ->map(function($chat) {
            if ($chat->user1_id == auth()->id()) {
                return [
                    'chat_id' => $chat->id,
                    'user_id' => $chat->user2_id,
                ];
            }else{
                return [
                    'chat_id' => $chat->id,
                    'user_id' => $chat->user1_id,
                ];
            }
        });
        if(Cache::has('request_friend_blocked:' . auth()->id())){
            $blocked = Cache::get('request_friend_blocked:' . auth()->id());
            return Inertia::render('user/Friend', compact('blocked', 'requestsFriend', 'friendList', 'chats'));
        }else{
            return Inertia::render('user/Friend', compact('requestsFriend', 'friendList', 'chats'));
        }
        
    }

    public function findUser(FindUserRequest $request)
    {
        $blocked = $this->service->blockingRequests(auth()->id(), 'request_friend_blocked', 'request_friend_attempts', 20);
        if ($blocked){
            return response()->json(['blockRequest' => 'Слишком много запросов, данный функционал теперь заблокирован на 1 час']);
        }
        $data = $request->validated();
        $user = User::where('name', $data['name'])->with('setting')->get();
        if ($user){
            if (!$user[0]->setting[0]->freind_add_can){
                return response()->json([
                    'cantAdd' => ['Данному пользователю нельзя отправлять запросы в друзья']
                ]);
            }
            if ($user[0]->id == auth()->id()){
                return response()->json([
                    'haveRequest' => ['Нельзя добавить самого себя в друзья']
                ]);
            }
            $res = WaitFriend::where('sender_id', $user[0]->id)->where('receiver_id', auth()->id())->get();
            if(count($res) != 0){ // При попытки найти пользователя который уже отправил запрос в друзья нам показываем ошибку
                return response()->json([
                    'haveRequest' => ['Данный пользователь уже отправлял вам запрос в друзья']
                ]);
            }
            //Всё хорошо
            return FindedResource::make($user[0])->resolve();
        } else{
            return response()->json([
                'error' => ['Пользователь не найден']
            ]);
        }
    }

    public function inviteFriend(Request $request)
    {
        $blocked = $this->service->blockingRequests(auth()->id(), 'request_friend_blocked', 'request_friend_attempts', 20);
        if ($blocked){
            return response()->json(['blockRequest' => 'Слишком много запросов, данный функционал теперь заблокирован на 1 час']);
        }
        $this->service->inviteFriend($request);
        $requestSent = WaitFriend::where('sender_id', auth()->id())->where('receiver_id', $request->id)->get();
        if (count($requestSent) != 0){
            return response()->json([
                'sent' => true
            ]);
        }
        $res1 = Friend::where('user1_id', auth()->id())->where('user2_id', $request->id)->get();
        $res2 = Friend::where('user1_id', $request->id)->where('user2_id', auth()->id())->get();
        if (count($res1) != 0 || count($res2) != 0){
            return response()->json([
                'friendHave' => ['Вы уже являетесь друзьями']
            ]);
        }
        $res = WaitFriend::where('sender_id', $request->id)->where('receiver_id', auth()->id())->get();
        if(count($res) != 0){ // При попытки найти пользователя который уже отправил запрос в друзья нам показываем ошибку
            return response()->json([
                'haveRequest' => ['Данный пользователь уже отправлял вам запрос в друзья']
            ]);
        }
        $notification = WaitFriend::create([
            'sender_id' => auth()->id(),
            'receiver_id' => $request->id
        ])->load('user');
        broadcast(new FriendEvent($notification))->toOthers();
        return response()->json([
            'success' => true
        ]);
    }

    public function addFriend(Request $request)
    {
        $this->service->inviteFriend($request);
        $friendCount = Friend::where('user1_id', auth()->id())->orWhere('user2_id', auth()->id())->count();
        if ($friendCount >= 50){
            return response()->json([
                'fullFriendList' => 'Максимум 50 друзей'
            ]);
        }
        Friend::create([
            'user1_id' => auth()->id(),
            'user2_id' => $request->id
        ]);
        WaitFriend::where('sender_id', $request->id)->where('receiver_id', auth()->id())->delete();
        Chat::create([
            'user1_id' => auth()->id(),
            'user2_id' => $request->id
        ]);
        return response()->json([
            'added' => true
        ]);
    }

    public function deleteFriend(Request $request)
    {
        $this->service->inviteFriend($request);
        Friend::where('user1_id', auth()->id())->where('user2_id', $request->id)->delete();
        Friend::where('user1_id', $request->id)->where('user2_id', auth()->id())->delete();
        Chat::where('user1_id', auth()->id())->where('user2_id', $request->id)->delete();
        Chat::where('user1_id', $request->id)->where('user2_id', auth()->id())->delete();
        return response()->json([
            'deleted' => true
        ]);
    }

    public function refuseFriend(Request $request)
    {
        $this->service->inviteFriend($request);
        WaitFriend::where('sender_id', $request->id)->where('receiver_id', auth()->id())->delete();
        return response()->json([
            'refused' => true
        ]);
    }
}
