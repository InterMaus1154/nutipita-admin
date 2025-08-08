@use(Carbon\Carbon)
<x-flux-layout>
    <x-page-section>
        @php($week = now()->weekOfYear())
        <x-page-heading title="Today Orders - Week {{$week}}"/>
        @if($orders->isEmpty())
            <em>No due orders for today!</em>
        @else
            <div class="flex flex-col gap-8">
                {{-- Day Time Orders --}}
                <div class="flex flex-col gap-4">
                    <div class="flex gap-4">
                        <flux:icon.sun/>
                        <h3 class="font-bold">Day Time Orders</h3>
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

                <flux:separator/>

                {{-- Night Time Orders --}}
                <div class="flex flex-col gap-4">
                    <div class="flex gap-4">
                        <flux:icon.moon/>
                        <h3 class="font-bold">Night Time Orders</h3>
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
                            'due_from' => $dueDate,
                            'due_to' => $dueDate
                        ]"
                        />
                    </div>
                </div>
            </div>
        @endif
    </x-page-section>
    <script>
        Livewire.on("order-count-details", (event)=>{
            const detail = event[0];

            const dayTimeOrdersContainer = document.querySelector("#dayTimeOrdersContainer");
            const nightTimeOrdersContainer = document.querySelector('#nightTimeOrdersContainer');

            // check for day time orders
            if(detail.is_daytime_only && !detail.has_orders){
                dayTimeOrdersContainer.classList.add("hidden");
            }

            // check for night time orders
            if(detail.is_nighttime_only && !detail.has_orders){
                nightTimeOrdersContainer.classList.add("hidden");
            }
        });
    </script>
</x-flux-layout>
