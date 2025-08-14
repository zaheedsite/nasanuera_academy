<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    protected $fillable = [
        'subject_id',
        'title',
        'description',
        'video_url',
        'duration',
        'thumbnail',
    ];

    protected $appends = ['thumbnail_url']; 

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    // Accessor untuk thumbnail URL
    public function getThumbnailUrlAttribute()
    {
        // Jika thumbnail kosong, berikan nilai default opsional
        if (!$this->thumbnail) {
            return url('storage/thumbnails/default.jpg'); // opsional
        }

        return url('storage/' . $this->thumbnail);
    }
}
