<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->unsignedBigInteger('shared_from_post_id')->nullable()->after('fijada');
            $table->foreign('shared_from_post_id')
                  ->references('id')->on('posts')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropForeign(['shared_from_post_id']);
            $table->dropColumn('shared_from_post_id');
        });
    }
};
