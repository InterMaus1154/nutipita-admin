<div class="flex flex-col gap-8">

    {{--both orders--}}
    @if($is_daytime && $is_nighttime)

    @endif

    {{-- Day Time Orders --}}
    @if($is_daytime)
        <div class="flex flex-col gap-4">
            <div class="flex gap-4 items-center justify-center">
                <flux:icon.sun/>
                <h3 class="font-bold text-xl sm:text-2xl">Day Time Orders</h3>
            </div>
            <div class="flex flex-col gap-4" id="dayTimeOrdersContainer">
                <livewire:order-list
                    wire:key="day-orders"
                    :withSummaryData="true"
                    :summaryVisibleByDefault="true"
                    :prop-filters="[
                                'due_from'=>now()->toDateString(),
                                'due_to' => now()->toDateString(),
                                'daytime_only' => true
                            ]"
                />
            </div>
        </div>
    @endif

    {{-- Night Time Orders --}}
    @if($is_nighttime)
        <div class="flex flex-col gap-4">
            <div class="flex gap-4 items-center justify-center">
                <flux:icon.moon/>
                <h3 class="font-bold text-xl sm:text-2xl">Night Time Orders</h3>
            </div>
            <div class="flex flex-col gap-4" id="nightTimeOrdersContainer">
                <livewire:order-list
                    wire:key="night-orders"
                    :withSummaryData="true"
                    :summaryVisibleByDefault="true"
                    :prop-filters="[
                            'due_from' => now()->addDay()->toDateString(),
                            'due_to' => now()->addDay()->toDateString(),
                            'nighttime_only' => true
                        ]"
                />
            </div>
        </div>
    @endif
</div>
