<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\CategorySection;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->enum('section', CategorySection::values());
            $table->string('id_slug')->unique();
            $table->text('description')->nullable();
            $table->string('icon')->nullable();
            $table->string('brand')->nullable();
            $table->string('help')->nullable();
            $table->enum('next', CategorySection::values())->nullable();
            // 'parent' enum removed; we now rely on parent_id to relate categories.
            $table->foreignId('parent_id')->nullable()->constrained('categories')->onDelete('cascade');
            $table->json('product')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
