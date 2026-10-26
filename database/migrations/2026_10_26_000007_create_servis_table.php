<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('servis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reservasi_id')->nullable()->constrained('reservasi')->nullOnDelete();
            $table->foreignId('kendaraan_id')->constrained('kendaraan')->restrictOnDelete();
            $table->foreignId('mekanik_id')->nullable()->constrained('users')->nullOnDelete();
            $table->date('tanggal_masuk');
            $table->date('tanggal_selesai')->nullable();
            $table->string('status', 25)->default('dijadwalkan')->index();
            $table->text('diagnosa')->nullable();
            $table->text('tindakan')->nullable();
            $table->decimal('total_biaya', 12, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('servis');
    }
};
