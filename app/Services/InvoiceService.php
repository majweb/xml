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
            ->select('dok__Dokument.dok_Id','dok_Pozycja.ob_WartBrutto','dok__Dokument.dok_NrPelny','dok__Dokument.dok_NrPelnyOryg','dok_Pozycja.ob_DoId',
            'ob_TowId', 'tw__Towar.tw_Symbol', 'tw__Towar.tw_Nazwa', 'dok_Pozycja.ob_Ilosc', 'dok_Pozycja.ob_Jm',
            'dok_Pozycja.ob_CenaNetto', 'dok_Pozycja.ob_CenaBrutto',
            'dok_Pozycja.ob_WartNetto')
            ->get()
            ->groupBy('dok_NrPelny');


            $sums = $invoices->mapWithKeys(function ($group,$key) {
            return [$key => [
            'ob_WartNetto'=>$group->sum('ob_WartNetto'),
            'ob_WartBrutto'=>$group->sum('ob_WartBrutto'),
            'dok_NrPelnyOryg'=>$key,
            'reszta'=>$group->each(function($d){
            return [
            'dok_Id'=>$d->dok_Id,
            'dok_NrPelny'=>$d->dok_NrPelny,
            'dok_NrPelnyOryg'=>$d->dok_NrPelnyOryg,
            'dok_NrPelnyOryg'=>$d->dok_NrPelnyOryg,
            'ob_CenaBrutto'=>$d->ob_CenaBrutto,
            'ob_DoId'=>$d->ob_DoId,
            'ob_Ilosc'=>$d->ob_Ilosc,
            'ob_Jm'=>$d->ob_Jm,
            'ob_TowId'=>$d->ob_TowId,
            'ob_WartBrutto'=>$d->ob_WartBrutto,
            'ob_WartNetto'=>$d->ob_WartNetto,
            'tw_Nazwa'=>$d->tw_Nazwa,
            'tw_Symbol'=>$d->tw_Symbol
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

      public function convertAllXml()
      {
            $arrayToXmlAfterConvert = [
            'OrderResponse-Header'=>[
                  'OrderResponseNumber'=>1,
                  'OrderResponseDate'=>now()->format('Y-m-d'),
                  'ResponseType'=>29,
            ]
            ];
            return $arrayToXmlAfterConvert;
      }

}
