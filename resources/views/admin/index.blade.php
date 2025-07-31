@use(Carbon\Carbon)
<x-flux-layout>
    <x-page-section>
        @php($week = now()->weekOfYear())
        <x-page-heading title="Today Orders - Week {{$week}}"/>
        <flux:link href="{{route('orders.create', ['order_placed_at' => now()->toDateString(), 'order_due_at' => now()->addDay()->toDateString()])}}">Place
            New Order for Today
        </flux:link>
        @if($orders->isEmpty())
            <em>No due orders for today!</em>
        @else
            <flux:link href="{{route('today.order.pdf')}}">Download total order PDF</flux:link>
{{--            <div class="flex gap-6 flex-wrap">--}}
{{--                <x-data-box dataBoxHeader="Daily Income" :dataBoxValue="moneyFormat($totalDayIncome)"/>--}}
{{--                <x-data-box dataBoxHeader="Total Pita" :dataBoxValue="amountFormat($totalDayPita)"/>--}}
{{--                @foreach($productTotals as $key => $value)--}}
{{--                    <x-data-box :dataBoxHeader="$key" :dataBoxValue="amountFormat($value)"/>--}}
{{--                @endforeach--}}
{{--            </div>--}}
            <livewire:order-list :withSummaryData="true" :summaryVisibleByDefault="true" :filters="['due_from'=>$dueDate, 'due_to' => $dueDate]"/>
        @endif
    </x-page-section>
</x-flux-layout>
