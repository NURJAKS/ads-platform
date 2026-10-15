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
        try {
            $googleUser = Socialite::driver('google')->user();

            // 1️⃣ Ищем пользователя по email
            $user = User::where('email', $googleUser->getEmail())->first();

            if ($user) {
                // 2️⃣ Если есть — привязываем Google (если еще не привязан)
                if (!$user->google_id) {
                    $user->update([
                        'google_id' => $googleUser->getId(),
                        'avatar' => $googleUser->getAvatar(),
                    ]);
                } else {
                    // Обновляем аватар, если изменился
                    $user->update([
                        'avatar' => $googleUser->getAvatar(),
                    ]);
                }
            } else {
                // 3️⃣ Если нет — создаём нового пользователя
                $user = User::create([
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                    'avatar' => $googleUser->getAvatar(),
                    'password' => bcrypt(Str::random(16)),
                    'role' => 'user', // По умолчанию обычный пользователь
                ]);
            }

            // 4️⃣ WEB-логин (КЛЮЧ)
            Auth::login($user, true); // true = запомнить пользователя

            // Редирект в зависимости от роли
            if ($user->role === 'admin') {
                return redirect()->route('admin.dashboard')->with('success', 'Добро пожаловать, ' . $user->name . '!');
            }

            return redirect()->route('ads.index')->with('success', 'Добро пожаловать, ' . $user->name . '!');
        } catch (\Exception $e) {
            \Log::error('Google OAuth error: ' . $e->getMessage());
            return redirect()->route('login')->with('error', 'Ошибка входа через Google. Попробуйте снова.');
        }
    }
}

