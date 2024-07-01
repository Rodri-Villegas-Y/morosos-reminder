<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class ItemUser extends Model
{
    use SoftDeletes, HasFactory;

    protected $table = 'items_users';

    protected $fillable = [
        'item_id',
        'user_id',
    ];
}
