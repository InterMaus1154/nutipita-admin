<x-flux-layout>
    <x-page-section>
        <x-page-heading title="Add New Financial Category"/>
        <div class="mx-auto">
            <x-error />
            <x-success />
        </div>
        <form method="POST" action="{{route('financial-categories.store')}}" class="flex flex-col gap-4">
            @csrf
            <x-form.form-wrapper>
                <x-form.form-label id="fin_cat_name">Category Name</x-form.form-label>
                <x-form.form-input id="fin_cat_name" placeholder="Category Name"></x-form.form-input>
            </x-form.form-wrapper>
            <flux:button type="submit" variant="primary">Add</flux:button>
        </form>
    </x-page-section>
</x-flux-layout>
