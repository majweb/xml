<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function products()
    {

        return $this->hasMany(OrderProduct::class);
    }

    protected function dateOfIssue(): Attribute
    {
        return Attribute::make(
            get: fn ($value) =>  $value ? Carbon::parse($value)->format('d-m-Y H:i:s') : null,
        );
    }
    protected function dateOfReturn(): Attribute
    {
        return Attribute::make(
            get: fn ($value) =>  $value ? Carbon::parse($value)->format('d-m-Y H:i:s') : null,
        );
    }
    protected function dateOfInvoice(): Attribute
    {
            return Attribute::make(
                get: fn ($value) =>  $value ? Carbon::parse($value)->format('d-m-Y H:i:s') : null,
            );
        
    }
    protected function orderDate(): Attribute
    {
            return Attribute::make(
                get: fn ($value) =>  $value ? Carbon::parse($value)->format('d-m-Y H:i:s') : null,
            );
        
    }


}
