<?php

namespace Creacoon\GitLabTile;

use Illuminate\Console\Command;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class FetchDataFromGitLabUserCountsCommand extends Command
{
    protected $signature = 'dashboard:fetch-data-from-gitlab-api';
    protected $description = 'Fetch data for GitLab tile';

    public function handle(): void
    {
        $specificUsernames = collect(config('dashboard.tiles.gitlab.specific_users') ?? [])
            ->map(fn (?string $username) => trim((string) $username))
            ->filter()
            ->values();

        $onlyUsersWithOpenItems = $specificUsernames->isEmpty();

        $users = $onlyUsersWithOpenItems
            ? $this->fetchAllActiveUsers()
            : $this->fetchSpecificUsers($specificUsernames);

        $activeUsers = $users->where('state', 'active');

        $tileData = [];

        foreach ($activeUsers as $user) {
            $username = $user['username'];

            $this->info("Fetching counts for `{$username}`...");

            $userCountResponse = $this->gitLab()
                ->withHeaders(['Sudo' => $username])
                ->get('/api/v4/user_counts');

            if (! $userCountResponse->successful()) {
                $this->error("Failed to fetch user count for: {$username}. Status: {$userCountResponse->status()}, Body: {$userCountResponse->body()}");

                continue;
            }

            $userCountData = $userCountResponse->json();

            $userProfile = [
                'avatar_url' => $user['avatar_url'] ?? null,
                'name' => preg_filter('/[^A-Z]/', '', $user['name']),
                'assigned_merge_requests' => $userCountData['assigned_merge_requests'] ?? 0,
                'review_requested_merge_requests' => $userCountData['review_requested_merge_requests'] ?? 0,
                'todos' => $userCountData['todos'] ?? 0,
            ];

            if ($onlyUsersWithOpenItems && ! $this->hasOpenItems($userProfile)) {
                continue;
            }

            $tileData[$username] = $userProfile;
        }

        GitLabUserCountsStore::make()->setData($tileData);

        $this->info('Data fetched successfully!');
    }

    /**
     * @param Collection<int, string> $usernames
     *
     * @return Collection<int, array>
     */
    private function fetchSpecificUsers(Collection $usernames): Collection
    {
        return $usernames
            ->map(function (string $username) {
                $userResponse = $this->gitLab()->get('/api/v4/users', ['username' => $username]);

                if (! $userResponse->successful()) {
                    $this->error("Failed to fetch user data for: {$username}. Status: {$userResponse->status()}, Body: {$userResponse->body()}");

                    return null;
                }

                return $userResponse->json()[0] ?? null;
            })
            ->filter()
            ->values();
    }

    /** @return Collection<int, array> */
    private function fetchAllActiveUsers(): Collection
    {
        $users = collect();
        $page = 1;

        while ($page) {
            $usersResponse = $this->gitLab()->get('/api/v4/users', [
                'active' => true,
                'humans' => true,
                'exclude_internal' => true,
                'per_page' => 100,
                'page' => $page,
            ]);

            if (! $usersResponse->successful()) {
                $this->error("Failed to fetch users. Status: {$usersResponse->status()}, Body: {$usersResponse->body()}");

                break;
            }

            $users = $users->concat($usersResponse->json());

            $page = (int) $usersResponse->header('x-next-page');
        }

        return $users;
    }

    private function hasOpenItems(array $userProfile): bool
    {
        return $userProfile['todos'] > 0
            || $userProfile['assigned_merge_requests'] > 0
            || $userProfile['review_requested_merge_requests'] > 0;
    }

    private function gitLab(): PendingRequest
    {
        return Http::baseUrl(config('dashboard.tiles.gitlab.api_url'))
            ->withHeaders(['PRIVATE-TOKEN' => config('dashboard.tiles.gitlab.api_token')]);
    }
}
