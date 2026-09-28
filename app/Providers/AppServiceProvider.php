<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use App\Models\Announcement;
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
        Schema::defaultStringLength(191);
        // jika mau mode local nonaktifin if ini
        if (app()->environment('production')) {
            URL::forceScheme('https');
        }

       //untuk pengumuman
        View::composer('frontend.layouts.master', function ($view) {
            $activeAnnouncement = Announcement::where('is_active', 1)->first();
            $view->with('activeAnnouncement', $activeAnnouncement);
        });
    }
}
