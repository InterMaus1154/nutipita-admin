<div class="w-full">
    <x-modal.wrapper title="Place New Order" size="7xl">
        <x-slot:content>
            <p>test</p>
        </x-slot:content>
        <x-slot:footer>
            <div class="flex justify-end gap-4">
                <flux:button variant="primary">Save</flux:button>
                <flux:button variant="danger" wire:click="cancel">Cancel</flux:button>
            </div>
        </x-slot:footer>
    </x-modal.wrapper>
</div>
