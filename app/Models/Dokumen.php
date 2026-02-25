<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use App\Enums\penerbit;
use App\Enums\tipeDok;
use App\Enums\status;

class Dokumen extends Model
{
    protected $table = 'dokumen';

    protected $guarded = [];

    protected $casts = [
        'tipeDkmn' => tipeDok::class,
        'penerbit'=> penerbit::class,
        'status' => status::class,
        'tglTerbit' => 'date',
        'tglkdlwrs' => 'date',
    ];

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class, 'mahasiswa_id');
    }
    

}
