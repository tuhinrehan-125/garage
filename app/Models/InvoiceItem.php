<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InvoiceItem extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table ='invoice_items';
    protected $fillable=['invoice_id',"vehicle_id", "product_id", "service_id" ];


    public function invoice(){
        return $this->belongsTo(Invoice::class);
    }




}
