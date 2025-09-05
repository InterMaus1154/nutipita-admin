@use(App\Enums\FinancialRecordType)
<div class="mx-auto mt-10" x-data="{isEditMode: @entangle('isEditMode'), isLoading: @entangle('isLoading')}">
    <div class="relative">
        <label for="file"
               class="border-1 border-zinc-600 bg-neutral-900 rounded-md py-3 px-4 cursor-pointer text-neutral-500 hover:text-accent font-semibold">
            Import Data
        </label>
        <input type="file" id="file" class="absolute opacity-0" wire:model="file" accept=".csv" x-on:change="isEditMode = true; isLoading = true;"/>
    </div>
    <template x-if="isEditMode">
        <div class="fixed inset-4 bg-zinc-900 z-[200] overflow-y-scroll">
            <div class="absolute right-2 top-2 flex gap-2">
                <button type="button" class="cursor-pointer" wire:click="save">
                    <flux:icon.save class="text-accent size-10"/>
                </button>
                <button type="button" class="cursor-pointer" wire:click="closeEditor">
                    <flux:icon.x-circle class="text-accent size-10"/>
                </button>
            </div>
            <div class="flex flex-col gap-4 p-4">
                <h2 class="text-center text-2xl font-semibold">{{$file?->getClientOriginalName()}}</h2>
                <template x-if="isLoading">
                    <p class="text-2xl text-center">Loading...</p>
                </template>
                <template x-if="!isLoading">
                    <x-table.table>
                        <x-table.head>
                            @foreach($csvHeaders as $slug => $value)
                                <x-table.header>
                                    {{$value}}
                                    <button type="button" class="cursor-pointer" wire:click="removeColumn('{{$slug}}')">
                                        <flux:icon.x-circle class="text-accent size-6"/>
                                    </button>
                                </x-table.header>
                            @endforeach
                        </x-table.head>
                        <x-table.body>
                            @foreach($csvData as $row => $dataArray)
                                <x-table.row wire:key="row-{{$row}}">
                                    @foreach($csvHeaders as $slug => $value)
                                        <x-table.data>
                                            @if($slug === 'category_id')
                                                <x-form.form-select id="select-{{$row}}-{{$slug}}"
                                                                    wireKey="input-{{$row}}-{{$slug}}"
                                                                    wire-model="csvData.{{$row}}.{{$slug}}"
                                                                    class="mx-auto border-1 border-zinc-400!">
                                                    <option value="">Category</option>
                                                    @foreach($categories as $category)
                                                        <option
                                                            value="{{$category->fin_cat_id}}">{{$category->fin_cat_name}}</option>
                                                    @endforeach
                                                </x-form.form-select>
                                            @elseif($slug === 'type')

                                                <x-form.form-select id="select-{{$row}}-{{$slug}}"
                                                                    wireKey="input-{{$row}}-{{$slug}}"
                                                                    wire-model="csvData.{{$row}}.{{$slug}}"
                                                                    class="mx-auto border-1 border-zinc-400!">
                                                    <option value="">Type</option>
                                                    @foreach(FinancialRecordType::cases() as $recordType)
                                                        <option
                                                            value="{{$recordType->name}}">{{$recordType->value}}</option>
                                                    @endforeach
                                                </x-form.form-select>
                                            @else
                                                <x-form.form-input id="input-{{$row}}-{{$slug}}" type="text"
                                                                   wire-key="input-{{$row}}-{{$slug}}"
                                                                   wire-model="csvData.{{$row}}.{{$slug}}" class="mx-auto"/>
                                            @endif
                                        </x-table.data>
                                    @endforeach
                                </x-table.row>
                            @endforeach
                        </x-table.body>
                    </x-table.table>
                </template>
            </div>
        </div>
    </template>
</div>
