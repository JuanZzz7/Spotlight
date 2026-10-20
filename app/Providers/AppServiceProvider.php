<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

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
        \Illuminate\Pagination\Paginator::useBootstrapFive();

        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }

        if ($alwaysTo = env('MAIL_ALWAYS_TO')) {
            Mail::alwaysTo($alwaysTo);
        }

        \Illuminate\Support\Facades\Gate::policy(\App\Models\ElectronicDocument::class, \App\Policies\ElectronicDocumentPolicy::class);
        \Illuminate\Support\Facades\Gate::policy(\App\Models\CompanyFiscalProfile::class, \App\Policies\CompanyFiscalProfilePolicy::class);
    }
}
