<?php

namespace Creacoon\GitLabTile;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class FetchDataFromApiCommand extends Command
{
    protected $signature = 'dashboard:fetch-data-from-gitlab-api';
    protected $description = 'Fetch data for GitLab tile';

    protected $specificUsers = [];

    public function handle()
    {
        $userProfiles = [];

        foreach ($this->specificUsers as $username) {
            $userResponse = Http::withHeaders([
                'PRIVATE-TOKEN' => env('GITLAB_API_TOKEN'),
            ])->get("https://gl.creacoon.nl/api/v4/users?username={$username}");

            if ($userResponse->successful()) {
                $userData = $userResponse->json()[0] ?? null;
                if ($userData) {
                    $userProfiles[$username] = [
                        'avatar_url' => $userData['avatar_url'] ?? null,
                        'name' => $userData['name'] ?? $username,
                        'assigned_merge_requests' => 0,
                        'review_requested_merge_requests' => 0,
                        'todos' => 0,
                    ];

                    $userCountResponse = Http::withHeaders([
                        'PRIVATE-TOKEN' => env('GITLAB_API_TOKEN'),
                    ])->get("https://gl.creacoon.nl/api/v4/user_counts");

                    if ($userCountResponse->successful()) {
                        $userCountData = $userCountResponse->json();
                        $userProfiles[$username]['assigned_merge_requests'] = $userCountData['assigned_merge_requests'] ?? 0;
                        $userProfiles[$username]['review_requested_merge_requests'] = $userCountData['review_requested_merge_requests'] ?? 0;
                        $userProfiles[$username]['todos'] = $userCountData['todos'] ?? 0;
                    } else {
                        $this->error('Failed to fetch user count for: ' . $username . '. Status: ' . $userCountResponse->status() . ', Body: ' . $userCountResponse->body());
                    }
                }
            } else {
                $this->error('Failed to fetch user data for: ' . $username . '. Status: ' . $userResponse->status() . ', Body: ' . $userResponse->body());
            }
        }

        GitLabStore::make()->setData($userProfiles);
        $this->info('Data fetched successfully!');
    }
}
