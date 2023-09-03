<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'uuid',
        'name',
        'active',
        'phone',
        'email',
        'password',
        'department_id',
        'device_token',
        'email_notify',
        'whatsapp_notify'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'id',
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * The roles that belong to the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_user', 'user_id', 'role_id');
    }

    /**
     * The departments that belong to the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function departments()
    {
        return $this->belongsToMany(Department::class, 'department_user', 'user_id', 'department_id');
    }

    public function categories() {
        return $this->hasMany(Category::class);
    }

    public function sales() {
        return $this->hasMany(Sale::class);
    }

    public function products() {
        return $this->hasMany(Product::class);
    }

    public function inventories() {
        return $this->hasMany(Inventory::class);
    }

    public function accounts() {
        return $this->hasMany(Account::class);
    }

    public function measurements() {
        return $this->hasMany(Measurement::class);
    }

    public function user_auths() {
        return $this->hasMany(UserAuth::class);
    }

    public function debtors() {
        return $this->hasMany(Debtor::class);
    }
}
