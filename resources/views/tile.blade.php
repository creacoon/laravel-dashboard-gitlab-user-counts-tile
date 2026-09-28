<x-dashboard-tile :position="$position" :refresh-interval="$refreshIntervalInSeconds">
    <div class="grid grid-rows-auto-1 gap-3 h-full">
        <div class="grid grid-cols-[1fr_repeat(3,5rem)] items-center gap-2 pr-3">
            <div class="font-medium text-dimmed text-sm uppercase tracking-wide">
                GitLab
            </div>
            @foreach(['To-dos', 'MRs', 'Reviews'] as $header)
                <div class="text-right text-xs font-medium uppercase tracking-wide text-dimmed">{{ $header }}</div>
            @endforeach
        </div>

        @if(empty($userCounts))
            <div class="flex items-center justify-center text-dimmed text-sm">
                No open items
            </div>
        @else
            <div class="flex flex-col gap-2">
                @foreach($userCounts as $user => $counts)
                    <div class="grid grid-cols-[1fr_repeat(3,5rem)] items-center gap-2 rounded-xl border border-white/5 bg-white/[0.04] px-3 py-2.5 text-base">
                        <div class="flex min-w-0 items-center gap-3">
                            @if(isset($counts['avatar_url']))
                                <img src="{{ $counts['avatar_url'] }}" alt="{{ $counts['name'] ?? $user }}" class="size-8 shrink-0 rounded-full object-cover ring-2 ring-white/10">
                            @else
                                <div class="flex size-8 shrink-0 items-center justify-center rounded-full bg-amber-500 text-sm font-semibold text-white ring-2 ring-white/10">
                                    {{ strtoupper(substr($counts['name'] ?? $user, 0, 1)) }}
                                </div>
                            @endif
                            <span class="truncate font-medium text-default">{{ $counts['name'] ?? $user }}</span>
                        </div>

                        @foreach(['todos', 'assigned_merge_requests', 'review_requested_merge_requests'] as $countKey)
                            <div @class([
                                'text-right font-semibold tabular-nums',
                                'text-default' => $counts[$countKey] > 0,
                                'text-dimmed opacity-50' => $counts[$countKey] === 0,
                            ])>
                                {{ $counts[$countKey] }}
                            </div>
                        @endforeach
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</x-dashboard-tile>
