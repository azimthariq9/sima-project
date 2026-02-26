<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use App\Enums\penerbit;
use App\Enums\tipeDok;
use App\Enums\status;
use Illuminate\Database\Eloquent\SoftDeletes;

class Dokumen extends Model
{
    use SoftDeletes; // TAMBAHKAN INI
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
    public function fileDetail(){
        return $this->hasMany(FileDetail::class, 'dokumen_id');
    }
    public function history(){
        return $this->hasMany(HistoryDokumen::class,'dokumen_id');
    }
    
        /**
     * Ambil history terbaru
     */
    public function latestHistory()
    {
        return $this->hasOne(HistoryDokumen::class, 'dokumen_id')->latestOfMany();
    }

}
