<?php

namespace Creacoon\GitLabTile;

use Livewire\Component;

class GitLabTileComponent extends Component
{
    public $position;

    public function mount(string $position)
    {
        $this->position = $position;
    }

    public function render()
    {
        return view('dashboard-gitlab-tile::tile', [
            'refreshIntervalInSeconds' => config('dashboard.tiles.gitlab.refresh_interval_in_seconds') ?? 60,
            'userCounts' => GitLabStore::make()->getData(),
        ]);
    }
}
