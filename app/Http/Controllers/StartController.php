<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\GameAccept;
use App\Models\GameRequest;
use Illuminate\Http\Request;
use Inertia\Inertia;

class StartController extends Controller
{
    public function index()
    {
        $userId = auth()->id();
        if($userId != null){
            $searchGame = GameRequest::where('user_id', $userId)->first();
            $acceptingGame = GameAccept::where('user1_id', $userId)->orWhere('user2_id', $userId)->first();
            $isPlaying = Game::where('user1_id', $userId)->orWhere('user2_id', $userId)->first();
            if ($searchGame != null || $acceptingGame != null || $isPlaying != null) {
                return Inertia::render('start/Index', compact('userId', 'searchGame', 'acceptingGame', 'isPlaying'));
            }
        }
        return Inertia::render('start/Index', compact('userId'));
    }

    public function offline()
    {
        return Inertia::render('start/Offline');
    }

    public function about()
    {
        return Inertia::render('start/About');
    }
}
