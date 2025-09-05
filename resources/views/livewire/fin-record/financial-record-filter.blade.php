@use(App\Enums\FinancialRecordType)
<div class="flex flex-col gap-4 ">
    <div class="flex gap-8 justify-center sm:justify-between 2xl:justify-evenly flex-wrap sm:grid grid-cols-3 items-center">
        <div class="flex gap-4 items-center 2xl:justify-self-end">
            <x-form.form-wrapper>
                <x-form.form-label id="category">Category</x-form.form-label>
                <x-form.form-select id="category" wire-model-live="category_id">
                    <option value=""></option>
                    @foreach($financialCategories as $category)
                        <option value="{{$category->fin_cat_id}}">{{$category->fin_cat_name}}</option>
                    @endforeach
                </x-form.form-select>
            </x-form.form-wrapper>
        </div>
        <div class="flex flex-col gap-2 mt-8 sm:flex-row sm:justify-center sm:items-center">
            <flux:button :variant="$selectedType === \App\Enums\FinancialRecordType::INCOME ? 'primary' : 'filled'" class="sm:order-1 hidden! sm:block!" wire:click="toggleType('{{FinancialRecordType::INCOME}}')">
                <flux:icon.banknote-arrow-up/>
            </flux:button>
            <x-form.quick-date-buttons
                :activePeriod="$activePeriod"
                :months="$months"
                class="w-full sm:w-auto order-1 sm:order-2"
            />
            <flux:button
                :variant="$selectedType === \App\Enums\FinancialRecordType::EXPENSE ? 'primary' : 'filled'"
                class="hidden! sm:block! sm:order-3" wire:click="toggleType('{{FinancialRecordType::EXPENSE}}')">
                <flux:icon.banknote-arrow-down />
            </flux:button>

            {{--mobile only bullshit--}}
            <div class="flex gap-2 justify-between sm:hidden order-2 sm:order-none">
                <flux:button :variant="$selectedType === \App\Enums\FinancialRecordType::EXPENSE ? 'primary' : 'filled'" class="order-2 sm:order-1" wire:click="toggleType('{{FinancialRecordType::EXPENSE}}')">
                    <flux:icon.banknote-arrow-down />
                </flux:button>
                <flux:button :variant="$selectedType === \App\Enums\FinancialRecordType::INCOME ? 'primary' : 'filled'" wire:click="toggleType('{{FinancialRecordType::INCOME}}')">
                    <flux:icon.banknote-arrow-up />
                </flux:button>
            </div>
        </div>
        {{--due date inputs--}}
        <div class="flex gap-6 justify-self-end 2xl:justify-self-start flex-wrap">
            <x-form.form-wrapper>
                <x-form.form-label id="due_from" text="Due From"/>
                <x-form.form-input type="date" id="due_from" wireModelLive="due_from" placeholder="Due From"/>
            </x-form.form-wrapper>
            <x-form.form-wrapper>
                <x-form.form-label id="due_to" text="Due To"/>
                <x-form.form-input type="date" id="due_to" wireModelLive="due_to" placeholder="Due To"/>
            </x-form.form-wrapper>
        </div>
    </div>
</div>
