<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendorProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'store_name',
        'store_slug',
        'description',
        'phone',
        'address',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
