<?php

namespace App\Models;

use App\Enums\StatusServis;
use Illuminate\Database\Eloquent\Model;

class Servis extends Model
{
    protected $table = 'servis';
    protected $fillable = ['reservasi_id', 'kendaraan_id', 'mekanik_id', 'tanggal_masuk', 'tanggal_selesai', 'status', 'diagnosa', 'tindakan', 'total_biaya'];
    protected $casts = ['tanggal_masuk' => 'date', 'tanggal_selesai' => 'date', 'status' => StatusServis::class, 'total_biaya' => 'decimal:2'];

    public function reservasi() { return $this->belongsTo(Reservasi::class); }
    public function kendaraan() { return $this->belongsTo(Kendaraan::class); }
    public function mekanik() { return $this->belongsTo(User::class, 'mekanik_id'); }
}
