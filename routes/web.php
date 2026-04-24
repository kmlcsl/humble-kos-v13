<?php

use Illuminate\Support\Facades\Route;
use App\Classes\ListRoutes;
use App\Http\Controllers\UserNotificationController;

$routes = new ListRoutes();
$dataRoutes = $routes->getDataAuth();

foreach ($dataRoutes as $routeGroup) {
    foreach ($routeGroup['item'] as $route) {
        $middleware = !empty($route['middleware']) ? explode(',', $route['middleware']) : [];

        if (is_string($route['controller']) && !str_contains($route['controller'], 'App\\Http\\Controllers')) { 
            $route['controller'] = 'App\\Http\\Controllers\\' . $route['controller'];
        }

        $methods = explode(',', $route['method']);
        if (count($methods) > 1) {
            $laravelRoute = Route::match($methods, $route['url'], $route['controller']);
        } else {
            $laravelRoute = Route::{$route['method']}($route['url'], $route['controller']);
        }

        if (str_contains($route['url'], '{id}')) {
            $laravelRoute->whereNumber('id');
        }
        if (str_contains($route['url'], '{booking}')) {
            $laravelRoute->whereNumber('booking');
        }

        $laravelRoute->name($route['name'])->middleware($middleware);
    }
}

Route::middleware('auth')->group(function () {
    Route::get('/users/notifications', [UserNotificationController::class, 'index'])->name('users.notifications.index');
    Route::post('/users/notifications/read-all', [UserNotificationController::class, 'markAllRead'])->name('users.notifications.readAll');
    Route::post('/users/notifications/{id}/read', [UserNotificationController::class, 'markRead'])->whereNumber('id')->name('users.notifications.read');
});

Route::get('/cek-laravel', function () {
    return view('welcome');
});
