<?php

namespace App\Models;

use App\Enums\JenisWhatsapp;
use Illuminate\Database\Eloquent\Model;

class WhatsappLog extends Model
{
    protected $table = 'whatsapp_logs';
    protected $fillable = ['user_id', 'reservasi_id', 'jenis', 'nomor_tujuan', 'pesan', 'status', 'terkirim_pada', 'respons'];
    protected $casts = ['jenis' => JenisWhatsapp::class, 'terkirim_pada' => 'datetime'];

    public function user() { return $this->belongsTo(User::class); }
    public function reservasi() { return $this->belongsTo(Reservasi::class); }
}
