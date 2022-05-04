<?php
namespace App\Services;

use Carbon\Carbon;

class XmlService
{
    public function convertProduct($products)
    {
        $productsAfter=[];
        $test=[];
        $checkStatus=false;
        
        foreach($products as $key=> $product){
            if($product['ordered_quantity'] != $product['ordered_quantity_updated']){
                $checkStatus=true; 
            } else {
                $checkStatus=false; 
            }
            
            $productsAfter[$key]['Line']=[
                    'Line-Item'=>[
                        'LineNumber'=>$product['line_number'],
                        'EAN'=>$product['ean'],
                        'LineItemStatus'=>$checkStatus ? 3 : 5,
                        'ItemDescription'=>[
                            '_cdata' => $product['item_description']
                        ],
                        'OrderedQuantity'=>$product['ordered_quantity'],
                        'QuantityToBeDelivered'=>$product['ordered_quantity_updated'],
                    ]
                ];
            
        }
        return $productsAfter;
    }
    
    public function convertAllXml($xml,$products)
    {
        $sum=0;
        $arryChanged=[];
        foreach($products as $key=> $product){
                $sum += $product['Line']['Line-Item']['QuantityToBeDelivered'];
                if($product['Line']['Line-Item']['OrderedQuantity'] != $product['Line']['Line-Item']['QuantityToBeDelivered']){
                    $arryChanged[]=true; 
                } 
        }
        $arrayToXmlAfterConvert = [
            'OrderResponse-Header'=>[
                'OrderResponseNumber'=>1,
                'OrderResponseDate'=>now()->format('Y-m-d'),
                'OrderNumber'=>$xml['order_number'],
                'ResponseType'=>count($arryChanged) ? 4 : 29,
            ],
            'OrderResponse-Parties'=>[
                'Buyer'=>[
                    'ILN'=>$xml['buyer_iln']
                ],
                'Invoicee'=>[
                    'ILN'=>$xml['buyer_iln']
                ],
                'Seller'=>[
                    'ILN'=>$xml['seller_iln']
                ],
                'DeliveryPoint'=>[
                    'ILN'=>$xml['delivery_point_iln']
                ]
            ],
            'OrderResponse-Lines'=>$products,
            'OrderResponse-Summary'=>[
                'TotalLines'=>count($xml['products']),
                'TotalOrderedAmount'=>$sum,
            ]
        ];
        return $arrayToXmlAfterConvert;
    }

}
