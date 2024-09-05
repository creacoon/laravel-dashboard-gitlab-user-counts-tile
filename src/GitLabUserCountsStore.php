<?php

namespace Creacoon\GitLabTile;

use Spatie\Dashboard\Models\Tile;

class GitLabUserCountsStore
{
    private Tile $tile;

    public static function make()
    {
        return new static();
    }

    public function __construct()
    {
        $this->tile = Tile::firstOrCreateForName("GitLabTile");
    }

    public function setData(array $data): self
    {
        $this->tile->putData('GitLabUserCounts', $data);
        return $this;
    }

    public function getData(): array
    {
        return $this->tile->getData('GitLabUserCounts') ?? [];
    }
}
