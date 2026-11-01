<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reservasi_layanan', function (Blueprint $table) {
            $table->dropForeign(['layanan_id']);
            $table->foreign('layanan_id')
                ->references('id')
                ->on('layanan')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('reservasi_layanan', function (Blueprint $table) {
            $table->dropForeign(['layanan_id']);
            $table->foreign('layanan_id')
                ->references('id')
                ->on('layanan')
                ->restrictOnDelete();
        });
    }
};
