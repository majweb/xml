<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        request()->validate([
            'sortowanie' => ['in:asc,desc'],
            'pole' => ['in:id,date_of_issue']
        ]);
 
        $query = Order::query()->with('products');
 
        $query->when(request('szukaj'),function ($q) {
            $q->whereHas('products',function($m){
                $m->where('product', 'LIKE', '%'.request('szukaj').'%');
            });
        });
        

        if (request()->has(['pole', 'sortowanie'])) {
            $query->orderBy(request('pole'), request('sortowanie'));
        }

        return Inertia::render('Orders',[
            'newOrders'=>Order::where('status','Nowe')->count(),
            'ConfirmOrders'=>Order::where('status','Potwierdzone')->count(),
            'InvoiceOrders'=>Order::where('status','Zafakturowane')->count(),
            'orders' => $query->paginate(10)->withQueryString(),
            'filters' => request()->all(['szukaj', 'pole', 'sortowanie'])
        ]);
    }
}
