<?php

namespace App\Http\Controllers;

use App\DataTransferObjects\OrderSummaryDto;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class DashboardController extends Controller
{
    /**
     * Show main dashboard
     */
    public function showDashboard()
    {
        return view('admin.index');
    }

    public function createOrderTotalPdf(Request $request)
    {
        $type = $request->get('type');
        $summaryDto = session('dto');
        if(!$summaryDto){
            abort(404);
        }
        return Pdf::loadView('pdf.order-total', compact('summaryDto', 'type'))
            ->download('order-total-'.$type.'-'. now()->toDateString().".pdf");
    }
}
