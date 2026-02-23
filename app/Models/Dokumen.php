<?php

namespace App\Models;

use App\status;
use Illuminate\Database\Eloquent\Model;

class Dokumen extends Model
{
    protected $table = 'dokumen';

    protected $guarded = [];

    protected $casts = [
        'tipeDkmn' => 'string',
        'penerbit'=> 'string',
        'status' => 'string',
        'tglTerbit' => 'date',
        'tglkdlwrs' => 'date',
    ];

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class, 'mahasiswa_id');
    }

}
