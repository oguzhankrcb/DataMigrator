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
        Schema::create('model_b_s', function (Blueprint $table) {
            $table->id();
            $table->string('new_key')->nullable();
            $table->string('name');
            $table->string('category');
            $table->string('alias');
            $table->integer('item_code');
            $table->integer('vat');
            $table->boolean('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('model_b');
    }
};
