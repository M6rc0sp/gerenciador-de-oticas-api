<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stores', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('nuvemshop_id')->unique();
            $table->text('access_token');
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('domain')->nullable();
            $table->string('original_domain')->nullable();
            $table->string('plan')->nullable();
            $table->string('country', 10)->default('BR');
            $table->string('currency', 10)->default('BRL');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('nuvemshop_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stores');
    }
};
