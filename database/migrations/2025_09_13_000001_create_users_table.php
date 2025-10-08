<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('Users', function (Blueprint $table) {
            $table->increments('userID');
            $table->string('userName', 50)->unique();
            $table->string('password', 100);
            $table->string('email', 100)->unique();
            $table->string('phoneNumber', 30)->nullable();
            $table->string('address', 255)->nullable();
            $table->enum('gender', ['Male', 'Female', 'Other'])->nullable();
            $table->enum('role', ['admin', 'staff', 'customer'])->default('customer');
            $table->enum('status', ['Active', 'Inactive'])->default('Active');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('Users');
    }
};
