<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kendaraan extends Model
{
    protected $table = 'kendaraan';
    protected $fillable = ['user_id', 'merk_motor_id', 'plat_nomor', 'model', 'tahun', 'kilometer'];
    protected $casts = ['tahun' => 'integer', 'kilometer' => 'integer'];

    public function pemilik() { return $this->belongsTo(User::class, 'user_id'); }
    public function merk() { return $this->belongsTo(MerkMotor::class, 'merk_motor_id'); }
    public function reservasi() { return $this->hasMany(Reservasi::class); }
    public function servis() { return $this->hasMany(Servis::class); }
}
