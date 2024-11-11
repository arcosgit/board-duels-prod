<?php

namespace App\Http\Controllers;

use App\Http\Resources\User\ShowResource;
use App\Models\Chat;
use App\Models\Friend;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ShowController extends Controller
{
    public function userShow($id)
    {
        if($id == auth()->id()){
            return redirect('/profile');
        }
        $findedUser = User::where('id', $id)->with('statistic', 'setting')->get();
        if(count($findedUser) == 0){
            return redirect('/');
        }
        $user = ShowResource::make($findedUser[0])->resolve();
        $friends = Friend::where('user1_id', $id)->where('user2_id', auth()->id())
        ->orWhere('user1_id', auth()->id())->where('user2_id', $id)
        ->get();
        $chat = Chat::where('user1_id', auth()->id())->where('user2_id', $id)
        ->orWhere('user2_id', auth()->id())->where('user1_id', $id)->get();
        return Inertia::render('user/Show', compact('user', 'friends', 'chat'));
    }
}
