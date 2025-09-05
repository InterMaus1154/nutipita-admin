@php
    /**
* @var \App\Models\FinancialRecord $record
 */
@endphp
<div class="space-y-4">
    <div class="mx-auto">
        <x-success />
        <x-error />
    </div>
    @if($records->count() > 0)
        {{--summary section--}}
        <div class='grid grid-cols-2 lg:grid-cols-[repeat(auto-fit,minmax(0,1fr))] justify-center gap-4 2xl:max-w-[50%] mx-auto'>
            <x-data-box data-box-header="# Items" :data-box-value="amountFormat($itemsCount)"/>
            <x-data-box data-box-header="Total £" :data-box-value="moneyFormat($total)"/>
        </div>
        <x-table.table>
            <x-table.head>
                <x-table.header>Name</x-table.header>
                <x-table.header>Date</x-table.header>
                <x-table.header>Amount</x-table.header>
                <x-table.header>Category</x-table.header>
                <x-table.header>Actions</x-table.header>
            </x-table.head>
            <x-table.body>
                @foreach($records as $record)
                    <x-table.row wire:key="record-{{$record->fin_record_id}}">
                        <x-table.data>
                        <span class="text-accent">
                            {{$record->fin_record_name}}
                        </span>
                        </x-table.data>
                        <x-table.data>@dayDate($record->fin_record_date)</x-table.data>
                        <x-table.data>@moneyFormat($record->fin_record_amount)</x-table.data>
                        <x-table.data>{{$record->category?->fin_cat_name}}</x-table.data>
                        <x-table.data link>
                            <flux:link href="{{route('financial-records.edit', compact('record'))}}">
                                <flux:icon.pencil-square />
                            </flux:link>
                            <flux:link class="cursor-pointer" wire:click="deleteRecord('{{$record->fin_record_id}}')" wire:confirm="Are you sure to delete this record permanently?">
                                <flux:icon.trash />
                            </flux:link>
                        </x-table.data>
                    </x-table.row>
                @endforeach
            </x-table.body>
        </x-table.table>
    @endif
</div>
