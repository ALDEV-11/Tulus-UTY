<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Layanan extends Model
{
    protected $table = 'layanan';
    protected $fillable = ['nama', 'deskripsi', 'durasi_menit', 'harga', 'interval_servis_km', 'aktif'];
    protected $casts = ['harga' => 'decimal:2', 'aktif' => 'boolean'];

    public function reservasi() { return $this->belongsToMany(Reservasi::class, 'reservasi_layanan')->withPivot(['jumlah', 'harga'])->withTimestamps(); }
}
