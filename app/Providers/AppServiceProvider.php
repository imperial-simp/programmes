<?php

namespace Imperial\Simp\Providers;

use Illuminate\Support\ServiceProvider;
use Smalot\PdfParser\Parser as PdfParser;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('parser.pdf', function() {
          return new PdfParser();
        });
    }
}
