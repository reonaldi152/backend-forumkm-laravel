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
            // $table->unsignedBigInteger('order_item_id')->nullable();
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('user_id');
            $table->integer('star_sellers');
            $table->integer('star_courier');
            $table->string('variations');
            $table->text('description');
            $table->json('attachments');
            $table->boolean('show_username');
            $table->timestamps();

            //$table->foreign('order_item_id')->references('id')->on('order_items');
            $table->foreign('product_id')->references('id')->on('products');
            $table->foreign('user_id')->references('id')->on('users');
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
