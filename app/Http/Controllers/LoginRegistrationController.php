<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRegistration\LoginRequest;
use App\Http\Requests\LoginRegistration\RegistrationRequest;
use App\Mail\ResetPasswordMail;
use App\Models\User;
use Auth;
use Cache;
use Hash;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Mail;
use Nette\Utils\Random;

class LoginRegistrationController extends BaseController
{
    public function login()
    {
        return Inertia::render('user/LoginRegistration');
    }


    public function registration(RegistrationRequest $request)
    {
        $data = $request->validated();
        $user = $this->service->registration($data);
        auth()->login($user, true);
        $request->session()->regenerate();
        return response()->json([
            'redirect' => true
        ]);
    }

    public function authenticate(LoginRequest $request)
    {
        if(Cache::has('request_auth_blocked:' . $request->email)){
            return response()->json([
                'blockAuthEmail' => $request->email,
            ]);
        }
        $data = $request->validated();
        if(Auth::attempt($data, true)){
            $request->session()->regenerate();
            return response()->json([
                'authenticated' => true
            ]);
        }else{
            $blocked = $this->service->blockingRequests($request->email, 'request_auth_blocked', 'request_auth_attempts', 7);
            if ($blocked){
                return response()->json([
                    'password' => 'Неверный пароль',
                    'blockAuthEmail' => $request->email
                ]);
            }
            return response()->json([
                'password' => 'Неверный пароль'
            ]);
        }
        
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email|max:255|exists:users,email',
        ],[
            'email.required' => 'Это поле необходимо заполнить',
            'email.string' => 'Почта должна быть строковой',
            'email.email' => 'Неверный формат почты',
            'email.max' => 'Максимальная длина почты 255',
            'email.exists' => 'Пользователь с таким email не зарегистрирован',
        ]);

        $email = Cache::get('request_reset_password:' . $request->email);
        if($email != null){
            return response()->json([
                'sent' => ['Письмо уже было отправлено'],
            ]);
        }
        Cache::put('request_reset_password:' . $request->email, $request->email, now()->addWeek());
        $user = User::where('email', $request->email)->first();        
        $newPassword = Random::generate(10);
        $user->update([
            'password' => Hash::make($newPassword)
        ]);
        Mail::to($request->email)->send(new ResetPasswordMail($newPassword));
    }
}
