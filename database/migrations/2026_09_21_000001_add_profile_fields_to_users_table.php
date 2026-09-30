<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['mahasiswa', 'dosen', 'admin'])->default('mahasiswa')->after('email');
            $table->string('nim', 30)->nullable()->unique()->after('role');
            $table->string('program_studi', 100)->nullable()->after('nim');
            $table->string('angkatan', 10)->nullable()->after('program_studi');
            $table->string('phone', 20)->nullable()->after('angkatan');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['nim']);
            $table->dropColumn(['role', 'nim', 'program_studi', 'angkatan', 'phone']);
        });
    }
};
