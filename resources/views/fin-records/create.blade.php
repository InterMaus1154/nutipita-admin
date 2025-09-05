@use(App\Enums\FinancialRecordType)
<x-flux-layout>
    <x-page-section>
        <x-page-heading title="Add New Record"></x-page-heading>
        <div class="mx-auto">
            <x-error />
            <x-success />
        </div>
        <form method="POST" action="{{route('financial-records.store')}}" class="flex flex-col gap-4">
            @csrf
            <div class="flex gap-4 flex-wrap flex-col sm:flex-row">
                <x-form.form-wrapper>
                    <x-form.form-label id="fin_record_name">Item Name</x-form.form-label>
                    <x-form.form-input id="fin_record_name" name="fin_record_name" placeholder="Item Name"></x-form.form-input>
                </x-form.form-wrapper>
                <x-form.form-wrapper>
                    <x-form.form-label id="fin_record_date">Date</x-form.form-label>
                    <x-form.form-input id="fin_record_date" type="date" name="fin_record_date" placeholder="Item Date"></x-form.form-input>
                </x-form.form-wrapper>
                <x-form.form-wrapper>
                    <x-form.form-label id="fin_record_amount">Amount</x-form.form-label>
                    <x-form.form-input id="fin_record_date" type="number" name="fin_record_amount" placeholder="Amount £"></x-form.form-input>
                </x-form.form-wrapper>
                <x-form.form-wrapper>
                    <x-form.form-label id="fin_record_type">Type</x-form.form-label>
                    <x-form.form-select id="fin_record_type">
                        <option value=""></option>
                        @foreach(FinancialRecordType::cases() as $type)
                            <option value="{{$type->name}}">{{$type->value}}</option>
                        @endforeach
                    </x-form.form-select>
                </x-form.form-wrapper>
                <x-form.form-wrapper>
                    <x-form.form-label id="fin_cat_id">Category</x-form.form-label>
                    <x-form.form-select id="fin_cat_id">
                        <option value=""></option>
                        @foreach($categories as $category)
                            <option value="{{$category->fin_cat_id}}">{{$category->fin_cat_name}}</option>
                        @endforeach
                    </x-form.form-select>
                </x-form.form-wrapper>
            </div>
            <flux:button type="submit" variant="primary">Add</flux:button>
        </form>
    </x-page-section>
</x-flux-layout>
