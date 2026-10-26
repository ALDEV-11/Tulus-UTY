<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kendaraan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('merk_motor_id')->constrained('merk_motor')->restrictOnDelete();
            $table->string('plat_nomor', 15)->unique();
            $table->string('model', 100)->nullable();
            $table->unsignedSmallInteger('tahun')->nullable();
            $table->unsignedInteger('kilometer')->default(0);
            $table->timestamps();
            $table->index(['user_id', 'merk_motor_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kendaraan');
    }
};
