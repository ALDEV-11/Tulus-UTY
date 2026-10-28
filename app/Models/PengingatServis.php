<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengingatServis extends Model
{
    protected $table = 'pengingat_servis';

    protected $fillable = [
        'kendaraan_id',
        'layanan_id',
        'servis_id',
        'tanggal_berikutnya',
        'kilometer_berikutnya',
        'status',
    ];

    protected $casts = [
        'tanggal_berikutnya' => 'date',
        'kilometer_berikutnya' => 'integer',
    ];

    public function kendaraan()
    {
        return $this->belongsTo(Kendaraan::class);
    }

    public function layanan()
    {
        return $this->belongsTo(Layanan::class);
    }

    public function servis()
    {
        return $this->belongsTo(Servis::class);
    }
}
