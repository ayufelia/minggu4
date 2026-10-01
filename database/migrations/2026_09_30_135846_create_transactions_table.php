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
        Schema::create('transaction_details', function (Blueprint $table) {

            $table->id();

            $table->unsignedBigInteger('transaction_id');

            $table->unsignedBigInteger('product_id');

            $table->string('product_code');

            $table->string('product_name');

            $table->decimal('price', 15, 2)->default(0);

            $table->integer('qty')->default(1);

            $table->decimal('discount', 15, 2)->default(0);

            $table->decimal('subtotal', 15, 2)->default(0);

            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaction_details');
    }
};