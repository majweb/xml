<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator as Paginator;

class InvoiceService
{
    public function getInvoices()
    {
        $term = request('szukaj');

        if(request()->has('szukaj')){
            $invoices = DB::connection('sqlsrv')->table('dok__Dokument')
            ->join('dok_Pozycja', 'dok__Dokument.dok_Id', '=', 'dok_Pozycja.ob_DokHanId')
            ->join('tw__Towar', 'dok_Pozycja.ob_TowId', '=', 'tw__Towar.tw_Id')
            ->when($term,function($query,$term){
            $query->where('dok__Dokument.dok_NrPelny', 'LIKE', '%' . $term . '%');
            })
            ->where('dok__Dokument.dok_typ',1)
            ->select('dok__Dokument.dok_Id','dok_Pozycja.ob_WartBrutto','dok_Pozycja.ob_VatProc','dok__Dokument.dok_WartVat','dok__Dokument.dok_Nr','dok__Dokument.dok_Podtyp','dok__Dokument.dok_DataZakonczenia','dok__Dokument.dok_DataWyst','dok__Dokument.dok_Waluta','dok__Dokument.dok_PlatTermin','dok__Dokument.dok_NrPelny','dok__Dokument.dok_NrPelnyOryg','dok_Pozycja.ob_DoId',
            'ob_TowId','ob_WartVat', 'tw__Towar.tw_Symbol','tw__Towar.tw_DniWaznosc', 'tw__Towar.tw_Nazwa', 'dok_Pozycja.ob_Ilosc', 'dok_Pozycja.ob_Jm',
            'dok_Pozycja.ob_CenaNetto', 'dok_Pozycja.ob_CenaBrutto',
            'dok_Pozycja.ob_WartNetto')
            ->get()
            ->groupBy('dok_NrPelny');


            $sums = $invoices->mapWithKeys(function ($group,$key) {
                $single = $group->first();
            return [$key => [
            'dok_Id'=>$single->dok_Id,
            'ob_WartNetto'=>$group->sum('ob_WartNetto'),
            'ob_WartBrutto'=>$group->sum('ob_WartBrutto'),
            'dok_WartVat'=>$single->dok_WartVat,
            'dok_NrPelnyOryg'=>$single->dok_NrPelnyOryg,
            'dok_NrPelny'=>$single->dok_NrPelny,
            'dok_Nr'=>$single->dok_Nr,
            'dok_DataWyst'=>$single->dok_DataWyst,
            'dok_PlatTermin'=>$single->dok_PlatTermin,
            'dok_Waluta'=>$single->dok_Waluta,
            'dok_DataZakonczenia'=>$single->dok_DataZakonczenia,
            'dok_Podtyp'=>$single->dok_Podtyp,
            'reszta'=>$group->each(function($d){
            return [
            'ob_CenaBrutto'=>$d->ob_CenaBrutto,
            'ob_CenaNetto'=>$d->ob_CenaNetto,
            'ob_DoId'=>$d->ob_DoId,
            'ob_Ilosc'=>$d->ob_Ilosc,
            'ob_Jm'=>$d->ob_Jm,
            'ob_TowId'=>$d->ob_TowId,
            'tw_Nazwa'=>$d->tw_Nazwa,
            'tw_Symbol'=>$d->tw_Symbol,
            'ob_VatProc'=>$d->ob_VatProc,
            'ob_WartVat'=>$d->ob_WartVat,
            'tw_DniWaznosc'=>$d->tw_DniWaznosc
            ];
            })
            ]
            ];
            })->toArray();
            $total=count($sums);
            $per_page = 5;
            $current_page = request()->input("page") ?? 1;

            $starting_point = ($current_page * $per_page) - $per_page;

            $sums = array_slice($sums, $starting_point, $per_page, true);

            $sums = new Paginator($sums, $total, $per_page, $current_page, [
            'path' => request()->url(),
            'query' => request()->query(),
            ]);
            return $sums;
        }
        
    }
    public function convertProduct($products,$order)
    {
        $productsAfter=[];
        $order->load('products');
        foreach($products as $key=> $product){
            $productsAfter['Line-Item'][]=
            [
                'LineNumber'=>$product['ob_TowId'],
                'EAN'=>$order->products[$key]->ean ?? null,
                'SerialNumber'=>$product['tw_Symbol'],
                'ItemDescription'=>$product['tw_Nazwa'],
                'ItemType'=>'IN',
                'InvoiceQuantity'=>$product['ob_Ilosc'],
                'UnitOfMeasure'=>$product['ob_Jm'],
                'InvoiceUnitNetPrice'=>$product['ob_CenaNetto'],
                'TaxRate'=>$product['ob_VatProc'],
                'TaxCategoryCode'=>'S',
                'TaxAmount'=>$product['ob_WartVat'],
                'NetAmount'=>$product['ob_WartNetto'],
                'ExpirationDate'=>$product['tw_DniWaznosc'] != 0 ? Carbon::parse($order->order_date)->addDays($product['tw_DniWaznosc'])->format('Y-m-d') : Carbon::parse($order->order_date)->addYears(5)->format('Y-m-d'),
                'Line-Order'=>[
                    'BuyerOrderNumber'=>$order->order_number,
                    'SupplierOrderNumber'=>$order->order_number,
                    'BuyerOrderDate'=>$order->order_date,
                ],
                'Line-Delivery'=>[
                    'DeliveryLocationNumber'=>$order->delivery_point_iln,
                    'DeliveryDate'=>$order->expected_delivery_date,
                    'DespatchNumber'=>$order->order_number
                ]
                ];
        }
        return $productsAfter;
    }

      public function convertAllXml($arrayToXml,$products,$order)
      {
            $taxes = collect($arrayToXml['reszta'])->groupBy('ob_VatProc');
            foreach($taxes as $key=>$el){
                    $result['Tax-Summary-Line'][]=[
                        'TaxRate'=>$key,
                        'TaxCategoryCode'=>'S',
                        'TaxAmount'=>$el->sum('ob_WartVat'),
                        'TaxableBasis'=>$el->sum('ob_WartNetto'),
                        'TaxableAmount'=>$el->sum('ob_WartNetto'),
                        'GrossAmount'=>$el->sum('ob_WartBrutto')
                    ];
                
            }


            $arrayToXmlAfterConvert = [
                'Invoice-Header'=>[
                    'InvoiceNumber'=>$arrayToXml['dok_Nr'],
                    'InvoiceDate'=>$arrayToXml['dok_DataWyst'],
                    'SalesDate'=>$arrayToXml['dok_PlatTermin'],
                    'InvoiceCurrency'=>$arrayToXml['dok_Waluta'],
                    'InvoicePaymentDueDate'=>$arrayToXml['dok_DataZakonczenia'],
                    'InvoicePaymentTerms'=>config('helpers.InvoicePaymentTerms'),
                    'DocumentFunctionCode'=>$arrayToXml['dok_Podtyp'],
                    'Order'=>[
                        'BuyerOrderNumber'=>$order->order_number,
                        'SupplierOrderNumber'=>$order->order_number,
                        'BuyerOrderDate'=>$order->order_date,
                    ],
                    'Delivery'=>[
                        'DeliveryLocationNumber'=>$order->delivery_point_iln,
                        'DeliveryDate'=>$order->expected_delivery_date,
                        'DespatchNumber'=>$order->order_number
                    ]
                ],
                'Invoice-Parties'=>[
                    'Buyer'=>[
                        'ILN'=>$order->buyer_iln,
                        'TaxID'=>config('helpers.NeucaNip'),
                        'Name'=>config('helpers.Name'),
                        'StreetAndNumber'=>config('helpers.StreetAndNumber'),
                        'CityName'=>config('helpers.CityName'),
                        'PostalCode'=>config('helpers.PostalCode'),
                        'Country'=>config('helpers.Country'),
                    ],
                    'Payer'=>[
                        'ILN'=>$order->buyer_iln,
                        'TaxID'=>config('helpers.NeucaNip'),
                        'Name'=>config('helpers.Name'),
                        'StreetAndNumber'=>config('helpers.StreetAndNumber'),
                        'CityName'=>config('helpers.CityName'),
                        'PostalCode'=>config('helpers.PostalCode'),
                        'Country'=>config('helpers.Country'),
                    ],
                    'Invoicee'=>[
                        'ILN'=>$order->buyer_iln,
                        'TaxID'=>config('helpers.NeucaNip'),
                        'Name'=>config('helpers.Name'),
                        'StreetAndNumber'=>config('helpers.StreetAndNumber'),
                        'CityName'=>config('helpers.CityName'),
                        'PostalCode'=>config('helpers.PostalCode'),
                        'Country'=>config('helpers.Country'),
                    ],
                    'Seller'=>[
                        'ILN'=>config('helpers.LarixIln'),
                        'TaxID'=>config('helpers.LarixIlnTaxId'),
                        'Name'=>config('helpers.LarixName'),
                        'StreetAndNumber'=>config('helpers.LarixStreetAndNumber'),
                        'CityName'=>config('helpers.LarixCityName'),
                        'PostalCode'=>config('helpers.LarixPostalCode'),
                        'Country'=>config('helpers.LarixCountry'),
                    ],
                    'Payee'=>[
                        'ILN'=>config('helpers.LarixIln'),
                        'TaxID'=>config('helpers.LarixIlnTaxId'),
                        'Name'=>config('helpers.LarixName'),
                        'StreetAndNumber'=>config('helpers.LarixStreetAndNumber'),
                        'CityName'=>config('helpers.LarixCityName'),
                        'PostalCode'=>config('helpers.LarixPostalCode'),
                        'Country'=>config('helpers.LarixCountry'),
                    ]
                ],
                'Invoice-Lines'=>[
                    'Line'=>$products
                ],
                'Invoice-Summary'=>[
                    'TotalLines'=>count($arrayToXml['reszta']),
                    'TotalNetAmount'=>$arrayToXml['ob_WartNetto'],
                    'TotalTaxableBasis'=>$arrayToXml['ob_WartNetto'],
                    'TotalTaxAmount'=>$arrayToXml['dok_WartVat'],
                    'TotalGrossAmount'=>$arrayToXml['ob_WartBrutto'],
                    'Tax-Summary'=>$result
                ]
            ];
            return $arrayToXmlAfterConvert;
      }

}
