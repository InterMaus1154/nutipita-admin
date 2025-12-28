<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Carbon\WeekDay;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class OrderController extends Controller
{
    /*
     * Show order list page
     */
    public function index(): View
    {
        return view('orders.index');
    }

    /*
     * Show create order form
     */
    public function create(): View
    {
        return view('orders.create');
    }

    /*
     * Create new order
     */
    public function store(StoreOrderRequest $request): RedirectResponse
    {
        $customer_id = $request->validated('customer_id');
        $order_placed_at = $request->validated('order_placed_at');
        $order_due_at = $request->validated('order_due_at');
        $is_daytime = $request->input('shift') === "day";

        $products = collect($request->array('products'));

        // products with more than 0 quantity
        $products = $products->filter(function ($qty) {
            return $qty > 0;
        });

        // if empty, all products amount is 0, which is invalid for an order
        if ($products->isEmpty()) {
            return redirect()
                ->route('orders.create')
                ->withErrors(['invalid_amount' => 'At least one product need to be more than 0!'])
                ->withInput();
        }

        DB::beginTransaction();
        try {
            $customer = Customer::find($customer_id);
            // create order for customer
            $order = $customer->orders()->create([
                'order_status' => OrderStatus::Y_CONFIRMED->name,
                'order_placed_at' => $order_placed_at,
                'order_due_at' => $order_due_at,
                'is_daytime' => $is_daytime
            ]);

            // create order products
            foreach ($products as $product_id => $qty) {
                // find product and set current customer for accurate pricing
                $product = Product::find($product_id)->setCurrentCustomer($customer);
                $order->products()->attach($product_id, [
                    'product_qty' => $qty,
                    'order_product_unit_price' => $product->price
                ]);
            }
            DB::commit();
            return redirect()
                ->route('orders.index')
                ->with('success', 'Order successfully created!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return redirect()->route('orders.create')
                ->withErrors(['error' => 'Error at creating order.', 'error_msg' => $e->getMessage()]);
        }
    }

    /*
     * Show order details page
     */
    public function show(Order $order)
    {
        $order->loadCount('products')
            ->loadMissing('customer:customer_id,customer_name', 'products.customPrices');
        return view('orders.show', compact('order'));
    }

    /*
     * Show edit order form
     */
    public function edit(Order $order)
    {
        $order->loadMissing('customer:customer_id,customer_name', 'products');
        // TODO optimise products
        $products = Product::query()->select(['product_id', 'product_name', 'product_weight_g'])->get()->map(fn($product) => $product->setCurrentCustomer($order->customer));
        return view('orders.edit', compact('order', 'products'));
    }

    /*
     * Update an order
     */
    public function update(UpdateOrderRequest $request, Order $order)
    {
        $order->loadMissing('products');
        DB::beginTransaction();
        try {
            $order->update([
                'order_status' => $request->validated('order_status'),
                'order_placed_at' => $request->validated('order_placed_at'),
                'order_due_at' => $request->validated('order_due_at'),
                'is_daytime' => $request->input('shift') === 'day',
                // once an order is updated, it is no longer a standing order
                'is_standing' => false
            ]);
            $orderProducts = collect($order->products)->keyBy('product_id');
            foreach ($request->array('products') as $product_id => $quantity) {
                if ($quantity <= 0) {
                    if ($orderProducts->has($product_id)) {
                        $order->products()->detach($product_id);
                    } else {
                        continue;
                    }
                } else {
                    $price = Product::find($product_id)->setCurrentCustomer($order->customer)->price;
                    if ($orderProducts->has($product_id)) {
                        $order->products()->updateExistingPivot($product_id, [
                            'product_qty' => $quantity,
                            'order_product_unit_price' => $price
                        ]);
                    } else {
                        $order->products()->attach($product_id, [
                            'product_qty' => $quantity,
                            'order_product_unit_price' => $price
                        ]);
                    }
                }
            }
            DB::commit();
            return redirect()
                ->route('orders.index')
                ->with('success', 'Order updated successfully');
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            DB::rollBack();
            return redirect()
                ->route('orders.edit', compact('order'))
                ->withErrors(['order_update' => 'Error at updating order. No changes made']);

        }
    }

    /**
     * Create a summary pdf from the passed in orders
     * @param Request $request
     * @return Response
     */
    public function createSummaryPdf(Request $request)
    {
        // refetch orders
        $orderIds = $request->array('orderIds');
        $orders = Order::query()
            ->orderBy('order_placed_at')
            ->whereIn('order_id', $orderIds)
            ->get();


        // get all the unique product ids from the order products
        $productsInOrders = $orders->flatMap(function ($order) {
            return $order->products->pluck('product_id');
        })->unique();

        $productTotals = $orders->flatMap(function (Order $order) {
            return $order->products->map(function (Product $product) {
                return [
                    'product_id' => $product->product_id,
                    'quantity' => $product->pivot->product_qty
                ];
            });
        })
            ->groupBy('product_id')
            ->map(function ($items) {
                return $items->sum('quantity');
            });

        // TODO: optimise by getting data from the already loaderd orders or sth
        // get the customer for the orders
        $customer = $orders->first()->customer;

        // get only products that are in the order
        $products = Product::query()
            ->whereIn('product_id', $productsInOrders->toArray())
            ->select('product_name', 'product_weight_g', 'product_id')
            ->get();

        $periodTotal = $orders->sum('total_price');

        $firstOrderDate = $orders->min('order_due_at');
        $lastOrderDate  = $orders->max('order_due_at');

        $first = \Carbon\Carbon::parse($firstOrderDate);
        $last  = \Carbon\Carbon::parse($lastOrderDate);

        $week = [
            'start' => $first->copy()->startOfWeek(\Carbon\WeekDay::Sunday),
            'end'   => $last->copy()->endOfWeek(\Carbon\WeekDay::Saturday),
            'weekNum' => getCurrentWeekNumber($first)
        ];

        return Pdf::loadView('pdf.order-summary', compact('orders', 'products', 'customer', 'periodTotal', 'productTotals', 'week'))
            ->download("{$customer->customer_name}_Week_{$week['weekNum']}_Order_Summary.pdf");
    }
}
