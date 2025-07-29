<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    protected $fillable = ['title', 'description', 'role', 'thumbnail', 'jumlah_video'];

    public function videos()
    {
        return $this->hasMany(Video::class);
    }

    public function pdfs()
    {
        return $this->hasMany(Pdf::class);
    }
}
