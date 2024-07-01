<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use SoftDeletes, HasFactory;

    protected $table = 'items';

    protected $fillable = [
        'section',
        'item',
        'amount',
        'icon',
        'quota'
    ];
}
