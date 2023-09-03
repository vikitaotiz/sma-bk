<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ["name", "uuid"];

    /**
     * The users that belong to the Department
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'department_user', 'department_id', 'user_id');
    }

    public function inventories() {
        return $this->hasMany(Inventory::class);
    }

    public function products() {
        return $this->hasMany(Product::class);
    }

    public function accounts() {
        return $this->hasMany(Account::class);
    }

    public function bills() {
        return $this->hasMany(Bill::class);
    }
}
