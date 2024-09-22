<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FontGroupFonts extends Model
{
    use HasFactory;
    protected $fillable = [
        'font_group_id',
        'font_id',
    ];
}
