<?php

namespace MOIREI\GoogleMerchantApi;

use Illuminate\Support\ServiceProvider;

class GoogleMerchantApiServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/laravel-google-merchant-api.php' => base_path('config/laravel-google-merchant-api.php'),
            ], 'config');
        }

        $this->app->booted(function () {
            if(config('laravel-google-merchant-api.contents.orders.schedule_orders_check', false)){
                $this->registerScheduler();
            }
        });

    }

    /**
     * Register the service provider.
     */
    public function register()
    {
        $this->app->instance('productApi', new Api\ProductApi);
        $this->app->instance('orderApi', new Api\OrderApi);

        $this->commands([
            Commands\CssOrdersScout::class,
        ]);

    }

    /*
     * @codeCoverageIgnore
     */
    protected function registerScheduler(){
        $schedule = $this->app['Illuminate\Console\Scheduling\Schedule'];

        $schedule_frequency = config('laravel-google-merchant-api.contents.orders.schedule_frequency', 'hourly');

        if(!in_array($schedule_frequency, [
            'everyMinute', 'everyFiveMinutes', 'everyTenMinutes', 'everyThirtyMinutes',
            'hourly', 'daily', 'weekly', 'monthly', 'yearly',
            'weekdays', 'mondays', 'tuesdays', 'wednesdays', 'thursdays', 'fridays', 'saturdays', 'sundays',
        ])){
            $schedule_frequency = 'hourly';
        }

        $schedule->command('gm-orders:scout')->$schedule_frequency();
    }
}
