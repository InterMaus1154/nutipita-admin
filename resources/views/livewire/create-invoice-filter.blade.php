<div>
    <div class="form-wrapper">
        <h2 class="form-title">Create New Invoices</h2>
        <x-error />
        <x-success />
        <form method="POST" action="{{route('invoices.store')}}">
            @csrf
            <div class="input-wrapper">
                <label for="customer_id">Customer</label>
                <select name="customer_id" id="customer_id" wire:model.live="customer_id">
                    <option value="">---Select a customer---</option>
                    @foreach($customers as $customer)
                        <option value="{{$customer->customer_id}}">{{$customer->customer_name}}</option>
                    @endforeach
                </select>
            </div>
            <div style="display: flex; gap: 1rem;">
                <div class="input-wrapper">
                    <label for="due_from">Due from:</label>
                    <input type="date" id="due_from" name="due_from" wire:model.live="due_from">
                </div>
                <div class="input-wrapper">
                    <label for="due_to">Due to:</label>
                    <input type="date" id="due_to" name="due_to" wire:model.live="due_to">
                </div>
            </div>
            <input type="submit" value="Generate Invoice" class="form-submit-button">
        </form>
    </div>
    @include('livewire.order-list')
</div>
