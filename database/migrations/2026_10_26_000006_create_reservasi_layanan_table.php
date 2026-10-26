<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reservasi_layanan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reservasi_id')->constrained('reservasi')->cascadeOnDelete();
            $table->foreignId('layanan_id')->constrained('layanan')->restrictOnDelete();
            $table->unsignedInteger('jumlah')->default(1);
            $table->timestamps();
            $table->unique(['reservasi_id', 'layanan_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservasi_layanan');
    }
};
