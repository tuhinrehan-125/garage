<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table ='invoices';



    public function invoiceItems(){
        return $this->hasMany(InvoiceItem::class);
    }

    public function client(){
        return $this->belongsTo(Contact::class,'contact_id','id');
    }


}
