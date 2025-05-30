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

    .invoice-wrapper {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    header {
        background-color: #323332;
        padding: 1.75rem 1.5rem;
        display: grid;
        grid-template-columns: 1fr auto 1fr;
        align-items: center;
        color: #fff;
    }

    .logo-wrapper {
        max-width: 200px;
    }

    img {
        max-width: 100%;
    }

    .invoice-middle {
        text-transform: uppercase;
        color: #fff;
        font-size: 2rem;
        letter-spacing: 1px;
    }

    .company-data {
        display: flex;
        gap: 1rem;
        flex-direction: column;
        text-align: right;
        align-items: flex-end;

        strong {
            font-size: 1.5rem;
        }

        p {
            font-size: .9rem;
            line-height: 1.4;
        }
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

        display: flex;
        flex-direction: column;
        gap: 2rem;
    }

    .invoice-body-header {
        display: flex;
        justify-content: space-between;
    }

    .customer-data {
        display: flex;
        flex-direction: column;
        gap: .125rem;
        align-items: flex-start;
        text-align: left;

        strong {
            font-size: 1.5rem;
        }

        p {
            font-size: .9rem;
        }
    }

    .invoice-details {
        display: flex;
        flex-direction: column;
        gap: .5rem;
    }

    .invoice-details .invoice-detail {
        display: flex;
        justify-content: space-between;
        gap: 4rem;
        font-size: .9rem;
    }

    .invoice-items table {
        width: 100%;
        border-collapse: collapse;

        thead {
            background-color: #eee;
            font-weight: normal;
        }

        thead th {
            font-weight: normal;
            padding: .5rem;
            text-align: left;
            border-bottom: 1px solid #000;
        }

        td {
            padding: 1rem .5rem;
        }

        thead th:first-child,
        tbody td:first-child {
            width: 30px;
        }

        thead th:nth-child(2),
        tbody td:nth-child(2) {
            width: auto;
        }

        thead th:last-child,
        tbody td:last-child {
            width: 100px;
            text-align: right;
        }

        .product-data-desc {
            color: #9e9e9e;
            font-size: .9rem;
        }

        tfoot {
            width: 100%;
            background-color: #eee;
            border-bottom: 1px solid black;
        }
    }

    .invoice-items table thead, .invoice-items table tfoot {
        background-color: #eee;
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
                        Unit 13, Langhedge Industrial Estate<br>
                        London Enfield N18 2TQ<br>
                        United Kingdom
                    </p>
                    <p>
                        +44 7754 22632<br>
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
                <td class="text-left">
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
                    <div class="invoice-detail">
                        <span>Invoice#</span>
                        <span>{{$invoice->invoice_number}}</span>
                    </div>
                    <div class="invoice-detail">
                        <span>Invoice Date</span>
                        <span>{{$invoice->invoice_issue_date}}</span>
                    </div>
                    <div class="invoice-detail">
                        <span>Terms</span>
                        <span>Due on Receipt</span>
                    </div>
                    <div class="invoice-detail">
                        <span>Due Date</span>
                        <span>{{\Illuminate\Support\Carbon::parse($invoice->invoice_issue_date)->addDay()->toDateString()}}</span>
                    </div>
                </td>
            </tr>
        </table>
        <div class="invoice-items" style="margin-top: 18px;">
            <table>
                <thead style="background-color: #eee">
                <tr>
                    <th>#</th>
                    <th>ITEM & DESCRIPTION</th>
                    <th>AMOUNT</th>
                </tr>
                </thead>
                <tbody>
                @foreach($products as $index => $product)
                    <tr>
                        <td>{{$index+1}}</td>
                        <td class="product-data">
                            <span
                                class="product-data-name">{{$product->product_name}} {{$product->product_weight_g}}g</span>
                            <br>
                            <span class="product-data-desc">Pcs x Unit Price </span>
                        </td>
                        <td class="product-data">
                            <span
                                class="product-data-name">£{{$product->pivot->product_qty * $product->pivot->order_product_unit_price}}</span>
                            <br>
                            <span
                                class="product-data-desc">{{$product->pivot->product_qty}} x £{{$product->price}}</span>
                        </td>
                    </tr>
                @endforeach
                </tbody>
                <tfoot>
                <tr>
                    <td colspan="2" style="width: 25%">Bank Details</td>
                    <td style="text-align: right">
                        <strong style="font-size: 1.5rem;">
                            <span style="display: inline-block; margin-right: .5rem;">Total:</span>
                            £{{$order->total_price}}
                        </strong>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        Lloyds bank
                        <br>
                        <span
                            style="white-space: nowrap; margin-block: .5rem; display: inline-block;">Sort Code: 30 99 50</span>
                        <br>
                        <span style="white-space: nowrap">Account Number: 7226993</span>
                    </td>
                    <td></td>
                </tr>
                </tfoot>
            </table>
        </div>
    </main>
</div>
