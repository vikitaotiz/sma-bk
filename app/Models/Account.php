<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    use HasFactory;

    protected $fillable = [
        "uuid",
        "production_cost",
        "expected_cash",
        "expected_mpesa",
        "expected_mpesa_cash",
        "expected_card",
        "expected_debt",
        "actual_cash",
        "actual_mpesa",
        "user_id",
        "department_id"
    ];

    protected $hidden = ["id"];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function department() {
        return $this->belongsTo(Department::class);
    }
}
