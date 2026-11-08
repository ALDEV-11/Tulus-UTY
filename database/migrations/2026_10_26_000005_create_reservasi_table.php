<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reservasi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->foreignId('kendaraan_id')->constrained('kendaraan')->restrictOnDelete();
            $table->foreignId('id_slot')->constrained('slot_waktu')->restrictOnDelete();
            $table->string('kode', 30)->unique();
            $table->string('status', 30)->default('menunggu_konfirmasi');
            $table->text('keluhan')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();
            $table->index('id_slot');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservasi');
    }
};
