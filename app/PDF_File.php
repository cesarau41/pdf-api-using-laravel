<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PDF_File extends Model
{
    protected $table = 'pdf_files';

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
