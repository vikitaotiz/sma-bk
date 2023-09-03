<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Measurement extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ["name", "uuid", "user_id"];

    protected $hidden = ['id'];

    public function inventories() {
        return $this->hasMany(Inventory::class);
    }

    public function products() {
        return $this->hasMany(Product::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }
}
