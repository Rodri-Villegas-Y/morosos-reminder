<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class UserSection extends Model
{
    use SoftDeletes, HasFactory;

    protected $table = 'users_sections';

    protected $fillable = [
        'user_id',
        'section_id',
    ];
}
