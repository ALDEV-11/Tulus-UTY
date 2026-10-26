<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MerkMotor extends Model
{
    protected $table = 'merk_motor';
    protected $fillable = ['nama', 'aktif'];
    protected $casts = ['aktif' => 'boolean'];

    public function kendaraan() { return $this->hasMany(Kendaraan::class); }
}
