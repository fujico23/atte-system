<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Laravel\Fortify\Fortify;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Paginator::useBootstrap();

        Fortify::authenticateUsing(function ($request) {
            $validated = Auth::validate($credentials = [
                'email' => $request->email,
                'password' => $request->password
            ]);

            return $validated ? Auth::getProvider()->retrieveByCredentials($credentials) : null;
        });
    }
}
