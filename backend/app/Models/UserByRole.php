<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserByRole extends Model
{
    use HasFactory;

    protected $table = 'user_by_role';

    protected $fillable = [
        'phone_number',
        'upt_code',
        'role',
        'status',
        'bio',
    ];

    /**
     * Get the UPT that owns the user by role.
     */
    public function upt(): BelongsTo
    {
        return $this->belongsTo(MasUpt::class, 'upt_code', 'upts_code');
    }
}