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
        Schema::table('reviews', function (Blueprint $table) {
            $table->text('content')->after('title');
        });
        
        // Copy data from comment to content
        \DB::statement('UPDATE reviews SET content = comment WHERE content IS NULL');
        
        Schema::table('reviews', function (Blueprint $table) {
            $table->dropColumn('comment');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->text('comment')->after('title');
        });
        
        // Copy data from content to comment
        \DB::statement('UPDATE reviews SET comment = content WHERE comment IS NULL');
        
        Schema::table('reviews', function (Blueprint $table) {
            $table->dropColumn('content');
        });
    }
};
