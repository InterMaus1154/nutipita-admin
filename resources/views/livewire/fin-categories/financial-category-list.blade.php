<div class="flex flex-col">
    <div class="mx-auto">
        <x-error />
        <x-success />
    </div>
    <x-table.table class="sm:max-w-[25%] mx-auto">
        <x-table.head>
            <x-table.header>Name</x-table.header>
            <x-table.header>Actions</x-table.header>
        </x-table.head>
        <x-table.body>
            @foreach($financialCategories as $category)
                <x-table.row>
                    <x-table.data>{{$category->fin_cat_name}}</x-table.data>
                    <x-table.data link>
                        <flux:link class="cursor-pointer" href="{{route('financial-categories.edit', compact('category'))}}">
                            <flux:icon.pencil-square/>
                        </flux:link>
                        <flux:link class="cursor-pointer" wire:confirm="Are you sure to delete this category?" wire:click="delete({{$category->fin_cat_id}})">
                            <flux:icon.trash/>
                        </flux:link>
                    </x-table.data>
                </x-table.row>
            @endforeach
        </x-table.body>
    </x-table.table>
</div>
