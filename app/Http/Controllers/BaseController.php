<?php

namespace App\Http\Controllers;

use App\Models\Friend;
use App\Models\History;
use App\Service\UserService;
use Illuminate\Http\Request;

class BaseController extends Controller
{
    public $service;
    public function __construct(UserService $service)
    {
        $this->service = $service;
    }

    public function friendList()
    {
        $friendList = Friend::where('user1_id', auth()->id())
        ->with(['user2:id,name'], 'setting') 
        ->orWhere('user2_id', auth()->id())
        ->with(['user1:id,name'], 'setting')
        ->get()
        ->map(function($friend) {
            // Определяем кто является другом
            if ($friend->user1_id == auth()->id() && $friend->user2->setting[0]->lobby_can == true) {
                return [
                    'id' => $friend->user2->id,
                    'name' => $friend->user2->name,
                    'settings' => $friend->user2->setting
                ];
            } if ($friend->user1_id != auth()->id() && $friend->user1->setting[0]->lobby_can == true) {
                return [
                    'id' => $friend->user1->id,
                    'name' => $friend->user1->name,
                    'settings' => $friend->user1->setting
                ];
            }
        });
        $readyFriendList = [];
        foreach ($friendList as $item) {
            if ($item != null) {
                array_push($readyFriendList, $item);
            }
        }
        return response()->json([
            'friendList' => $readyFriendList
        ]);
    }

    public function history(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);
        $history = History::where('user1_id', $request->user_id)
        ->with('user1:id,name')
        ->orWhere('user2_id', $request->user_id)
        ->with('user2:id,name')
        ->latest()->paginate(6);
        return response()->json([
            'history' => $history
        ]);
    }
}
