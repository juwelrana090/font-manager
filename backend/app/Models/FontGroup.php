<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FontGroup extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
    ];

    // Hide created_at and updated_at attributes
    protected $hidden = ['created_at', 'updated_at'];

    // Define the many-to-many relationship with Font
    public function fonts()
    {
        return $this->belongsToMany(Font::class, 'font_group_fonts');
    }
}
