<?php

namespace Creacoon\GitLabTile;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class GitLabTileServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                FetchDataFromApiCommand::class,
            ]);
        }

        $this->publishes([
            __DIR__ . '/../resources/views' => resource_path('views/vendor/dashboard-gitlab-tile'),
        ], 'dashboard-gitlab-tile-views');

        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'dashboard-gitlab-tile');

        Livewire::component('git-lab-tile', GitLabTileComponent::class);
    }
}
