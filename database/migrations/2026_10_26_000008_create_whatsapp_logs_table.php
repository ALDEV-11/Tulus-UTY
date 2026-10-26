<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('whatsapp_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('reservasi_id')->nullable()->constrained('reservasi')->nullOnDelete();
            $table->string('jenis', 30);
            $table->string('nomor_tujuan', 20);
            $table->text('pesan');
            $table->string('status', 20)->default('menunggu')->index();
            $table->timestamp('terkirim_pada')->nullable();
            $table->text('respons')->nullable();
            $table->timestamps();
            $table->index(['jenis', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('whatsapp_logs');
    }
};
