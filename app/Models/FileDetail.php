<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FileDetail extends Model
{
    protected $table = 'fileDetail';
    protected $guarded = [];

    public function dokumen(){
        return $this->belongsTo(Dokumen::class, 'dokumen_id');
    }

    public function reqDok(){
        return $this->belongsTo(ReqDokumen::class, 'reqDokumen_id');
    }
}
