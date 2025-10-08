<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('tours', function (Blueprint $table) {
            if (!Schema::hasColumn('tours', 'categoryID')) $table->unsignedBigInteger('categoryID')->nullable()->after('tourID');
            if (!Schema::hasColumn('tours', 'title')) $table->string('title', 200)->nullable()->after('tourName');
            if (!Schema::hasColumn('tours', 'quantity')) $table->integer('quantity')->default(0)->after('description');
            if (!Schema::hasColumn('tours', 'priceAdult')) $table->double('priceAdult')->default(0)->after('quantity');
            if (!Schema::hasColumn('tours', 'priceChild')) $table->double('priceChild')->nullable()->after('priceAdult');
            if (!Schema::hasColumn('tours', 'availability')) $table->boolean('availability')->default(true)->after('priceChild');
            if (!Schema::hasColumn('tours', 'startDate')) $table->date('startDate')->nullable()->after('availability');
            if (!Schema::hasColumn('tours', 'endDate')) $table->date('endDate')->nullable()->after('startDate');
            if (!Schema::hasColumn('tours', 'pickupPoint')) $table->string('pickupPoint', 255)->nullable()->after('endDate');
            if (!Schema::hasColumn('tours', 'departurePoint')) $table->string('departurePoint', 255)->nullable()->after('pickupPoint');
            if (!Schema::hasColumn('tours', 'destinationPoint')) $table->string('destinationPoint', 255)->nullable()->after('departurePoint');

            // Foreign key (optional if Category table uses different naming)
            if (!Schema::hasColumn('tours', 'categoryID')) return; // safeguard
            try {
                $table->foreign('categoryID')->references('categoryID')->on('Category')->nullOnDelete()->cascadeOnUpdate();
            } catch (\Throwable $e) { /* ignore if already exists */
            }
        });
    }

    public function down(): void
    {
        Schema::table('tours', function (Blueprint $table) {
            // Drop added columns (only if they exist)
            $cols = ['categoryID', 'title', 'quantity', 'priceAdult', 'priceChild', 'availability', 'startDate', 'endDate', 'pickupPoint', 'departurePoint', 'destinationPoint'];
            foreach ($cols as $c) {
                if (Schema::hasColumn('tours', $c)) {
                    if ($c === 'categoryID') {
                        try {
                            $table->dropForeign(['categoryID']);
                        } catch (\Throwable $e) {
                        }
                    }
                    $table->dropColumn($c);
                }
            }
        });
    }
};
