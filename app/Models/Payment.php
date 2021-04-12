<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $filable = [
        'client_id', 'select_mode', 'by_cash', 'by_bank', 'bank_id',
        'by_bKash', 'from_bKash', 'to_bKash', 'adjust_advance',
        'payment_amount', 'delete_status',
    ];
    public function Client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }
    public function Bank()
    {
        return $this->belongsTo(Bank::class, 'bank_id');
    }
    public function Mode()
    {
        return $this->belongsTo(Mode::class, 'select_mode');
    }
    public function Order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function getCreatedAtAttribute($value)
    {
        return date("d-m-Y", strtotime($value));
    }
}
