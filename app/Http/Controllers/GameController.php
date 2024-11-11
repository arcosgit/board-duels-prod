<?php

namespace App\Http\Controllers;

use App\Events\Game\AcceptEvent;
use App\Events\Game\InviteEvent;
use App\Events\Game\LeftEvent;
use App\Events\Game\LobbyStartEvent;
use App\Events\Game\PlayEvent;
use App\Events\Game\RestartEvent;
use App\Events\Game\StartEvent;
use App\Http\Requests\Game\MakeMoveRequest;
use App\Http\Requests\Game\RequestToGameRequest;
use App\Http\Requests\Game\TimeoutRequest;
use App\Jobs\Game\DeleteAcceptJob;
use App\Models\Game;
use App\Models\GameAccept;
use App\Models\GameRequest;
use App\Models\History;
use App\Models\Statistic;
use App\Models\User;
use Cache;
use Illuminate\Http\Request;
use Inertia\Inertia;

class GameController extends BaseController
{
    public function requestToGame(RequestToGameRequest $request)
    {
        $data = $request->validated();
        $tryToStartGame = GameRequest::where('status', 'pending')->where('time', $data['time'])->where('size_board', $data['size_board'])->first();
        if ($tryToStartGame == null){
            GameRequest::create($data);
        } else{
            $user = $tryToStartGame->user_id;
            $gameAccept = GameAccept::create([
                'user1_id' => $user,
                'user2_id' => $data['user_id'],
                'time' => $data['time'],
                'size_board' => $data['size_board']
            ]);
            GameRequest::where('user_id', $user)->delete();
            GameRequest::where('user_id', $data['user_id'])->delete();
            broadcast(new AcceptEvent($gameAccept->user1_id, $gameAccept->user2_id));
        }
    }

    public function refuseGame(Request $request)
    {
        GameRequest::where('user_id', $request->user_id)->delete();
    }

    public function acceptGame(Request $request)
    {
        $gameAcceptFirst = GameAccept::where('user1_id', $request->user_id)->first();
        $gameAcceptSecond = GameAccept::where('user2_id', $request->user_id)->first();
        if($gameAcceptFirst != null){
            $gameAcceptFirst->update([
                'user1_status' => 'ready',
            ]);
        }
        if($gameAcceptSecond != null){
            $gameAcceptSecond->update([
                'user2_status' => 'ready',
            ]);
        }
        $ready = GameAccept::where('user1_id', $request->user_id)->orWhere('user2_id', $request->user_id)->first();
        if($ready->user1_status == 'ready' && $ready->user2_status == 'ready'){
            $game = Game::create([
                'user1_id' => $ready->user1_id,
                'user2_id' => $ready->user2_id,
                'time' => $ready->time,
                'board' => [
                    null, null, null,
                    null, null, null,
                    null, null, null
                ],
                'current_move_userId' => $ready->user1_id,
                'winner' => null
            ]);
            Statistic::where('user_id', $ready->user1_id)->increment('count_games');
            Statistic::where('user_id', $ready->user2_id)->increment('count_games');
            $ready->delete();
            broadcast(new StartEvent($game->id, $game->user1_id, $game->user2_id));
            return response()->json([
                'start' => $game->id
            ]);
        }

    }

    public function noAcceptGame(Request $request)
    {
        GameAccept::where('user1_id', $request->user_id)->orWhere('user2_id', $request->user_id)->delete();
    }

    public function game(Game $game)
    {
        $checkUserInGame = $game->where('user1_id', auth()->id())->orWhere('user2_id', auth()->id())->get();
        if(count($checkUserInGame) == 0){
            return redirect('/');
        }
        $game->load('user1', 'user2');
        if($game->status == 'end'){
            $winnerUserName = $game->winner;
            return Inertia::render('game/Index', compact('game', 'winnerUserName'));
        }
        return Inertia::render('game/Index', compact('game'));
    }

    public function move(MakeMoveRequest $request)
    {
        $data = $request->validated();
        $game = Game::find($data['game_id']);
        if($game->current_move_userId != auth()->id()){
            return response()->json([
                'error' => $game->board
            ]);
        }
        if ($data['currnet'] == 'x'){
            $game->update([
                'board' => $data['board'],
                'currnet' => 'o',
                'current_move_userId' => $game->user2_id
            ]);
        } else {
            $game->update([
                'board' => $data['board'],
                'currnet' => 'x',
                'current_move_userId' => $game->user1_id
            ]);
        }
        $isDraw = $this->service->isDraw($game->board);
        if($isDraw){
            $game->update([
                'status' => 'end',
                'winner' => 'draw'
            ]);
            broadcast(new PlayEvent($game->id, $game->currnet, $game->board, null, true));
        }
        $winner = $this->service->checkWinner($game->board);
        if($winner && $data['currnet'] == 'x'){
            $user = User::find($game->user1_id);
            $game->update([
                'status' => 'end',
                'winner' => $user->name
            ]);
            History::create([
                'user1_id' => $game->user1_id,
                'user2_id' => $game->user2_id,
                'result' => $user->name
            ]);
            Statistic::where('user_id', $user->id)->increment('count_wins');
            broadcast(new PlayEvent($game->id, $game->currnet, $game->board, $user->name));
        }
        if($winner && $data['currnet'] == 'o'){
            $user = User::find($game->user2_id);
            $game->update([
                'status' => 'end',
                'winner' => $user->name
            ]);
            History::create([
                'user1_id' => $game->user1_id,
                'user2_id' => $game->user2_id,
                'result' => $user->name
            ]);
            Statistic::where('user_id', $user->id)->increment('count_wins');
            broadcast(new PlayEvent($game->id, $game->currnet, $game->board,$user->name));
        }
        broadcast(new PlayEvent($game->id, $game->currnet, $game->board));
    }

    public function timeout(TimeoutRequest $request)
    {
        $data = $request->validated();
        $game = Game::with('user1', 'user2')->find($data['game_id']);
        $winner = null;
        if($game->status == 'end'){
            return response()->json([
                'winner' => $game->winner
            ]);
        }
        $game->update(['status' => 'end']);
        if ($game->user1_current == $data['winner']){
            $res = History::create([
                'user1_id' => $game->user1_id,
                'user2_id' => $game->user2_id,
                'result' => $game->user1->name
            ]);
            $game->update(['winner' => $res->result]);
            $winner = $res->result;
            Statistic::where('user_id', $game->user1->id)->increment('count_wins');
        }
        if($game->user2_current == $data['winner']){
            $res = History::create([
                'user1_id' => $game->user1_id,
                'user2_id' => $game->user2_id,
                'result' => $game->user2->name
            ]);
            $winner = $res->result;
            $game->update(['winner' => $res->result]);
            Statistic::where('user_id', $game->user2->id)->increment('count_wins');
        }
        return response()->json([
            'winner' => $winner
        ]);

    }

    public function reset(Request $request)
    {
        $game = Game::find($request->game_id);
        if($game == null){
            return response()->json([
                'no_game' => true
            ]);
        }
        $reset = Cache::get('reset-game:' . $request->game_id, 0);
        if($reset >= 1){
            $game = Game::find($request->game_id);
            $game->update([
                'status' => 'playing',
                'board' => [
                    null, null, null,
                    null, null, null,
                    null, null, null
                ],
                'winner' => null
            ]);
            Cache::forget('reset-game:' . $request->game_id);
            Statistic::where('user_id', $game->user1_id)->increment('count_games');
            Statistic::where('user_id', $game->user2_id)->increment('count_games');
            broadcast(new RestartEvent($game));
            return;
        }
        Cache::increment('reset-game:' . $request->game_id);
        Cache::put('reset-game:' . $request->game_id, Cache::get('reset-game:' . $request->game_id), now()->addMinutes(2));
    }

    public function left(Request $request)
    {
        $request->validate([
            'game_id' => 'required|exists:games,id'
        ]);
        $game = Game::find($request->game_id);
        Cache::forget('reset-game:' . $request->game_id);
        if($game == null || $game->status == 'end'){
            $game->delete();
            return;
        }
        if($game->user1_id == auth()->id()){
            $user = User::find($game->user2_id);
            History::create([
                'user1_id' => $game->user1_id,
                'user2_id' => $game->user2_id,
                'result' => $user->name
            ]);
            Statistic::where('user_id', $user->id)->increment('count_wins');
            broadcast(new LeftEvent($game->id, $user->name));
            $game->delete();
        }
        if($game->user2_id == auth()->id()){
            $user = User::find($game->user1_id);
            History::create([
                'user1_id' => $game->user1_id,
                'user2_id' => $game->user2_id,
                'result' => $user->name
            ]);
            Statistic::where('user_id', $user->id)->increment('count_wins');
            broadcast(new LeftEvent($game->id, $user->name));
            $game->delete();
        }
    }

    public function end(Request $request)
    {
        $game = Game::find($request->game_id);
        if($game == null){
            return response()->json([
                'deleted' => true
            ]);
        }
        $game->delete();
        return response()->json([
            'deleted' => true
        ]);
    }

    public function invite(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'time' => 'required',

        ]);
        broadcast(new InviteEvent($request->user_id, auth()->user()->name, $request->time, auth()->id()))->toOthers();
    }

    public function createWithFriend(Request $request)
    {
        $request->validate([
            'friendId' => 'required|exists:users,id',
            'time' => 'required',
        ]);
        $inGame = Game::where('user1_id', $request->friendId)->orWhere('user2_id', $request->friendId)->first();
        if($inGame != null){
            return response()->json([
                'in_game' => true
            ]);
        }
        $game = Game::create([
            'user1_id' => $request->friendId,
            'user2_id' => auth()->id(),
            'time' => $request->time,
            'board' => [
                null, null, null,
                null, null, null,
                null, null, null
            ],
            'current_move_userId' => $request->friendId,
            'winner' => null
        ]);
        broadcast(new LobbyStartEvent($request->friendId, $game->id))->toOthers();
        Statistic::where('user_id', $request->friendId)->increment('count_games');
        Statistic::where('user_id', auth()->id())->increment('count_games');
        return response()->json([
            'game' => $game
        ]);
    }
}
