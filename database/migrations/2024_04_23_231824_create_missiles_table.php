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
        Schema::create('missiles', function (Blueprint $table) {
            $table->id();
            $table->string('position',5);
            $table->foreignId('partie_id');
            $table->integer('resultat')->nullable(); // 1 a 6 voir README prof
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('missiles');
    }
};
