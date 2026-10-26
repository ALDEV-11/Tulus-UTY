<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('layanan', 'interval_servis_hari')) {
            Schema::table('layanan', function (Blueprint $table) {
                $table->unsignedInteger('interval_servis_hari')->nullable()->after('durasi_menit');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('layanan', 'interval_servis_hari')) {
            Schema::table('layanan', function (Blueprint $table) {
                $table->dropColumn('interval_servis_hari');
            });
        }
    }
};
