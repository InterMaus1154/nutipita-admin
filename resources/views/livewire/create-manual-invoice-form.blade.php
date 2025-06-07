<div>
    <div class="form-wrapper">
        <x-error />
        <x-success />
        @if(session()->has('invoice'))
            <a href="{{route('invoices.download', ['invoice' => session()->get('invoice')])}}" class="action-link">
                Download invoice
            </a>
            <a href="{{route('invoices.view-inline', ['invoice' => session()->get('invoice')])}}" class="action-link">View</a>
        @endif
        <form method="POST" wire:submit="save">
            @csrf
            <h3>Invoice Info</h3>
            <div class="input-wrapper">
                <label for="customer_id">Customer*</label>
                <select name="customer_id" id="customer_id" wire:model.live="customer_id">
                    <option value="">---Select a customer---</option>
                    @foreach($customers as $customer)
                        <option value="{{$customer->customer_id}}">{{$customer->customer_name}}</option>
                    @endforeach
                </select>
            </div>
            <div class="input-wrapper">
                <label for="invoice_number">Invoice Number*</label>
                <input type="text" name="invoice_number" wire:model="invoice_number">
            </div>
            <div style="display: flex; gap: 1rem">
                <div class="input-wrapper">
                    <label for="invoice_issue_date">Invoice Issue Date*</label>
                    <input type="date" id="invoice_issue_date" name="invoice_issue_date"
                           value="{{now()->toDateString()}}" wire:model="invoice_issue_date">
                </div>
                <div class="input-wrapper">
                    <label for="invoice_due_date">Invoice Due Date*</label>
                    <input type="date" id="invoice_due_date" name="invoice_due_date"
                           value="{{now()->addDay()->toDateString()}}" wire:model="invoice_due_date">
                </div>
            </div>
            <h3>Order Info</h3>
            <div style="display: flex; gap: 1rem;">
                <div class="input-wrapper">
                    <label for="due_from">Order due from:</label>
                    <input type="date" id="due_from" name="due_from" wire:model.live="due_from">
                </div>
                <div class="input-wrapper">
                    <label for="due_to">Order due to:</label>
                    <input type="date" id="due_to" name="due_to" wire:model.live="due_to">
                </div>
            </div>
            @if(!empty($orders))
                @include('livewire.order-list')
            @endif
            <h3>Product Info</h3>
            @if(!empty($customer_id))
                @foreach($products as $product)
                    @php($product->setCurrentCustomer($customer_id))
                    <div class="input-wrapper">
                        <label
                            for="invoiceProducts[{{$product->product_id}}]">{{$product->product_name}} {{$product->price}}</label>
                        <input type="number" placeholder="Quantity" value="0"
                               id="invoiceProducts[{{$product->product_id}}]"
                               name="invoiceProducts[{{$product->product_id}}]"
                               wire:model="invoiceProducts.{{$product->product_id}}">
                    </div>
                @endforeach
            @else
                <em>Select a customer first</em>
            @endif
            <input type="submit" class="form-submit-button" value="Generate Invoice">
        </form>
    </div>
</div>
