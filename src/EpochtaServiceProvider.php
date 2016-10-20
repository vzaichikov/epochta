<?php

namespace Enniel\Epochta;

use Illuminate\Support\ServiceProvider;

class EpochtaServiceProvider extends ServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function boot()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function register()
    {
        $this->app->singleton(SMS::class, function ($app) {
            return new SMS($app['config']->get('services.epochta.sms'));
        });
        $this->app->alias(SMS::class, 'epochta.sms');
    }
}
