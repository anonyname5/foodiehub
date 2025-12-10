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
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('restaurant_id')->constrained()->onDelete('cascade');
            $table->decimal('overall_rating', 3, 2);
            $table->decimal('food_rating', 3, 2)->nullable();
            $table->decimal('service_rating', 3, 2)->nullable();
            $table->decimal('ambiance_rating', 3, 2)->nullable();
            $table->decimal('value_rating', 3, 2)->nullable();
            $table->string('title')->nullable();
            $table->text('comment');
            $table->date('visit_date')->nullable();
            $table->json('photos')->nullable(); // Store photo paths as JSON array
            $table->boolean('recommend')->nullable(); // true/false/null
            $table->integer('helpful_count')->default(0);
            $table->boolean('is_verified')->default(false);
            $table->timestamps();

            // Indexes for better performance
            $table->index(['restaurant_id', 'created_at']);
            $table->index(['user_id', 'created_at']);
            $table->index('overall_rating');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
