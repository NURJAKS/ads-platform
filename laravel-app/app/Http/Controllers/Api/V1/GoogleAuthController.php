<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;
use App\Helpers\ApiResponse;

class GoogleAuthController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('google')
            ->stateless()
            ->redirect();
    }

    public function callback()
    {
        try {
            \Log::info('Google Auth Callback: Started');
            
            $googleUser = Socialite::driver('google')
                ->stateless()
                ->user();
                
            \Log::info('Google Auth Callback: User received', ['email' => $googleUser->getEmail()]);

            $user = User::updateOrCreate([
                'email' => $googleUser->getEmail(),
            ], [
                'name' => $googleUser->getName(),
                'google_id' => $googleUser->getId(),
                'avatar' => $googleUser->getAvatar(),
                'password' => bcrypt(Str::random(16)), // Fallback password for new users
            ]);

            $token = $user->createToken('google-auth')->plainTextToken;

            // Redirect to frontend with token
            $frontendUrl = config('app.frontend_url', 'http://localhost:3000');
            return redirect("{$frontendUrl}/auth/google/callback?token={$token}");

        } catch (\Exception $e) {
            \Log::error('Google Auth Error: ' . $e->getMessage());
            $frontendUrl = config('app.frontend_url', 'http://localhost:3000');
            return redirect("{$frontendUrl}/login?error=auth_failed");
        }
    }
}
