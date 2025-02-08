<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->default(DB::raw('(UUID())')); // Hanya tersedia di PHP 8
            $table->unsignedBigInteger('user_id');
            $table->boolean('is_default');
            $table->string('receiver_name')->nullable();
            $table->string('receiver_phone')->nullable();
            $table->unsignedBigInteger('city_id');
            $table->string('district')->nullable();
            $table->string('postal_code', 10)->nullable();
            $table->text('detail_address')->nullable();
            $table->string('address_note')->nullable();
            $table->string('type')->nullable()->comment('home | office');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('city_id')->references('id')->on('cities');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
