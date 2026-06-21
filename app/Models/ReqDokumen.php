<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Enums\TipeDok;
use App\Enums\Status;
use App\Models\Mahasiswa;
use App\Models\FileDetail;

class ReqDokumen extends Model
{
    protected $table = 'reqDokumen';
    protected $guarded = [];

    protected $casts = [
        'tipeDkmn' => TipeDok::class,
        'status' => Status::class
    ];

    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }
    public function mahasiswa(){
        return $this->belongsTo(Mahasiswa::class, 'mahasiswa_id');
    }
    public function fileDetail(){
        return $this->hasMany(FileDetail::class, 'reqDokumen_id');
    }
}

