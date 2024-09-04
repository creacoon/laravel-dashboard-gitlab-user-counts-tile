<?php

namespace Creacoon\GitLabTile;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class FetchDataFromGitLabUserCountsCommand extends Command
{
    protected $signature = 'dashboard:fetch-data-from-gitlab-api';
    protected $description = 'Fetch data for GitLab tile';

    protected $specificUsers = [];

    public function handle()
    {
        $gitlabConfig = config('dashboard.tiles.gitlab');
        $specificUsers = $gitlabConfig['specific_users'];

        foreach ($specificUsers as $username) {
            $userResponse = Http::withHeaders([
                'PRIVATE-TOKEN' => $gitlabConfig['api_token'],
            ])->get($gitlabConfig['api_url'] . "/api/v4/users?username={$username}");

            if ($userResponse->successful()) {
                $userData = $userResponse->json()[0] ?? null;
                if ($userData) {
                    $userProfile = [
                        'avatar_url' => $userData['avatar_url'] ?? null,
                        'name' => $userData['name'] ?? $username,
                        'assigned_merge_requests' => 0,
                        'review_requested_merge_requests' => 0,
                        'todos' => 0,
                    ];

                    $userCountResponse = Http::withHeaders([
                        'PRIVATE-TOKEN' => $gitlabConfig['api_token'],
                    ])->get($gitlabConfig['api_url'] . "/api/v4/user_counts");

                    if ($userCountResponse->successful()) {
                        $userCountData = $userCountResponse->json();
                        $userProfile['assigned_merge_requests'] = $userCountData['assigned_merge_requests'] ?? 0;
                        $userProfile['review_requested_merge_requests'] = $userCountData['review_requested_merge_requests'] ?? 0;
                        $userProfile['todos'] = $userCountData['todos'] ?? 0;
                    } else {
                        $this->error('Failed to fetch user count for: ' . $username . '. Status: ' . $userCountResponse->status() . ', Body: ' . $userCountResponse->body());
                    }

                    GitLabUserCountsStore::make()->setData([$username => $userProfile]);
                }
            } else {
                $this->error('Failed to fetch user data for: ' . $username . '. Status: ' . $userResponse->status() . ', Body: ' . $userResponse->body());
            }
        }

        $this->info('Data fetched successfully!');
    }
}
