<?php

namespace Creacoon\GitLabTile;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class FetchDataFromApiCommand extends Command
{
    protected $signature = 'dashboard:fetch-data-from-gitlab-api';
    protected $description = 'Fetch data for GitLab tile';

    public function handle()
    {
        $usersResponse = Http::withHeaders([
            'PRIVATE-TOKEN' => env('GITLAB_API_TOKEN'),
        ])->get('https://gl.creacoon.nl/api/v4/users?active=true');

        if ($usersResponse->successful()) {
            $usersData = $usersResponse->json();
            dump('Users Data:', $usersData);

            $userProfiles = [];
            foreach ($usersData as $user) {
                $username = $user['username'];
                $userId = $user['id'];
                $userProfiles[$username] = [
                    'avatar_url' => $user['avatar_url'] ?? null,
                    'name' => $user['name'] ?? $username,
                    'assigned_merge_requests' => 0,
                    'review_requested_merge_requests' => 0,
                    'todos' => 0,
                ];

                $userCountResponse = Http::withHeaders([
                    'PRIVATE-TOKEN' => env('GITLAB_API_TOKEN'),
                ])->get("https://gl.creacoon.nl/api/v4/user_counts");

                if ($userCountResponse->successful()) {
                    $userCountData = $userCountResponse->json();
                    dump('User Count Data for '.$username.':', $userCountData);

                    $userProfiles[$username]['assigned_merge_requests'] = $userCountData['assigned_merge_requests'] ?? 0;
                    $userProfiles[$username]['review_requested_merge_requests'] = $userCountData['review_requested_merge_requests'] ?? 0;
                    $userProfiles[$username]['todos'] = $userCountData['todos'] ?? 0;
                } else {
                    $this->error('Failed to fetch user count for: ' . $username . '. Status: ' . $userCountResponse->status() . ', Body: ' . $userCountResponse->body());
                }
            }

            GitLabStore::make()->setData($userProfiles);
            $this->info('Data fetched successfully!');
        } else {
            $this->error('Failed to fetch user data: ' . $usersResponse->status());
        }
    }
}
