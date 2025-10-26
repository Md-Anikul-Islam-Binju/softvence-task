<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Content extends Model
{
    use HasFactory;

    protected $fillable = [
        'module_id',
        'title',
        'type',
        'body',
        'media_path',
        'position',
        'meta'
    ];
    protected $casts = ['meta' => 'array'];

    public function module()
    {
        return $this->belongsTo(Module::class);
    }
}
