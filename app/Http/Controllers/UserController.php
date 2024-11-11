<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\Profile\SettingsRequest;
use App\Http\Resources\User\PersonalDataResource;
use App\Models\User;
use Auth;
use Cache;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class UserController extends BaseController
{
    public function profile(Request $request)
    {   
        $result = User::where('id', auth()->id())->with('statistic', 'setting')->get();
        $user = PersonalDataResource::make($result[0])->resolve();
        if(Cache::has('request_settings_blocked:' . auth()->id())){
            $blockedRequestSettings = Cache::get('request_settings_blocked:' . auth()->id());
            return Inertia::render('user/Profile', compact('user', 'blockedRequestSettings'));
        }
        return Inertia::render('user/Profile', compact('user'));
    }

    public function update($what, Request $request)
    {
        if($what == 'name'){
            $userId = auth()->id();
            $newName = $request->name;
            if (Cache::has('user_blocked:' . $userId)) {
                return response()->json(['blockChangeName' => 'Смена логина заблокирована на 1 час']);
            }
            $attempts = Cache::get('name_change_attempts:' . $userId, 0);

            if ($attempts >= 5) {
                // Если было 5 попыток, блокируем пользователя на 1 час
                Cache::put('user_blocked:' . $userId, true, now()->addHour());
                Cache::forget('name_change_attempts:' . $userId); // Сбрасываем счетчик
                return response()->json(['blockChangeName' => 'Смена логина заблокирована на 1 час']);
            }
            $this->service->updateLogin($request);
            $cacheUser = Cache::get('user:' . $userId);
            $cacheUser->name = $newName;
            Cache::put('user:' . $userId, $cacheUser);
            $user = User::find($userId);
            $user->update(['name' => $newName]);
            Cache::increment('name_change_attempts:' . $userId);
            Cache::put('name_change_attempts:' . $userId, Cache::get('name_change_attempts:' . $userId), now()->addMinutes(5)); 
        } elseif ($what == 'email') {
            $this->service->updateEmail($request);
            $user = Auth::user();
            if (!Hash::check($request->password, $user->password)) {
                return response()->json([
                    'password' => ['Неверный пароль']
                ]);
            }
            $user->email = $request->email;
            $user->save();
        } elseif ($what == 'password') {
            $this->service->updatePassword($request);
            $user = Auth::user();
            if (!Hash::check($request->oldPassword, $user->password)) {
                return response()->json([
                    'badPassword' => ['Неверный пароль']
                ]);
            }
            $user->password = Hash::make($request->newPassword);
            $user->save();
            DB::table('sessions')->where('user_id', auth()->id())->delete();
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return response()->json([
                'redirect' => true
            ]);
        }
    }
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return response()->json([
            'redirect' => true
        ]);
    }

    public function delete(Request $request)
    {
        $this->service->deleteUser($request);
        $user = Auth::user();
        if (!Hash::check($request->password, $user->password)) {
            return response()->json([
                'password' => ['Неверный пароль']
            ]);
        }
        $user1 = User::find(auth()->id());
        $user1->delete();
        DB::table('sessions')->where('user_id', auth()->id())->delete();
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        Cache::forget('user:' . $user->id);
        return response()->json([
            'deleted' => true
        ]);
    }

    public function updateSettigs(SettingsRequest $request)
    {
        $blocked = $this->service->blockingRequests(auth()->id(), 'request_settings_blocked', 'request_settings_attempts', 10);
        if ($blocked){
            return response()->json(['blockRequest' => 'Слишком много запросов, данный функционал теперь заблокирован на 1 час']);
        }
        $data = $request->validated();
        $this->service->updateSettings($data);
        return response()->json([
            'changed' => true
        ]);
    }
}
