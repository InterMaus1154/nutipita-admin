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
                    <x-form.form-input id="fin_record_date" step="any" type="number" name="fin_record_amount" placeholder="Amount £"></x-form.form-input>
                </x-form.form-wrapper>
                <x-form.form-wrapper>
                    <x-form.form-label id="fin_record_type">Type</x-form.form-label>
                    <x-ui.select.select name="fin_record_type" wrapper-class="sm:w-[150px]">
                        <x-slot:options>
                            <x-ui.select.option text="Clear" value=""/>
                            @foreach(FinancialRecordType::cases() as $type)
                                <x-ui.select.option :text="$type->value" :value="$type->name"/>
                            @endforeach
                        </x-slot:options>
                    </x-ui.select.select>
                </x-form.form-wrapper>
                <x-form.form-wrapper>
                    <x-form.form-label id="fin_cat_id">Category</x-form.form-label>
                    <x-ui.select.select name="fin_cat_id" wrapper-class="sm:w-[150px]">
                        <x-slot:options>
                            <x-ui.select.option value="" text="Clear"/>
                            @foreach($categories as $category)
                                <x-ui.select.option :value="$category->fin_cat_id" :text="$category->fin_cat_name"/>
                            @endforeach
                        </x-slot:options>
                    </x-ui.select.select>
                </x-form.form-wrapper>
                <livewire:fin-record.file-import :categories="$categories"/>
            </div>
            <flux:button type="submit" variant="primary">Add</flux:button>
        </form>

    </x-page-section>
</x-flux-layout>
