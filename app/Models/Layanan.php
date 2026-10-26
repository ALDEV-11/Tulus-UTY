<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Layanan extends Model
{
    protected $table = 'layanan';
    protected $fillable = ['nama', 'deskripsi', 'durasi_menit', 'interval_servis_hari', 'interval_servis_km', 'aktif'];
    protected $casts = ['aktif' => 'boolean'];

    public function reservasi() { return $this->belongsToMany(Reservasi::class, 'reservasi_layanan')->withPivot('jumlah')->withTimestamps(); }
}
