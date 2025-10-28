<?php

namespace App\Providers;
use App\Models\Parametres;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // View::composer('*', function ($view) {
        //     $theme = null;

        //     if (auth()->check()) {
        //         $user = auth()->user();

        //         if ($user->idCompagnie) {
        //             // Thème lié à la compagnie
        //             $theme = Parametres::where('idCompagnie', $user->idCompagnie)->first();
        //         } else {
        //             // Admin général ou utilisateur sans compagnie → thème global
        //             $theme = Parametres::whereNull('idCompagnie')->first();
        //         }
        //     } else {
        //         // Visiteur non connecté → thème global
        //         $theme = Parametres::whereNull('idCompagnie')->first();
        //     }

        //     $view->with('theme', $theme);
        // });

        Schema::defaultStringLength(245);
    }
}
