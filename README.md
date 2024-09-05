# laravel-dashboard-gitlab-user-counts-tile

---

GitLab User Counts Tile

---

This tile displays user counts from GitLab, including assigned merge requests, review requested merge requests, and todos.



## Installation

You can install the tile via composer:

```bash
composer require creacoon/laravel-dashboard-gitlab-user-counts-tile
```

In the `dashboard` config file, you must add this configuration in the `tiles` key.

 Sign up at  your `GitLab` instance to obtain GITLAB_API_TOKEN

```php
// in config/dashboard.php

return [
    // ...
    'tiles' => [
        'gitlab' => [
            'api_token' => env('GITLAB_API_TOKEN'),
            'api_url' => env('GITLAB_API_URL', 'https://gitlab.com'),
            'specific_users' => explode(',', env('GITLAB_SPECIFIC_USERS')),
        ],
    ],
];
```

In app\Console\Kernel.php you should schedule the Creacoon\GitLabTile\FetchDataFromGitLabUserCountsCommand to run at your desired interval.

```php
// in app/console/Kernel.php

protected function schedule(Schedule $schedule)
{
    // ...
    $schedule->command(\Creacoon\GitLabTile\FetchDataFromGitLabUserCountsCommand::class)->everyMinute();
}
```

## Usage

In your dashboard view you use the `livewire:gitlab-user-counts-tile` component.

```html
<x-dashboard>
    <livewire:gitlab-user-counts-tile position="a1" />
</x-dashboard>
```

### Customizing the view

If you want to customize the view used to render this tile, run this command:

```bash
php artisan vendor:publish --provider="Creacoon\GitLabTile\GitLabUserCountsTileServiceProvider" --tag="dashboard-gitlab-user-counts-tile-views"```
