<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        "uuid",
        "quantity",
        "actual_quantity",
        "buying_price",
        "user_id",
        "department_id",
        "measurement_id",
        "product_id"
    ];

    protected $hidden = ['id'];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function product() {
        return $this->belongsTo(Product::class);
    }

    public function measurement() {
        return $this->belongsTo(Measurement::class);
    }

    public function department() {
        return $this->belongsTo(Department::class);
    }
}
