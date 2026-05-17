<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reportes', function (Blueprint $table) {
            $table->foreignId('comment_id')->nullable()->constrained('comments')->onDelete('cascade')->after('post_id');
            $table->foreignId('reported_user_id')->nullable()->constrained('users')->onDelete('cascade')->after('comment_id');
        });

        // Hacer post_id nullable de forma segura
        Schema::table('reportes', function (Blueprint $table) {
            $table->unsignedBigInteger('post_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('reportes', function (Blueprint $table) {
            $table->dropForeign(['comment_id']);
            $table->dropForeign(['reported_user_id']);
            $table->dropColumn(['comment_id', 'reported_user_id']);
            $table->unsignedBigInteger('post_id')->nullable(false)->change();
        });
    }
};
