<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Debtor extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        "uuid",
        "name",
        "phone",
        "email",
        "user_id"
    ];

    protected $hidden = ["id"];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function debt_records() {
        return $this->hasMany(DebtRecord::class);
    }

    public function bills() {
        return $this->hasMany(Bill::class);
    }
}
