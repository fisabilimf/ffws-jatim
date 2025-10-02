<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GeojsonFile extends Model
{
    protected $fillable = [
        'original_name',
        'stored_path',
        'disk',
        'size',
        'mime_type',
        'sha256',
        'label',
    ];
}
