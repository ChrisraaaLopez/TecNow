<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('communities', function (Blueprint $table) {
            $table->enum('tipo', ['carrera', 'avisos', 'general'])->default('general')->after('description');
            $table->string('slug')->unique()->nullable()->after('tipo');
        });
    }

    public function down(): void
    {
        Schema::table('communities', function (Blueprint $table) {
            $table->dropColumn(['tipo', 'slug']);
        });
    }
};
