<?php

namespace Rapidez\QuickOrder;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Rapidez\QuickOrder\Http\ViewComposers\ConfigComposer;

class QuickOrderServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/rapidez/quick-order.php', 'rapidez.quick-order');
    }

    public function boot()
    {
        $this
            ->bootComposers()
            ->bootRoutes()
            ->bootTranslations()
            ->bootViews()
            ->bootPublishables();
    }

    protected function bootComposers(): static
    {
        View::composer('rapidez::layouts.app', ConfigComposer::class);

        return $this;
    }

    public function bootTranslations() : self
    {
        $this->loadTranslationsFrom(__DIR__.'/../lang', 'rapidez-quick-order');

        return $this;
    }

    public function bootRoutes() : self
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');

        return $this;
    }

    public function bootViews() : self
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'rapidez-quick-order');

        return $this;
    }

    public function bootPublishables() : self
    {
        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/rapidez-quick-order'),
        ], 'rapidez-quick-order-views');

        $this->publishes([
            __DIR__.'/../config/rapidez/quick-order.php' => config_path('rapidez/quick-order.php'),
        ], 'rapidez-quick-order-config');

        return $this;
    }
}
