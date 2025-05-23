<x-layout>
    <section class="page-section">
        <h2 class="section-title">Order #{{$order->order_id}}</h2>
        <div class="table-wrapper">
            <table>
                <tbody>
                <tr>
                    <td>Customer</td>
                    <td>
                        <a href="{{route('customers.show', ['customer' => $order->customer])}}" class="action-link">
                            {{$order->customer->customer_name}}
                        </a>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </section>
</x-layout>
