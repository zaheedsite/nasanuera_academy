<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pdf extends Model
{
    protected $fillable = [
        'subject_id',
        'title',
        'pages',
        'pdf_url',
    ];

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }
}
