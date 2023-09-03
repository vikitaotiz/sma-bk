<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ["name", "uuid", "user_id", "bill_id", "status", "quantity"];

    protected $hidden = ["id"];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function bill() {
        return $this->belongsTo(Bill::class);
    }
}
