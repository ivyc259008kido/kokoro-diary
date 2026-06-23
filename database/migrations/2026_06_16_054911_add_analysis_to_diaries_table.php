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
        Schema::table('diaries', function (Blueprint $table) {
            $table->integer('mood')->nullable();
            $table->string('summary')->nullable();
            $table->text('encouragement')->nullable();
            $table->json('themes')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('diaries', function (Blueprint $table) {
            $table->dropColumn(['mood', 'summary', 'encouragement', 'themes']);
        });
    }
};
