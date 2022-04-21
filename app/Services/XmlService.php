<?php
namespace App\Services;

use Carbon\Carbon;

class XmlService
{
    public function convertProduct($products)
    {
        $productsAfter=[];
        foreach($products as $key=> $product){
            $productsAfter['Line-Item'][]=
            [
                'LineNumber'=>$product['line_number'],
                'EAN'=>$product['ean'],
                'LineItemStatus'=>5,
                'ItemDescription'=>[
                    '_cdata' => $product['item_description']
                ],
                'OrderedQuantity'=>$product['ordered_quantity'],
                'QuantityToBeDelivered'=>$product['ordered_quantity_updated'],
            ];
        }
        return $productsAfter;
    }
    
    public function convertAllXml($xml,$products)
    {
        $sum=0;
        foreach($products as $key=> $product){
            foreach($product as $el){
                $sum += $el['QuantityToBeDelivered'];
            }
        }

        $arrayToXmlAfterConvert = [
            'OrderResponse-Header'=>[
                'OrderResponseNumber'=>1,
                'OrderResponseDate'=>now()->format('Y-m-d'),
                'OrderNumber'=>$xml['order_number'],
                'ResponseType'=>29,
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
            'OrderResponse-Lines'=>[
                'Line'=>
                    $products
                
            ],
            'OrderResponse-Summary'=>[
                'TotalLines'=>count($xml['products']),
                'TotalOrderedAmount'=>$sum,
            ]
        ];
        return $arrayToXmlAfterConvert;
    }

}
