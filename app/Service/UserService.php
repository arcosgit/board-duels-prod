<?php
namespace App\Service;
use App\Models\Setting;
use App\Models\Statistic;
use App\Models\User;
use Cache;
use Hash;
use Illuminate\Support\Facades\DB;
use Request;
class UserService{
    public function registration($data)
    {
        try{
            DB::beginTransaction();
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password'])
            ]);
            Statistic::create([
                'user_id' => $user->id,
            ]);
            Setting::create([
                'user_id' => $user->id,
            ]);
            $withSettingsStatisticsUser = User::with('setting', 'statistic')->find($user->id);
            DB::commit();
            Cache::put('user:' . $user->id, $withSettingsStatisticsUser);
            return $user;
        } catch(\Exception $e){
            DB::rollBack();
        }
    }

    public function updateLogin($request)
    {
        $request->validate([
            'name' => 'required|string|max:50|unique:users,name,' . auth()->id(),
        ],[
            'name.required' => 'Это поле необходимо заполнить',
            'name.string' => 'Логин должен быть строковым',
            'name.max' => 'Максимальная длина 50 символов',
            'name.unique' => 'Такой логин уже существует',
        ]);
    }

    public function updateEmail($request)
    {
        $request->validate([
            'email' => 'required|string|email|max:255|unique:users,email,' . auth()->id(),
            'password' => 'required|string',
        ], [
            'email.required' => 'Это поле необходимо заполнить',
            'email.string' => 'Email должен быть строковым',
            'email.email' => 'Некорректный email',
            'email.max' => 'Максимальная длина 255 символов',
            'email.unique' => 'Такой email уже существует',
            'password.required' => 'Это поле необходимо заполнить',
            'password.string' => 'Пароль должен быть строковым',
        ]);
    }

    public function updatePassword($request)
    {
        $request->validate([
            'oldPassword' => 'required|string',
            'newPassword' => 'required|string|min:8',
            'newPasswordConfirm' => 'required|string|min:8|same:newPassword',
        ],[
            'oldPassword.required' => 'Это поле необходимо заполнить',
            'oldPassword.string' => 'Пароль должен быть строковым',
            'newPassword.required' => 'Это поле необходимо заполнить',
            'newPassword.string' => 'Пароль должен быть строковым',
            'newPassword.min' => 'Минимум 8 символов',
            'newPasswordConfirm.required' => 'Это поле необходимо заполнить',
            'newPasswordConfirm.string' => 'Пароль должен быть строковым',
            'newPasswordConfirm.min' => 'Минимум 8 символов',
            'newPasswordConfirm.same' => 'Пароли не совпали',
        ]);
    }

    public function deleteUser($request)
    {
        $request->validate([
            'password' => 'required|string',
        ],[
            'password.required' => 'Это поле необходимо заполнить',
            'password.string' => 'Пароль должен быть строковым',
        ]);
    }


    public function updateSettings($data)
    {
        Setting::where('user_id', auth()->id())->update($data);
    }


    public function inviteFriend($request)
    {
        $request->validate([
            'id' => 'required|exists:users,id'
        ],[
            'id.required' => 'Возникли ошибки при добавлении в друзья',
            'id.exists' => 'Возникли ошибки при добавлении в друзья'
        ]);
    }

    public function blockingRequests(int|string $userId, string $nameKeyBlocked, string $nameKeyAttempts, int $attemptsCount)
    {
        if (Cache::has("$nameKeyBlocked:" . $userId)) {
            return true;
        }
        $attempts = Cache::get("$nameKeyAttempts:" . $userId, 0);

        if ($attempts >= $attemptsCount) {
            Cache::put("$nameKeyBlocked:" . $userId, true, now()->addHour());
            Cache::forget("$nameKeyAttempts:" . $userId); // Сбрасываем счетчик
            return true;
        }
        Cache::increment("$nameKeyAttempts:" . $userId);
        Cache::put("$nameKeyAttempts:" . $userId, Cache::get("$nameKeyAttempts:" . $userId), now()->addMinutes(5));
    }

    public function checkWinner($board)
    {
        $winningCombinations = [
            [0, 1, 2],
            [3, 4, 5],
            [6, 7, 8],
            [0, 3, 6],
            [1, 4, 7],
            [2, 5, 8],
            [0, 4, 8],
            [2, 4, 6]
        ];
        foreach ($winningCombinations as $combination) {
            list($a, $b, $c) = $combination;
            if ($board[$a] && $board[$a] === $board[$b] && $board[$a] === $board[$c]) {
                return true;
            }
        }
        return false;

    }

    public function isDraw($board)
    {
        if (array_reduce($board, fn($carry, $cell) => $carry && $cell !== null, true)) {
            return true;
        }
        return false;
    }
}