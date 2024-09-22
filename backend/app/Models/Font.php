<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Font extends Model
{
    use HasFactory;
    protected $fillable = [
        'title',
        'slug',
        'path',
    ];

    // Hide created_at and updated_at attributes
    protected $hidden = ['created_at', 'updated_at'];

    protected $appends = ['font_url'];

    public function getFontUrlAttribute()
    {
        return url($this->path);
    }

    public function fontGroups()
    {
        return $this->belongsToMany(FontGroup::class, 'font_group_fonts');
    }
}
