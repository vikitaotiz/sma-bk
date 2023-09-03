<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        "name",
        "uuid",
        "user_id",
        "quantity",
        "quantity_left",
        "category_id",
        "buying_price",
        "selling_price",
        "measurement_id",
        "department_id"
    ];

    protected $hidden = ['id'];

    public function category() {
        return $this->belongsTo(Category::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function measurement() {
        return $this->belongsTo(Measurement::class);
    }

    public function department() {
        return $this->belongsTo(Department::class);
    }

    public function inventories() {
        return $this->hasMany(Inventory::class);
    }
}
