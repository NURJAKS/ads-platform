<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\Router;
use App\Http\Middleware\RoleMiddleware;

class AppServiceProvider extends ServiceProvider
{
    public function boot(Router $router): void
    {
        // ✅ Регистрируем alias middleware ПРАВИЛЬНО (Laravel 12)
        $router->aliasMiddleware('role', RoleMiddleware::class);
    }
}
