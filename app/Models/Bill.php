<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Bill extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        "status",
        "bill_ref",
        "debtor_id",
        "uuid",
        "user_id",
        "selling_price",
        "actual_selling_price",
        "payment_mode_id",
        "department_id"
    ];

    protected $hidden = ["id"];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function payment_mode() {
        return $this->belongsTo(PaymentMode::class);
    }

    public function department() {
        return $this->belongsTo(Department::class);
    }

    public function sales() {
        return $this->hasMany(Sale::class);
    }

    public function debtor() {
        return $this->belongsTo(Debtor::class);
    }
}
