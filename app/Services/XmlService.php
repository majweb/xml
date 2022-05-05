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
            
            $productsAfter[]=[
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
        $res=[];
        foreach($products as $key=>$product){
            $res['Line'][]=$product;
        }

        $sum=0;
        $arryChanged=[];
        foreach($res as $key=> $product){
            foreach($product as $el){
                $sum += $el['Line-Item']['QuantityToBeDelivered'];
                if($el['Line-Item']['OrderedQuantity'] != $el['Line-Item']['QuantityToBeDelivered']){
                    $arryChanged[]=true; 
                } 
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
            'OrderResponse-Lines'=>$res,
            'OrderResponse-Summary'=>[
                'TotalLines'=>count($xml['products']),
                'TotalOrderedAmount'=>$sum,
            ]
        ];
        return $arrayToXmlAfterConvert;
    }

}
