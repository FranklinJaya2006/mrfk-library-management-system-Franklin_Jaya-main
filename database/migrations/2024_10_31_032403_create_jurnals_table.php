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
        Schema::create('journals', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_pengguna');
            $table->string('author');
            $table->string('title');
            $table->text('description');
            $table->integer('thn_terbit')->default(0);
            $table->integer('jml_halaman')->default(0);
            $table->string('status')->default('pending');
            $table->timestamps();

            $table->foreign('id_pengguna')
                ->references('id_pengguna')
                ->on('users'); // Tabel penggunas adalah tabel librarian
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('journals');
    }
};
