@php
    /**
* @var \App\Models\FinancialCategory $category
 */
@endphp
<x-flux-layout>
    <x-page-section>
        <x-page-heading title="Edit '{{$category->fin_cat_name}}'"/>
        <div class="mx-auto">
            <x-error />
            <x-success />
        </div>
        <form method="POST" action="{{route('financial-categories.update', compact('category'))}}" class="flex flex-col gap-4">
            @csrf
            @method('PUT')
            <x-form.form-wrapper>
                <x-form.form-label id="fin_cat_name">Category Name</x-form.form-label>
                <x-form.form-input id="fin_cat_name" name="fin_cat_name" placeholder="Category Name" value="{{$category->fin_cat_name}}"></x-form.form-input>
            </x-form.form-wrapper>
            <flux:button type="submit" variant="primary">Update</flux:button>
        </form>
    </x-page-section>
</x-flux-layout>
