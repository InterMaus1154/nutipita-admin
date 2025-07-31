@use(Carbon\Carbon)
<x-flux-layout>
    <x-page-section>
        @php($week = now()->weekOfYear())
        <x-page-heading title="Today Orders - Week {{$week}}"/>
        @if($orders->isEmpty())
            <em>No due orders for today!</em>
        @else
            <div class="flex flex-col gap-8">
                {{--day time orders--}}
                <div class="flex flex-col gap-4">
                    <div class="flex gap-4">
                        <flux:icon.sun />
                        <h3 class="font-bold">Day Time Orders</h3>
                    </div>
                    <flux:link>
                        Place New Order for Day Time
                    </flux:link>
                    <flux:link>Download total day orders PDF</flux:link>
                    <flux:badge color="orange">Coming soon...</flux:badge>
                </div>
                <flux:separator />
                {{--night time orders--}}
                <div class="flex flex-col gap-4">
                    <div class="flex gap-4">
                        <flux:icon.moon />
                        <h3 class="font-bold">Night Time Orders</h3>
                    </div>
                    <flux:link href="{{route('orders.create', ['order_placed_at' => now()->toDateString(), 'order_due_at' => now()->addDay()->toDateString()])}}">Place
                        New Order for Tonight
                    </flux:link>
                    <flux:link href="{{route('today.order.pdf')}}">Download total night orders PDF</flux:link>
                    <livewire:order-list :withSummaryData="true" :summaryVisibleByDefault="true" :filters="['due_from'=>$dueDate, 'due_to' => $dueDate]"/>
                </div>
            </div>
        @endif
    </x-page-section>
</x-flux-layout>
