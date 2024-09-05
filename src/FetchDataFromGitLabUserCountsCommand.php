<?php

namespace Creacoon\GitLabTile;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class FetchDataFromGitLabUserCountsCommand extends Command
{
    protected $signature = 'dashboard:fetch-data-from-gitlab-api';
    protected $description = 'Fetch data for GitLab tile';

    public function handle()
    {
        $tile_data = [];

        foreach (config('dashboard.tiles.gitlab.specific_users') as $username) {
            $userResponse = Http::withHeaders([
                'PRIVATE-TOKEN' => config('dashboard.tiles.gitlab.api_token'),
            ])->get(config('dashboard.tiles.gitlab.api_url') . "/api/v4/users?username={$username}");

            if ($userResponse->successful()) {
                $userData = $userResponse->json()[0] ?? null;
                if ($userData) {
                    $userProfile = [
                        'avatar_url' => $userData['avatar_url'] ?? null,
                        'name' => preg_filter('/[^A-Z]/', '', $userData['name']),
                        'assigned_merge_requests' => 0,
                        'review_requested_merge_requests' => 0,
                        'todos' => 0,
                    ];

                    $userCountResponse = Http::withHeaders([
                        'PRIVATE-TOKEN' => config('dashboard.tiles.gitlab.api_token'),
                        'Sudo' => $username
                    ])->get(config('dashboard.tiles.gitlab.api_url') . "/api/v4/user_counts");

                    if ($userCountResponse->successful()) {
                        $userCountData = $userCountResponse->json();
                        $userProfile['assigned_merge_requests'] = $userCountData['assigned_merge_requests'] ?? 0;
                        $userProfile['review_requested_merge_requests'] = $userCountData['review_requested_merge_requests'] ?? 0;
                        $userProfile['todos'] = $userCountData['todos'] ?? 0;

                        $tile_data[$username] = $userProfile;
                    } else {
                        $this->error('Failed to fetch user count for: ' . $username . '. Status: ' . $userCountResponse->status() . ', Body: ' . $userCountResponse->body());
                    }
                }
            } else {
                $this->error('Failed to fetch user data for: ' . $username . '. Status: ' . $userResponse->status() . ', Body: ' . $userResponse->body());
            }
        }
        GitLabUserCountsStore::make()->setData($tile_data);

        $this->info('Data fetched successfully!');
    }
}
