<?php

namespace App\Models;

use App\Enums\StatusReservasi;
use Illuminate\Database\Eloquent\Model;

class Reservasi extends Model
{
    protected $table = 'reservasi';
    protected $fillable = ['user_id', 'kendaraan_id', 'id_slot', 'kode', 'status', 'keluhan', 'catatan'];
    protected $casts = ['status' => StatusReservasi::class];

    public function pelanggan() { return $this->belongsTo(User::class, 'user_id'); }
    public function kendaraan() { return $this->belongsTo(Kendaraan::class); }
    public function slot() { return $this->belongsTo(SlotWaktu::class, 'id_slot'); }
    public function layanan() { return $this->belongsToMany(Layanan::class, 'reservasi_layanan')->withPivot(['jumlah', 'harga'])->withTimestamps(); }
    public function servis() { return $this->hasOne(Servis::class); }
    public function whatsappLogs() { return $this->hasMany(WhatsappLog::class); }
}
