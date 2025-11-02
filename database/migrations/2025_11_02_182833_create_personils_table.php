<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('personils', function (Blueprint $table) {
            $table->id();
            $table->string('nama')->nullable();
            $table->string('pangkat')->nullable();
            $table->string('nrp')->nullable();

            // Jabatan terakhir — nanti diambil dari riwayat_jabatan
            $table->string('jabatan')->nullable();
            $table->date('tmt')->nullable();

            // Identitas pribadi
            $table->string('tempat_lahir')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->string('golongan_darah')->nullable(); // opsi golongan darah
            $table->string('agama')->nullable(); // opsi agama
            $table->string('suku')->nullable(); // opsi suku

            // Alamat dan relasi wilayah
            $table->string('alamat')->nullable();
            $table->bigInteger('province_id')->nullable()->index();
            $table->bigInteger('city_id')->nullable()->index();
            $table->bigInteger('district_id')->nullable()->index();
            $table->bigInteger('subdistrict_id')->nullable()->index();

            // Kontak dan administratif
            $table->string('telp')->nullable();
            $table->string('bpjs')->nullable(); // file kartu bpjs

            // JSON fields
            $table->json('pasangan')->nullable(); // json istri/suami
            $table->json('keluarga')->nullable(); // json anak nama & ttl
            $table->json('pendidikan_polri')->nullable(); // json pendidikan polri : tingkat, tahun dan dokumen pendukung
            $table->json('pendidikan_umum')->nullable(); // json pendidikan umum : tingkat, nama institusi, tahun dan dokumen pendukung
            $table->json('riwayat_pangkat')->nullable(); // json riwayat pangkat : pangkat, tmt dan dokumen pendukung
            $table->json('riwayat_jabatan')->nullable(); // json riwayat jabatan : jabatan, tmt dan dokumen pendukung
            $table->json('dikbang_pelatihan')->nullable(); // json dikbang pelatihan : nama pelatihan, tmt dan dokumen pendukung
            $table->json('tanda_kehormatan')->nullable(); // json tanda kehormatan : nama tanda, tmt dan dokumen pendukung
            $table->json('kemampuan_bahasa')->nullable(); // json kemampuan bahasa : bahasa, status dan dokumen pendukung
            $table->json('penugasan_ln')->nullable(); // json penugasan ln : jabatan, tmt dan dokumen pendukung

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('personils');
    }
};
