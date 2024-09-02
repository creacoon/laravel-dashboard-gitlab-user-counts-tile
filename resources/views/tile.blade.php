<x-dashboard-tile :position="$position" :refresh-interval="$refreshIntervalInSeconds">
    <div class="p-4 h-full">
        <div class="flex items-center justify-center mb-4">
            <div class="font-medium text-dimmed text-sm uppercase tracking-wide tabular-nums">
                GitLab User Counts
            </div>
        </div>
        <div wire:poll.{{ $refreshIntervalInSeconds }}s class="h-full overflow-y-auto">
            <table class="min-w-full divide-y divide-white">
                <thead>
                <tr>
                    @foreach(['', 'To-dos', 'Merge Requests', 'Review Requested'] as $header)
                        <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ $header }}</th>
                    @endforeach
                </tr>
                </thead>
                <tbody class="divide-y divide-white">
                @foreach($userCounts as $user => $counts)
                    <tr class="bg-transparent">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-6 h-6 rounded-full bg-gray-200 flex items-center justify-center mr-2 overflow-hidden">
                                    @if(isset($counts['avatar_url']))
                                        <img src="{{ $counts['avatar_url'] }}" alt="{{ $user }}" class="w-full h-full object-cover">
                                    @else
                                        <span class="text-gray-500 text-xs">{{ strtoupper(substr($user, 0, 1)) }}</span>
                                    @endif
                                </div>
                                <div class="font-medium text-gray-200">{{ $user }}</div>
                            </div>
                        </td>
                        <td class="text-gray-200 px-6 py-4 whitespace-nowrap">{{ $counts['todos'] }}</td>
                        <td class="text-gray-200 px-6 py-4 whitespace-nowrap">{{ $counts['assigned_merge_requests'] }}</td>
                        <td class="text-gray-200 px-6 py-4 whitespace-nowrap">{{ $counts['review_requested_merge_requests'] }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-dashboard-tile>
