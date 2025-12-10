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
        Schema::create('restaurants', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('cuisine');
            $table->text('description');
            $table->string('address');
            $table->string('phone')->nullable();
            $table->json('hours')->nullable(); // Store opening hours as JSON
            $table->string('price_range', 10); // $, $$, $$$, $$$$
            $table->string('location');
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->string('main_image')->nullable();
            $table->json('images')->nullable(); // Store multiple images as JSON
            $table->json('features')->nullable(); // Store features as JSON array
            $table->decimal('average_rating', 3, 2)->default(0.00);
            $table->integer('review_count')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('restaurants');
    }
};
