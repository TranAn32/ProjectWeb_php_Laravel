<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('bookings', function (Blueprint $table) {
            $table->increments('bookingID');
            $table->unsignedInteger('tourID');
            $table->unsignedInteger('userID');

            $table->timestamp('bookingDate')->useCurrent();
            $table->date('departureDate');

            $table->integer('numAdults');
            $table->integer('numChildren')->default(0);

            $table->decimal('totalPrice', 12, 2)->nullable();
            $table->enum('status', ['Pending', 'Confirmed', 'Cancelled'])->default('Pending');
            $table->enum('paymentStatus', ['Unpaid', 'Paid', 'Refunded'])->default('Unpaid');

            $table->text('specialRequest')->nullable();

            $table->foreign('tourID')
                ->references('tourID')->on('tours')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('userID')
                ->references('userID')->on('users')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });
    }

    public function down(): void {
        Schema::dropIfExists('bookings');
    }
};