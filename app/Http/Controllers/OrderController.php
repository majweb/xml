<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Order;
use App\Services\XmlService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Services\InvoiceService;
use Spatie\ArrayToXml\ArrayToXml;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Redirect;


class OrderController extends Controller
{
    public function index()
    {        
        request()->validate([
            'sortowanie' => ['in:asc,desc'],
            'pole' => ['in:id,date_of_issue']
        ]);
 
        $query = Order::query()->withCount('products');
 
        $query->when(request('szukaj'),function ($q) {
            $q->whereHas('products',function($m){
                $m->where('item_description', 'LIKE', '%'.request('szukaj').'%');
            });
        });
        $query->when(request(['pole', 'sortowanie']),function ($q) {
            $q->orderBy(request('pole'), request('sortowanie'));
        });
        $query->when(request('typ'),function ($q) {
            if(request('typ')=='Wszystkie'){
                return true;
            } else{
                $q->where('status',request('typ'));
            }
        });
    
        return Inertia::render('Orders',[
            'newOrders'=>Order::where('status','Nowe')->count(),
            'ConfirmOrders'=>Order::where('status','Potwierdzone')->count(),
            'InvoiceOrders'=>Order::where('status','Zafakturowane')->count(),
            'orders' => $query->orderBy('id','desc')->paginate(10)->withQueryString(),
            'filters' => request()->all(['szukaj', 'pole', 'sortowanie','typ'])
        ]);
    }

    public function getOrders()
    {
        $files = Storage::disk('ftpOut')->allFiles();
        if($files){
            $array=[];
            $orders=[];
            
            foreach ($files as $file) {
                if (pathinfo($file, PATHINFO_EXTENSION) == 'xml') {
                    $existFile = Order::where('filename',$file)->exists();
                    if(!$existFile){
                        $getFile = Storage::disk('ftpOut')->get($file);

                        Storage::disk('local')->put('xml/'.$file,$getFile);

                        $xml = simplexml_load_string($getFile);
                        $json = json_encode($xml);
                        $array = json_decode($json,true);

                        $order = Order::create([
                            'filename'=>$file,
                            'order_number'=>$array['Order-Header']['OrderNumber'],
                            'order_date'=>$array['Order-Header']['OrderDate'],
                            'expected_delivery_date'=>$array['Order-Header']['ExpectedDeliveryDate'],
                            'document_function_code'=>$array['Order-Header']['DocumentFunctionCode'],
                            'buyer_iln'=>$array['Order-Parties']['Buyer']['ILN'],
                            'seller_iln'=>$array['Order-Parties']['Seller']['ILN'],
                            'delivery_point_iln'=>$array['Order-Parties']['DeliveryPoint']['ILN'],
                            'date_of_issue'=>now(),         
                            'status'=>'Nowe'     
                        ]);
                        $orders[]=$order;
                        
                        foreach($array['Order-Lines']['Line'] as $product){
                            $order->products()->create([
                                'line_number'=>$product['Line-Item']['LineNumber'],
                                'ean'=>$product['Line-Item']['EAN'],
                                'buyer_item_code'=>$product['Line-Item']['BuyerItemCode'],
                                'item_description'=>$product['Line-Item']['ItemDescription'],
                                'ordered_quantity'=>$product['Line-Item']['OrderedQuantity'],
                                'ordered_quantity_updated'=>$product['Line-Item']['OrderedQuantity'],
                                'unit_of_measure'=>$product['Line-Item']['UnitOfMeasure'],
                                'expected_delivery_date'=>$product['Line-Item']['ExpectedDeliveryDate'],
                            ]);
                        }
                    }
                }
            }
        }


        return Redirect::back()->with(['message'=>'Sprawdzono stan zamówień.'.(count($orders) > 0 ? 'Dodano '.count($orders).' zamówienia' : ''),'newest'=>$orders]);
        
    }
    public function show(Order $order)
    {
        $order->load(['products'=>function($query){
            if(request()->has(['pole','sortowanie'])){
                $query->orderBy(request('pole'), request('sortowanie'));
            }
        }])->loadCount('products');
    

        return Inertia::render('OrdersSingle',[
            'order'=>$order,
            'orginal'=>$order,
            'filters' => request()->all(['pole', 'sortowanie'])
        ]);
    }

    public function update($id,XmlService $service){

        $validated = request()->validate([
            'products.*' => 'array',
            'products.*.id' => 'required',
            'products.*.ordered_quantity' => 'required|numeric',
            'products.*.ordered_quantity_updated' => 'required|numeric|min:0|lte:products.*.ordered_quantity',
        ],[
            'products.*'=>'Brak produktów', 
            'products.*.ordered_quantity_updated.required'=>'Produkt wymagany', 
            'products.*.ordered_quantity_updated.numeric'=>'Ilość musi być liczbą', 
            'products.*.ordered_quantity_updated.min'=>'Minimalna ilość to 0', 
            'products.*.ordered_quantity_updated.lte'=>'Maksymalna ilość po aktualizacji musi być mniejsza bądź równa ilości.'
        ]);
        
        $products = $validated['products'];

        $order = Order::where('id',$id)->first();

        foreach($products as $product){
            $order->products()->where('id',$product['id'])->update(['ordered_quantity_updated'=>$product['ordered_quantity_updated']]);
        }
        $order->load('products');
        $arrayToXml = $order->toArray();

        $productsUpdated=$service->convertProduct($arrayToXml['products']);
        
        $arrayToXmlAfterConvert = $service->convertAllXml($arrayToXml,$productsUpdated);
   
        $result = ArrayToXml::convert($arrayToXmlAfterConvert,'Document-OrderResponse', false, 'UTF-8', '1.0');

        $xml = simplexml_load_string($result);

        $path = $xml->asXml(storage_path('app/xml/').'updated'.$order->filename);

        $getFile = Storage::disk('local')->get('xml/updated'.$order->filename);

        Storage::disk('ftpIn')->put('updated'.$order->filename,$getFile);
        Storage::disk('local')->put('xml/'.'updated'.$order->filename,$getFile);
     
        $order->update(['status'=>'Potwierdzone','date_of_return'=>now()]);
        return Redirect::route('orders')->with('message','Zmieniono zamówienie');

    }

    public function invoiceSend(Order $order,Request $request)
    {
             $validated = request()->validate([
             'pdf' => 'nullable|file|mimes:pdf|max:5MB',
             'invoice' => 'required',
             ],[
             'invoice'=>'Brak Wybranej faktury',
            'pdf.mimes'=>'Plik musi być w formacie Pdf',
            'pdf.max'=>'Plik maksymalnie do 5MB'
             ]);
            dd($validated);
    }


    public function invoice(Order $order, InvoiceService $InvoiceService)
    {
            $invoices = NULL;
            $sums = $InvoiceService->getInvoices();
            return Inertia::render('OrdersSingleInvoice',[
                'order'=>$order,
                'invoices'=>isset($sums) ? $sums : $invoices,
                'filters' => request('szukaj') ?? null
            ]);
    }
}
