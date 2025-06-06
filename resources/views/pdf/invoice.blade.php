@use(Illuminate\Support\Carbon)
<style>
    * {
        box-sizing: border-box;
        margin: 0;
        padding: 0;
    }

    body {
        margin: 0;
        font-family: 'Roboto', sans-serif;
    }

    header {
        background-color: #323332;
        padding: 1.75rem 1.5rem;
        display: grid;
        grid-template-columns: 1fr auto 1fr;
        align-items: center;
        color: #fff;
    }

    img {
        max-width: 100%;
    }

    .company-data-cell p {
        margin: 8px 0;
    }

    .company-data-cell strong {
        font-size: 26px;
    }

    /*end of header*/

    /*start of main body content*/
    main {
        padding: 1.75rem 1.5rem;
    }

    .header-table td {
        width: 33.33%;
        vertical-align: middle;
    }

    .text-left {
        text-align: left;
    }

    .text-center {
        text-align: center;
    }

    .text-right {
        text-align: right;
    }

    /*table for main header with customer and invoice data*/
    .body-header-table {
        width: 100%;
        table-layout: fixed;
    }

    .body-header-table td {
        width: 50%;
        vertical-align: middle;
    }

    .body-header-table .customer-data strong {
        font-size: 1.125rem;
        margin-bottom: 4px;
    }

    .body-header-table .customer-data p {
        margin-bottom: 4px;
    }

    .body-header-table .invoice-detail {
        margin-bottom: 4px;
    }

    .body-header-table .invoice-detail span:first-child {
        margin-right: 8px;
    }

    .invoice-detail-table {
        width: 100%;
    }

    .invoice-detail-table td {
        text-align: right;
    }

    /*table for invoice items*/
    .invoice-items table {
        width: 100%;
        table-layout: fixed;
        border-collapse: collapse;
    }

    .invoice-items table thead {
        background-color: #eee;
    }

    .col-index {
        width: 5%;
        text-align: left;
    }

    .col-description {
        width: 75%;
        text-align: left;
    }

    .col-amount {
        width: 20%;
        text-align: right;
    }

    .product-data-desc {
        color: #6e6e6e;
        font-size: .9rem;
    }

    .invoice-items table thead, .invoice-items table tfoot {
        background-color: #eee;
    }

    .invoice-items tfoot td {
        padding-top: 1rem;
    }

    .invoice-items .footer-left {
        width: 50%;
        text-align: left;
        vertical-align: top;
    }

    .invoice-items .footer-left span {
        display: block;
        margin-bottom: 4px;
    }

    .invoice-items .footer-right {
        width: 50%;
        text-align: right;
        vertical-align: top;
    }

    .invoice-items thead th {
        border-top: 1px solid #000;
    }

    .invoice-items tbody td, .invoice-items thead th {
        padding: 0.75rem 0.5rem;
        vertical-align: top;
    }

    .invoice-items tfoot td {
        border-bottom: 1px solid #000;
        padding: 0.5rem;
    }

</style>
<div class="invoice-wrapper">
    <header>
        @php
            $logo = public_path('images/logo.png');
        @endphp
        <table class="header-table" style="width: 100%; table-layout: fixed; border-collapse: collapse">
            <tr>
                <!-- Logo -->
                <td class="text-left">
                    <img src="{{ $logo }}" alt="Logo" style="max-width: 200px">
                </td>
                <!-- Invoice Title -->
                <td class="text-center">
                    <div style="font-size: 26px; font-weight: bold;">INVOICE</div>
                </td>
                <!-- Company Data -->
                <td class="text-right company-data-cell">
                    <strong>Nuti Pita Limited</strong>
                    <p>
                        Unit 13, Langhedge Industrial Estate
                        <br>
                        London Enfield N18 2TQ
                        <br>
                        United Kingdom
                    </p>
                    <p>
                        +44 7754 22632
                        <br>
                        nutipita.co.uk
                    </p>
                    <p>
                        VAT: 486 4163 64
                    </p>
                </td>
            </tr>
        </table>

    </header>
    <main>
        <table class="body-header-table">
            <tr>
                {{--customer data--}}
                <td class="text-left customer-data">
                    <strong>
                        {{$customer->customer_name}}
                    </strong>
                    <p>
                        {{$customer->customer_address_1}}
                    </p>
                    @if($customer->customer_address_2)
                        <p>
                            {{$customer->customer_address_2}}
                        </p>
                    @endif
                    <p>
                        {{$customer->customer_city}}
                    </p>
                    <p>
                        {{$customer->customer_postcode}}
                    </p>
                    <p>
                        {{$customer->customer_country}}
                    </p>
                </td>
                {{--invoice data--}}
                <td class="text-right">
                    <table class="invoice-detail-table">
                        <tr>
                            <td>
                                <strong>Invoice#</strong>
                            </td>
                            <td>{{$invoice->invoice_number}}</td>
                        </tr>
                        <tr>
                            <td>
                                <strong>Invoice Date</strong>
                            </td>
                            <td>{{Carbon::parse($invoice->invoice_issue_date)->format('d/m/Y')}}</td>
                        </tr>
                        <tr>
                            <td>
                                <strong>Terms</strong>
                            </td>
                            <td>Due on Receipt</td>
                        </tr>
                        <tr>
                            <td>
                                <strong>Due Date</strong>
                            </td>
                            <td>{{Carbon::parse($invoice->invoice_due_date)->format('d/m/Y')}}</td>
                        </tr>
                    </table>

                </td>
            </tr>
        </table>
        <div class="invoice-items" style="margin-top: 18px;">
            <table>
                <thead>
                <tr>
                    <th class="col-index">#</th>
                    <th class="col-description text-left">ITEM & DESCRIPTION</th>
                    <th class="col-amount text-right">AMOUNT</th>
                </tr>
                </thead>
                <tbody>
                {{--if it is coming from a bulk invoice generation--}}
                @php($i = 1)
                @if(isset($fromBulk))
                    @foreach($products as $product)
                        <tr>
                            <td class="col-index">{{$i}}</td>
                            <td class="col-description">
                            <span
                                class="product-data-name">{{$product['product_name']}} {{$product['product_weight_g']}}g</span>
                                <br>
                                <span class="product-data-desc">Pcs x Unit Price </span>
                            </td>
                            <td class="col-amount">
                            <span
                                class="product-data-name">£{{$product['total_quantity'] * $product['unit_price']}}</span>
                                <br>
                                <span
                                    class="product-data-desc">{{$product['total_quantity']}} x £{{$product['unit_price']}}</span>
                            </td>
                        </tr>
                        @php($i++)
                    @endforeach
                @else
                    @foreach($products as $index => $product)
                        <tr>
                            <td class="col-index">{{$i}}</td>
                            <td class="col-description">
                            <span
                                class="product-data-name">{{$product->product_name}} {{$product->product_weight_g}}g</span>
                                <br>
                                <span class="product-data-desc">Pcs x Unit Price </span>
                            </td>
                            <td class="col-amount">
                            <span
                                class="product-data-name">£{{$product->pivot->product_qty * $product->pivot->order_product_unit_price}}</span>
                                <br>
                                <span
                                    class="product-data-desc">{{$product->pivot->product_qty}} x £{{$product->price}}</span>
                            </td>
                        </tr>
                        @php($i++)
                    @endforeach
                @endif
                </tbody>
                <tfoot>
                <tr>
                    <td colspan="2" class="footer-left">
                        <span>Bank Details</span>
                        <span>Lloyds bank</span>
                        <span>Sort Code: 30 99 50</span>
                        <span>Account Number: 7226993</span>
                    </td>
                    <td class="footer-right text-right">
                        <strong style="font-size: 1.25rem;">
                            <span>Total:</span>
                            £{{$totalPrice}}
                        </strong>
                    </td>
                </tr>
                </tfoot>
            </table>
        </div>
    </main>
</div>
