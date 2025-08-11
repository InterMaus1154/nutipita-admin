<div class="flex flex-col gap-8">
    {{-- Day Time Orders --}}
    @if($is_daytime)
        <div class="flex flex-col gap-4">
            <div class="flex gap-4 items-center justify-center">
                <flux:icon.sun/>
                <h3 class="font-bold text-xl sm:text-2xl">Day Time Orders</h3>
            </div>
            <flux:link
                href="{{ route('orders.create', [
                            'order_placed_at' => now()->toDateString(),
                            'order_due_at' => now()->toDateString(),
                            'is_daytime' => 1
                        ]) }}">
                Place New Order for Day Time
            </flux:link>
            <div class="flex flex-col gap-4" id="dayTimeOrdersContainer">
                <flux:link>Download total day orders PDF</flux:link>
                <livewire:order-list
                    wire:key="day-orders"
                    :withSummaryData="true"
                    :summaryVisibleByDefault="true"
                    :filters="[
                                'due_from'=>now()->toDateString(),
                                'due_to' => now()->toDateString(),
                                'daytime_only' => true
                            ]"
                />
            </div>
        </div>
    @endif

    @if($is_nighttime && $is_daytime)
        <flux:separator />
    @endif

    {{-- Night Time Orders --}}
    @if($is_nighttime)
        <div class="flex flex-col gap-4">
            <div class="flex gap-4 items-center justify-center">
                <flux:icon.moon/>
                <h3 class="font-bold text-xl sm:text-2xl">Night Time Orders</h3>
            </div>
            <flux:link
                href="{{ route('orders.create', [
                            'order_placed_at' => now()->toDateString(),
                            'order_due_at' => now()->addDay()->toDateString()
                        ]) }}">
                Place New Order for Tonight
            </flux:link>
            <div class="flex flex-col gap-4" id="nightTimeOrdersContainer">
                <flux:link href="{{ route('today.order.pdf') }}">
                    Download total night orders PDF
                </flux:link>
                <livewire:order-list
                    wire:key="night-orders"
                    :withSummaryData="true"
                    :summaryVisibleByDefault="true"
                    :filters="[
                            'due_from' => now()->addDay()->toDateString(),
                            'due_to' => now()->addDay()->toDateString()
                        ]"
                />
            </div>
        </div>
    @endif
</div>
