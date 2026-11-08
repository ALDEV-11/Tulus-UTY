<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('layanan', 'harga')) {
            Schema::table('layanan', function (Blueprint $table) {
                $table->dropColumn('harga');
            });
        }

        if (Schema::hasColumn('reservasi_layanan', 'harga')) {
            Schema::table('reservasi_layanan', function (Blueprint $table) {
                $table->dropColumn('harga');
            });
        }
    }

    public function down(): void
    {
        Schema::table('layanan', function (Blueprint $table) {
            $table->decimal('harga', 12, 2)->default(0);
        });

        Schema::table('reservasi_layanan', function (Blueprint $table) {
            $table->decimal('harga', 12, 2)->default(0);
        });
    }
};
