<div class="flex flex-col gap-8">
    <livewire:homepage.homepage-toggler />
    <div class="flex flex-col gap-20">
        {{-- Day Time Orders --}}
        @if($isDaytime && $hasDayTimeOrders)
            <div class="flex flex-col gap-4" id="dayTimeOrders">
                <div class="flex flex-col gap-4" id="dayTimeOrdersContainer">
                    <livewire:order-list
                        wire:key="day-orders"
                        :withSummaryData="true"
                        :withIncome="false"
                        :with-mobile-sort="true"
                        :summaryVisibleByDefault="true"
                        :prop-filters="$dayTimeFilters"
                    />
                </div>
            </div>
        @endif
        @if($isDaytime && $isNighttime && $hasNightTimeOrders && $hasDayTimeOrders)
            {{--TODO--}}
            <div class="h-[1px] border-b-1 border-b-neutral-600 w-[75%] mx-auto justify-self-center"></div>
        @endif
        {{-- Night Time Orders --}}
        @if($isNighttime && $hasNightTimeOrders)
            <div class="flex flex-col gap-4" id="nightTimeOrders">
                <div class="flex flex-col gap-4" id="nightTimeOrdersContainer">
                    <livewire:order-list
                        wire:key="night-orders"
                        :withSummaryData="true"
                        :withIncome="false"
                        :with-mobile-sort="true"
                        :summaryVisibleByDefault="true"
                        :prop-filters="$nightTimeFilters"
                    />
                </div>
            </div>
        @endif
    </div>
</div>
