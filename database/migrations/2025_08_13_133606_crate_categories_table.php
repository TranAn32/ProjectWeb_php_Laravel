<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('categories', function (Blueprint $table) {
            $table->increments('categoryID');
            $table->string('categoryName', 100);
            $table->text('description')->nullable();
            $table->enum('type', ['domestic', 'international'])->default('domestic');
            $table->string('imageURL', 255)->nullable();
            $table->string('slug', 150)->unique()->nullable();
            $table->enum('status', ['Active', 'Inactive'])->default('Active');
            $table->integer('sort_order')->default(0);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
        });
    }

    public function down(): void {
        Schema::dropIfExists('categories');
    }
};