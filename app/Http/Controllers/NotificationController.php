<?php

namespace App\Http\Controllers;

use App\Http\Resources\Notification\FriendResource;
use App\Models\WaitFriend;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function getUser()
    {
        return response()->json([
            'id' => auth()->id()
        ]);
    }

    public function getFriendNotifications()
    {
        $requestResult = WaitFriend::where('receiver_id', auth()->id())->with('user')->get();
        $request = FriendResource::collection($requestResult)->resolve();
        return response()->json([
            'requests' => $request
        ]);
    }
}
