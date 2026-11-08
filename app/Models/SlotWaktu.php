<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SlotWaktu extends Model
{
    protected $table = 'slot_waktu';
    protected $fillable = ['tanggal', 'jam_mulai', 'jam_selesai', 'kapasitas', 'aktif'];
    protected $casts = ['tanggal' => 'date', 'aktif' => 'boolean'];

    public function reservasi() { return $this->hasMany(Reservasi::class, 'id_slot'); }
}
