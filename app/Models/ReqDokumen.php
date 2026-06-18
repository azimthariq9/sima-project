<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Enums\tipeDok;
use App\Enums\status;

class ReqDokumen extends Model
{
    protected $table = 'reqDokumen';
    protected $guarded = [];

    protected $casts = [
        'tipeDkmn' => tipeDok::class,
        'status' => status::class
    ];

    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }
    public function mahasiswa(){
        return $this->belongsTo(mahasiswa::class, 'mahasiswa_id');
    }
    public function fileDetail(){
        return $this->hasMany(fileDetail::class, 'reqDokumen_id');
    }
}

