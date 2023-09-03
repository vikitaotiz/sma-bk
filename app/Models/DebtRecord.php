<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class DebtRecord extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        "uuid",
        "debtor_id",
        "amount_paid",
        "balance",
        "bill_id",
        "user_id",
        "payment_mode_id"
    ];

    protected $hidden = ["id"];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function bill() {
        return $this->belongsTo(Bill::class);
    }

    public function  payment_mode(){
        return $this->belongsTo(PaymentMode::class);
    }

    public function  debtor(){
        return $this->belongsTo(Debtor::class);
    }
}
