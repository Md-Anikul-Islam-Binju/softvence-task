<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'category',
        'feature_video_path',
        'meta',
        'feature_image'
    ];
    protected $casts = ['meta' => 'array'];

    public function modules()
    {
        return $this->hasMany(Module::class)->orderBy('position');
    }
}
