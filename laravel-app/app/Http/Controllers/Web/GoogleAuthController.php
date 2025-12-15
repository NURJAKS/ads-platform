<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback()
    {
        $googleUser = Socialite::driver('google')->user();

        // 1️⃣ Ищем пользователя по email
        $user = User::where('email', $googleUser->getEmail())->first();

        if ($user) {
            // 2️⃣ Если есть — привязываем Google
            if (!$user->google_id) {
                $user->update([
                    'google_id' => $googleUser->getId(),
                    'avatar' => $googleUser->getAvatar(),
                ]);
            }
        } else {
            // 3️⃣ Если нет — создаём
            $user = User::create([
                'name' => $googleUser->getName(),
                'email' => $googleUser->getEmail(),
                'google_id' => $googleUser->getId(),
                'avatar' => $googleUser->getAvatar(),
                'password' => bcrypt(Str::random(16)),
            ]);
        }

        // 4️⃣ WEB-логин (КЛЮЧ)
        Auth::login($user);

        return redirect('/admin/dashboard'); // или куда тебе нужно
    }
}

