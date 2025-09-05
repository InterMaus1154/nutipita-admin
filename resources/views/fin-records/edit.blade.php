@use(App\Enums\FinancialRecordType)
@php
    /**
* @var \App\Models\FinancialRecord $record
 */
@endphp
<x-flux-layout>
    <x-page-section>
        <x-page-heading :title="'Edit '.$record->fin_record_name"></x-page-heading>
        <div class="mx-auto">
            <x-error />
            <x-success />
        </div>
        <form method="POST" action="{{route('financial-records.update', compact('record'))}}" class="flex flex-col gap-4">
            @csrf
            @method('PUT')
            <div class="flex gap-4 flex-wrap flex-col sm:flex-row">
                <x-form.form-wrapper>
                    <x-form.form-label id="fin_record_name">Item Name</x-form.form-label>
                    <x-form.form-input id="fin_record_name" name="fin_record_name" placeholder="Item Name" value="{{$record->fin_record_name}}"></x-form.form-input>
                </x-form.form-wrapper>
                <x-form.form-wrapper>
                    <x-form.form-label id="fin_record_date">Date</x-form.form-label>
                    <x-form.form-input id="fin_record_date" type="date" name="fin_record_date" placeholder="Item Date" value="{{$record->fin_record_date}}"></x-form.form-input>
                </x-form.form-wrapper>
                <x-form.form-wrapper>
                    <x-form.form-label id="fin_record_amount">Amount</x-form.form-label>
                    <x-form.form-input id="fin_record_date" step="any" type="number" name="fin_record_amount" placeholder="Amount £" value="{{$record->fin_record_amount}}"></x-form.form-input>
                </x-form.form-wrapper>
                <x-form.form-wrapper>
                    <x-form.form-label id="fin_record_type">Type</x-form.form-label>
                    <x-form.form-select id="fin_record_type" name="fin_record_type">
                        <option value=""></option>
                        @foreach(FinancialRecordType::cases() as $type)
                            <option @selected($type->name === $record->fin_record_type) value="{{$type->name}}">{{$type->value}}</option>
                        @endforeach
                    </x-form.form-select>
                </x-form.form-wrapper>
                <x-form.form-wrapper>
                    <x-form.form-label id="fin_cat_id">Category</x-form.form-label>
                    <x-form.form-select id="fin_cat_id" name="fin_cat_id">
                        <option value=""></option>
                        @foreach($categories as $category)
                            <option @selected($record->fin_cat_id == $category->fin_cat_id) value="{{$category->fin_cat_id}}">{{$category->fin_cat_name}}</option>
                        @endforeach
                    </x-form.form-select>
                </x-form.form-wrapper>
            </div>
            <flux:button type="submit" variant="primary">Update</flux:button>
        </form>

    </x-page-section>
</x-flux-layout>
