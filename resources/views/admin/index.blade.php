<x-flux-layout>
    <x-page-section>
        <x-page-heading title="Today Orders"/>
        @if($orders->isEmpty())
            <em>No due orders for today!</em>
        @else
            <flux:link href="{{route('today.order.pdf')}}">Download total order PDF</flux:link>
            <div class="flex gap-6 flex-wrap">
                <div class="flex flex-col gap-2 bg-white border border-gray-200 shadow-2xs rounded-xl p-4 md:p-5 dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 text-center items-center text-xl">
                    <span>Daily Income</span>
                    <span>£{{$totalDayIncome}}</span>
                </div>
                <div class="flex flex-col gap-2 bg-white border border-gray-200 shadow-2xs rounded-xl p-4 md:p-5 dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 text-center items-center text-xl">
                    <span>Total pita</span>
                    <span>{{$totalDayPita}}</span>
                </div>
                @foreach($productTotals as $key => $value)
                    <div class="flex flex-col gap-2 bg-white border border-gray-200 shadow-2xs rounded-xl p-4 md:p-5 dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 text-center items-center text-xl">
                        <div>{{$key}}</div>
                        <div>{{$value}}</div>
                    </div>
                @endforeach
            </div>
            @include('livewire.order-list')
        @endif
    </x-page-section>
</x-flux-layout>
