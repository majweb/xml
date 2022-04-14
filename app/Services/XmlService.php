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
                'BuyerItemCode'=>$product['buyer_item_code'],
                'ItemDescription'=>$product['item_description'],
                'OrderedQuantity'=>$product['ordered_quantity_updated'],
                'UnitOfMeasure'=>$product['unit_of_measure'],
                'ExpectedDeliveryDate'=>$product['expected_delivery_date'],
            ];
        }
        return $productsAfter;
    }
    
    public function convertAllXml($xml,$products)
    {
  
        $arrayToXmlAfterConvert = [
            'OrderResponse-Header'=>[
                'OrderResponseNumber'=>1,
                'OrderResponseDate'=>now()->format('Y-m-d'),
                'ExpectedDeliveryDate'=>Carbon::parse($xml['expected_delivery_date'])->format('Y-m-d'),
                'OrderNumber'=>$xml['order_number'],
                'OrderDate'=>Carbon::parse($xml['order_date'])->format('Y-m-d'),
                'ResponseType'=>29,
            ],
            'OrderResponse-Parties'=>[
                'Buyer'=>[
                    'ILN'=>$xml['buyer_iln']
                ],
                'Invoicee'=>[
                    'ILN'=>1
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
                'TotalLines'=>count($xml['products'])
            ]
        ];
        return $arrayToXmlAfterConvert;
    }

}
