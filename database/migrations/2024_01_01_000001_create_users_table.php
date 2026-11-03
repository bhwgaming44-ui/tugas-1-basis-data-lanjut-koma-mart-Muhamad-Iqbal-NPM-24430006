<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id('id_user');
            $table->string('username', 50)->unique();
            $table->string('password');
            $table->string('nama_lengkap', 100);
            $table->string('jabatan', 100)->default('Staf Operasional');
            $table->enum('role', ['admin', 'manajer', 'kasir', 'mitra'])->default('kasir');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
