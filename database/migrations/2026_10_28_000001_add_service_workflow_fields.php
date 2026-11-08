<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('servis', function (Blueprint $table) {
            $table->unsignedInteger('odometer_awal')->nullable()->after('tanggal_masuk');
            $table->unsignedInteger('odometer')->nullable()->after('odometer_awal');
            $table->dateTime('waktu_mulai')->nullable()->after('status');
            $table->dateTime('waktu_selesai')->nullable()->after('waktu_mulai');
            $table->text('rincian_pekerjaan')->nullable()->after('waktu_selesai');
            $table->text('catatan')->nullable()->after('rincian_pekerjaan');
            $table->string('status', 25)->default('menunggu')->change();
        });

        Schema::table('kendaraan', function (Blueprint $table) {
            $table->unsignedInteger('odometer_terakhir')->default(0)->after('kilometer');
        });

        DB::table('kendaraan')->update([
            'odometer_terakhir' => DB::raw('kilometer'),
        ]);

        DB::table('servis')
            ->where('status', 'dijadwalkan')
            ->update(['status' => 'menunggu']);

        DB::table('servis')
            ->where('status', 'dibatalkan')
            ->update(['status' => 'diambil']);

        DB::table('servis')
            ->whereNull('rincian_pekerjaan')
            ->whereNotNull('tindakan')
            ->update(['rincian_pekerjaan' => DB::raw('tindakan')]);

        Schema::create('pengingat_servis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kendaraan_id')->constrained('kendaraan')->cascadeOnDelete();
            $table->foreignId('layanan_id')->nullable()->constrained('layanan')->nullOnDelete();
            $table->foreignId('servis_id')->nullable()->constrained('servis')->nullOnDelete();
            $table->date('tanggal_berikutnya')->nullable();
            $table->unsignedInteger('kilometer_berikutnya')->nullable();
            $table->string('status', 20)->default('menunggu')->index();
            $table->timestamps();

            $table->unique(['servis_id', 'layanan_id']);
            $table->index(['kendaraan_id', 'tanggal_berikutnya']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengingat_servis');

        Schema::table('kendaraan', function (Blueprint $table) {
            $table->dropColumn('odometer_terakhir');
        });

        Schema::table('servis', function (Blueprint $table) {
            $table->dropColumn([
                'odometer_awal',
                'odometer',
                'waktu_mulai',
                'waktu_selesai',
                'rincian_pekerjaan',
                'catatan',
            ]);
        });
    }
};
