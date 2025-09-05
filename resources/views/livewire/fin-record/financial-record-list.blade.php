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
    <div class="flex justify-center">
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
                    <x-table.data>{{$record->fin_record_name}}</x-table.data>
                    <x-table.data>@dayDate($record->fin_record_date)</x-table.data>
                    <x-table.data>@moneyFormat($record->fin_record_amount)</x-table.data>
                    <x-table.data>{{$record->category->fin_cat_name}}</x-table.data>
                    <x-table.data link>
                        <flux:link class="cursor-pointer" wire:click="deleteRecord('{{$record->fin_record_id}}')" wire:confirm="Are you sure to delete this record permanently?">
                            <flux:icon.trash />
                        </flux:link>
                    </x-table.data>
                </x-table.row>
            @endforeach
        </x-table.body>
    </x-table.table>
</div>
