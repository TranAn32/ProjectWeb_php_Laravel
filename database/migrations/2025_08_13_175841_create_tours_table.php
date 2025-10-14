<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tours', function (Blueprint $table) {
            $table->increments('tourID');
            $table->unsignedInteger('categoryID')->nullable();

            $table->string('title', 200);
            $table->text('description')->nullable();

            $table->json('images')->nullable();
            $table->json('prices')->nullable();
            $table->json('itinerary')->nullable();

            $table->string('pickupPoint', 255)->nullable();
            $table->string('departurePoint', 255)->nullable();

            $table->json('hotels')->nullable();
            $table->enum('status', ['draft', 'published', 'canceled'])->default('draft');

            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->foreign('categoryID')
                ->references('categoryID')->on('categories')
                ->onUpdate('cascade')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tours');
    }
};
