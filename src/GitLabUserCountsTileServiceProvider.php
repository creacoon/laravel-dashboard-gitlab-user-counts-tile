<?php

namespace Creacoon\GitLabTile;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class GitLabUserCountsTileServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                FetchDataFromGitLabUserCountsCommand::class,
            ]);
        }

        $this->publishes([
            __DIR__ . '/../resources/views' => resource_path('views/vendor/dashboard-gitlab-tile'),
        ], 'dashboard-gitlab-tile-views');

        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'dashboard-gitlab-tile');

        Livewire::component('git-lab-tile', GitLabUserCountsTileComponent::class);
    }
}
