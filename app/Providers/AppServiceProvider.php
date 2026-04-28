<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\Pembayaran;
use App\Observers\PembayaranObserver;
use App\Models\UlasanKosan;
use App\Observers\UlasanKosanObserver;
use App\Models\Kosan;
use App\Models\Kamar;
use App\Models\Notifikasi;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind('path.public', function() {
            return base_path('public');
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (app()->environment('production')) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        // Polymorphic Morph Map
        \Illuminate\Database\Eloquent\Relations\Relation::morphMap([
            'kosan' => Kosan::class,
            'kamar' => Kamar::class,
        ]);

        // Model Observers
        if (class_exists(Pembayaran::class) && class_exists(PembayaranObserver::class)) {
            Pembayaran::observe(PembayaranObserver::class);
        }
        if (class_exists(UlasanKosan::class) && class_exists(UlasanKosanObserver::class)) {
            UlasanKosan::observe(UlasanKosanObserver::class);
        }

        Carbon::setLocale(config('app.locale'));

        // Custom Email Reset Password ke Bahasa Indonesia
        ResetPassword::toMailUsing(function ($notifiable, $token) {
            $resetUrl = url(route('password.reset', [
                'token' => $token,
                'email' => $notifiable->getEmailForPasswordReset(),
            ], false));

            return (new MailMessage)
                ->subject('Notifikasi Reset Password - HumbleKos')
                ->greeting('Halo!')
                ->line('Anda menerima email ini karena kami menerima permintaan reset password untuk akun Anda.')
                ->action('Reset Password', $resetUrl)
                ->line('Link reset password ini akan kadaluarsa dalam 60 menit.')
                ->line('Jika Anda tidak merasa melakukan permintaan ini, abaikan email ini.')
                ->salutation('Salam hangat, Tim HumbleKos');
        });

        // View Composer for Header
        View::composer('layouts.user.header', function ($view) {
            /** @var \App\Models\User|null $user */
            $user = Auth::user();
            $wishlist = 0;
            $notifCount = 0;
            $notifs = collect();

            if ($user) {
                $wishlist = Kosan::whereJsonContains('favorit', $user->user_id)->count();
                $notifCount = Notifikasi::where('user_id', $user->user_id)->unread()->count();
                $notifs = Notifikasi::where('user_id', $user->user_id)
                    ->latest()
                    ->limit(5)
                    ->get();
            }

            $view->with('header_wishlist_count', $wishlist)
                 ->with('header_notification_count', $notifCount)
                 ->with('header_notifications', $notifs);
        });
    }
}
